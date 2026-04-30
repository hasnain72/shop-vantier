<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PriceRuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'                => fake()->words(3, true),
            'target_type'          => 'line_item',
            'target_selection'     => 'all',
            'allocation_method'    => 'across',
            'value_type'           => 'percentage',
            'value'                => 10.00,
            'customer_selection'   => 'all',
            'once_per_customer'    => false,
            'usage_limit'          => null,
            'usage_count'          => 0,
            'starts_at'            => now()->subDay(),
            'ends_at'              => null,
        ];
    }

    public function fixedAmount(float $amount = 5.00): static
    {
        return $this->state(['value_type' => 'fixed_amount', 'value' => $amount]);
    }

    public function expired(): static
    {
        return $this->state(['ends_at' => now()->subDay()]);
    }
}
