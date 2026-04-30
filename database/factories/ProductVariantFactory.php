<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id'          => Product::factory(),
            'title'               => 'Default Title',
            'sku'                 => strtoupper(fake()->lexify('SKU-????')),
            'price'               => fake()->randomFloat(2, 5, 500),
            'compare_at_price'    => null,
            'cost_per_item'       => null,
            'inventory_quantity'  => fake()->numberBetween(0, 100),
            'inventory_policy'    => 'deny',
            'weight'              => fake()->randomFloat(3, 0.1, 5),
            'weight_unit'         => 'kg',
            'requires_shipping'   => true,
            'taxable'             => true,
            'is_active'           => true,
            'position'            => 1,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(['inventory_quantity' => 0, 'inventory_policy' => 'deny']);
    }
}
