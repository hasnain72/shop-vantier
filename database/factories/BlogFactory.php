<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(2, true);
        return [
            'title'            => ucwords($title),
            'slug'             => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'commentable'      => 'no',
            'meta_title'       => ucwords($title),
            'meta_description' => fake()->sentence(),
        ];
    }
}
