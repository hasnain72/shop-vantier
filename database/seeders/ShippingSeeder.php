<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    public function run(): void
    {
        // ── Zone 1: Pakistan ─────────────────────────────────────────────────────
        $pakistan = ShippingZone::firstOrCreate(['name' => 'Pakistan'], [
            'countries' => ['PK'],
        ]);

        if ($pakistan->wasRecentlyCreated) {
            $pakistan->rates()->createMany([
                [
                    'name'      => 'Standard Delivery',
                    'rate_type' => 'flat',
                    'price'     => 3.00,
                    'is_active' => true,
                ],
                [
                    'name'      => 'Express Delivery',
                    'rate_type' => 'flat',
                    'price'     => 7.00,
                    'is_active' => true,
                ],
                [
                    'name'                 => 'Free Shipping (orders over $50)',
                    'rate_type'            => 'price_based',
                    'price'                => 0.00,
                    'min_order_subtotal'   => 50.00,
                    'max_order_subtotal'   => null,
                    'is_active'            => true,
                ],
                [
                    'name'      => 'Cash on Delivery',
                    'rate_type' => 'flat',
                    'price'     => 2.00,
                    'is_active' => true,
                ],
            ]);
        }

        // ── Zone 2: GCC Countries ────────────────────────────────────────────────
        $gcc = ShippingZone::firstOrCreate(['name' => 'GCC'], [
            'countries' => ['AE', 'SA', 'KW', 'QA', 'BH', 'OM'],
        ]);

        if ($gcc->wasRecentlyCreated) {
            $gcc->rates()->createMany([
                [
                    'name'      => 'Standard International',
                    'rate_type' => 'flat',
                    'price'     => 12.00,
                    'is_active' => true,
                ],
                [
                    'name'      => 'Express International',
                    'rate_type' => 'flat',
                    'price'     => 22.00,
                    'is_active' => true,
                ],
                [
                    'name'               => 'Free Shipping (orders over $100)',
                    'rate_type'          => 'price_based',
                    'price'              => 0.00,
                    'min_order_subtotal' => 100.00,
                    'is_active'          => true,
                ],
            ]);
        }

        // ── Zone 3: Rest of World ────────────────────────────────────────────────
        $row = ShippingZone::firstOrCreate(['name' => 'Rest of World'], [
            'countries' => ['US', 'GB', 'CA', 'AU', 'DE', 'FR', 'NL', 'JP', 'SG'],
        ]);

        if ($row->wasRecentlyCreated) {
            $row->rates()->createMany([
                [
                    'name'      => 'Standard International',
                    'rate_type' => 'flat',
                    'price'     => 15.00,
                    'is_active' => true,
                ],
                [
                    'name'      => 'Express International (DHL)',
                    'rate_type' => 'flat',
                    'price'     => 28.00,
                    'is_active' => true,
                ],
                [
                    'name'               => 'Free Shipping (orders over $150)',
                    'rate_type'          => 'price_based',
                    'price'              => 0.00,
                    'min_order_subtotal' => 150.00,
                    'is_active'          => true,
                ],
                [
                    'name'       => 'Heavy Parcel Rate',
                    'rate_type'  => 'weight_based',
                    'price'      => 35.00,
                    'min_weight' => 1.0,
                    'is_active'  => true,
                ],
            ]);
        }
    }
}
