<?php

namespace App\Services;

use App\Services\Shipping\ShippingProviderInterface;
use App\Services\Shipping\SmsaExpressProvider;
use InvalidArgumentException;

/**
 * Registry for real-world shipping providers (SMSA, DHL, etc.). Kept separate
 * from ShippingService — which is the storefront zone/rate calculator — because
 * that's cart-facing while this one is fulfillment-facing.
 */
class ShippingProviderService
{
    public function get(string $name): ShippingProviderInterface
    {
        return match ($name) {
            'smsa'  => new SmsaExpressProvider(),
            default => throw new InvalidArgumentException("Unknown shipping provider: {$name}"),
        };
    }
}
