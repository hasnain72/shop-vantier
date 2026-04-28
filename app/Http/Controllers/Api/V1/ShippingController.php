<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShippingRateResource;
use App\Models\ProductVariant;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(private ShippingService $shippingService) {}

    public function rates(Request $request)
    {
        return $this->resolveRates($request);
    }

    public function estimate(Request $request)
    {
        return $this->resolveRates($request);
    }

    private function resolveRates(Request $request)
    {
        $data = $request->validate([
            'shipping_address'              => 'required|array',
            'shipping_address.country_code' => 'required|string|size:2',
            'shipping_address.province_code'=> 'nullable|string',
            'line_items'                    => 'required|array|min:1',
            'line_items.*.variant_id'       => 'required|integer',
            'line_items.*.quantity'         => 'required|integer|min:1',
        ]);

        [$subtotal, $totalWeight] = $this->calculateTotals($data['line_items']);

        $rates = $this->shippingService->getAvailableRates(
            $data['shipping_address'],
            $data['line_items'],
            $subtotal,
            $totalWeight
        );

        return response()->json(['shipping_rates' => $rates]);
    }

    private function calculateTotals(array $lineItems): array
    {
        $subtotal    = 0.0;
        $totalWeight = 0.0;

        $variantIds = array_column($lineItems, 'variant_id');
        $variants   = ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

        foreach ($lineItems as $item) {
            $variant = $variants->get($item['variant_id']);
            if (!$variant) continue;
            $qty          = (int) $item['quantity'];
            $subtotal    += (float) $variant->price * $qty;
            $totalWeight += (float) ($variant->weight ?? 0) * $qty;
        }

        return [$subtotal, $totalWeight];
    }
}
