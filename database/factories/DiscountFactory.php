<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'           => strtoupper(fake()->lexify('SAVE????')),
            'type'           => 'percentage',
            'value'          => 10,
            'applies_to'     => 'all',
            'min_order_amount' => null,
            'usage_limit'    => null,
            'used_count'     => 0,
            'is_active'      => true,
            'starts_at'      => now()->subDay(),
            'ends_at'        => null,
        ];
    }

    public function fixedAmount(): static
    {
        return $this->state(['type' => 'fixed_amount', 'value' => 5]);
    }

    public function expired(): static
    {
        return $this->state(['ends_at' => now()->subDay()]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
