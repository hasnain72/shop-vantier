<?php

namespace Database\Seeders;

use App\Models\InventoryAdjustment;
use App\Models\InventoryItem;
use App\Models\InventoryLevel;
use App\Models\InventoryLocation;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // ── Locations ────────────────────────────────────────────────────────────
        $karachi = InventoryLocation::firstOrCreate(['name' => 'Karachi Warehouse'], [
            'address1'              => 'Plot 12, SITE Area, Sector 10',
            'city'                  => 'Karachi',
            'province'              => 'Sindh',
            'country'               => 'Pakistan',
            'zip'                   => '75730',
            'phone'                 => '+92-21-34523000',
            'is_active'             => true,
            'fulfills_online_orders'=> true,
        ]);

        $lahore = InventoryLocation::firstOrCreate(['name' => 'Lahore Showroom'], [
            'address1'              => '72-B, MM Alam Road, Gulberg III',
            'city'                  => 'Lahore',
            'province'              => 'Punjab',
            'country'               => 'Pakistan',
            'zip'                   => '54660',
            'phone'                 => '+92-42-35761200',
            'is_active'             => true,
            'fulfills_online_orders'=> false,
        ]);

        // ── Build inventory for every variant ───────────────────────────────────
        $variants = ProductVariant::all();

        foreach ($variants as $variant) {
            // InventoryItem (one per variant)
            $item = InventoryItem::firstOrCreate(
                ['variant_id' => $variant->id],
                [
                    'sku'              => $variant->sku,
                    'cost'             => $variant->cost_per_item ?? 0,
                    'tracked'          => true,
                    'requires_shipping'=> true,
                ]
            );

            // Stock split: ~60% Karachi, ~40% Lahore
            $total    = max($variant->inventory_quantity, 0);
            $kqty     = (int) round($total * 0.6);
            $lqty     = $total - $kqty;

            // Use withoutEvents to skip Observer during seeding
            InventoryLevel::withoutEvents(function () use ($item, $karachi, $lahore, $kqty, $lqty) {
                InventoryLevel::firstOrCreate(
                    ['inventory_item_id' => $item->id, 'location_id' => $karachi->id],
                    ['available' => $kqty, 'incoming' => 0, 'committed' => 0]
                );
                InventoryLevel::firstOrCreate(
                    ['inventory_item_id' => $item->id, 'location_id' => $lahore->id],
                    ['available' => $lqty, 'incoming' => 0, 'committed' => 0]
                );
            });

            // Seed adjustment record for initial stock
            if ($kqty > 0) {
                InventoryAdjustment::firstOrCreate(
                    [
                        'inventory_item_id' => $item->id,
                        'location_id'       => $karachi->id,
                        'reason'            => 'received',
                        'available_before'  => 0,
                    ],
                    [
                        'adjustment'       => $kqty,
                        'available_after'  => $kqty,
                        'user_id'          => null,
                    ]
                );
            }
        }
    }
}
