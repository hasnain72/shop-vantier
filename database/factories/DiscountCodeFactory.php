<?php

namespace Database\Factories;

use App\Models\PriceRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountCodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'price_rule_id' => PriceRule::factory(),
            'code'          => strtoupper(fake()->lexify('SAVE????')),
            'usage_count'   => 0,
        ];
    }
}
