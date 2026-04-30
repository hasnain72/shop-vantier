<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CollectionFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(2, true);
        return [
            'title'       => ucwords($title),
            'slug'        => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->sentence(),
            'published'   => true,
            'sort_order'  => 'manual',
        ];
    }

    public function unpublished(): static
    {
        return $this->state(['published' => false]);
    }
}
