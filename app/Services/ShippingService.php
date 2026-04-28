<?php

namespace App\Services;

use App\Models\ShippingRate;
use App\Models\ShippingZone;

class ShippingService
{
    public function getAvailableRates(
        array $shippingAddress,
        array $lineItems,
        float $orderSubtotal,
        float $totalWeight
    ): array {
        $countryCode = strtoupper($shippingAddress['country_code'] ?? '');

        $matchingZones = ShippingZone::with('rates')->get()->filter(function ($zone) use ($countryCode) {
            return in_array($countryCode, array_map('strtoupper', $zone->countries ?? []));
        });

        $available = [];

        foreach ($matchingZones as $zone) {
            foreach ($zone->rates()->active()->get() as $rate) {
                if ($this->rateMatches($rate, $orderSubtotal, $totalWeight)) {
                    $available[] = [
                        'id'        => $rate->id,
                        'name'      => $rate->name,
                        'price'     => (float) $rate->price,
                        'zone_name' => $zone->name,
                        'rate_type' => $rate->rate_type,
                    ];
                }
            }
        }

        return $available;
    }

    private function rateMatches(ShippingRate $rate, float $subtotal, float $weight): bool
    {
        return match ($rate->rate_type) {
            'flat', 'free' => true,
            'price_based'  => $this->inRange($subtotal, $rate->min_order_subtotal, $rate->max_order_subtotal),
            'weight_based' => $this->inRange($weight, $rate->min_weight, $rate->max_weight),
            default        => false,
        };
    }

    private function inRange(float $value, ?float $min, ?float $max): bool
    {
        if ($min !== null && $value < $min) return false;
        if ($max !== null && $value > $max) return false;
        return true;
    }

    public function calculateShipping(int $rateId, float $subtotal): float
    {
        $rate = ShippingRate::findOrFail($rateId);

        if ($rate->rate_type === 'free') {
            return 0.0;
        }

        return (float) $rate->price;
    }
}
