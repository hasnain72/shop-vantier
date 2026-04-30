<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(3, true);
        return [
            'title'            => ucwords($title),
            'slug'             => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'body_html'        => '<p>' . fake()->paragraph() . '</p>',
            'published'        => true,
            'published_at'     => now()->subDays(5),
            'meta_title'       => ucwords($title),
            'meta_description' => fake()->sentence(),
        ];
    }

    public function unpublished(): static
    {
        return $this->state(['published' => false, 'published_at' => null]);
    }
}
