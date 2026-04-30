<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(3, true);
        return [
            'title'            => ucwords($title),
            'slug'             => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'body_html'        => '<p>' . fake()->paragraph() . '</p>',
            'vendor'           => fake()->company(),
            'status'           => 'active',
            'requires_shipping'=> true,
            'taxable'          => true,
            'has_only_default_variant' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }
}
