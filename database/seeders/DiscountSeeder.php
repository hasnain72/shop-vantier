<?php

namespace Database\Seeders;

use App\Models\DiscountCode;
use App\Models\PriceRule;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        // ── Price Rule 1: 20% off sitewide ───────────────────────────────────────
        $rule1 = PriceRule::firstOrCreate(['title' => 'Summer Sale 20% Off'], [
            'value_type'        => 'percentage',
            'value'             => -20,
            'target_type'       => 'line_item',
            'target_selection'  => 'all',
            'allocation_method' => 'across',
            'customer_selection'=> 'all',
            'starts_at'         => now()->subDays(5),
            'ends_at'           => now()->addDays(25),
            'usage_limit'       => 500,
            'usage_count'       => 12,
            'once_per_customer' => false,
        ]);

        if ($rule1->wasRecentlyCreated) {
            DiscountCode::create([
                'price_rule_id' => $rule1->id,
                'code'          => 'SUMMER20',
                'usage_count'   => 12,
            ]);
        }

        // ── Price Rule 2: Fixed $10 off orders over $80 ──────────────────────────
        $rule2 = PriceRule::firstOrCreate(['title' => '$10 Off Over $80'], [
            'value_type'                  => 'fixed_amount',
            'value'                       => -10,
            'target_type'                 => 'line_item',
            'target_selection'            => 'all',
            'allocation_method'           => 'across',
            'customer_selection'          => 'all',
            'prerequisite_subtotal_range' => ['greater_than_or_equal_to' => '80.00'],
            'starts_at'                   => now()->subDays(30),
            'usage_count'                 => 7,
            'once_per_customer'           => true,
        ]);

        if ($rule2->wasRecentlyCreated) {
            DiscountCode::create([
                'price_rule_id' => $rule2->id,
                'code'          => 'SAVE10',
                'usage_count'   => 7,
            ]);
        }

        // ── Price Rule 3: Free shipping code ─────────────────────────────────────
        $rule3 = PriceRule::firstOrCreate(['title' => 'Free Shipping'], [
            'value_type'        => 'percentage',
            'value'             => -100,
            'target_type'       => 'shipping_line',
            'target_selection'  => 'all',
            'allocation_method' => 'across',
            'customer_selection'=> 'all',
            'starts_at'         => now()->subDays(10),
            'ends_at'           => now()->addDays(50),
            'usage_limit'       => 200,
            'usage_count'       => 3,
            'once_per_customer' => false,
        ]);

        if ($rule3->wasRecentlyCreated) {
            DiscountCode::create([
                'price_rule_id' => $rule3->id,
                'code'          => 'FREESHIP',
                'usage_count'   => 3,
            ]);
        }

        // ── Price Rule 4: VIP 30% off (single use per customer) ──────────────────
        $rule4 = PriceRule::firstOrCreate(['title' => 'VIP Exclusive 30% Off'], [
            'value_type'        => 'percentage',
            'value'             => -30,
            'target_type'       => 'line_item',
            'target_selection'  => 'all',
            'allocation_method' => 'across',
            'customer_selection'=> 'prerequisite',
            'starts_at'         => now()->subDays(1),
            'usage_count'       => 1,
            'once_per_customer' => true,
        ]);

        if ($rule4->wasRecentlyCreated) {
            DiscountCode::create([
                'price_rule_id' => $rule4->id,
                'code'          => 'VIP30',
                'usage_count'   => 1,
            ]);
        }
    }
}
