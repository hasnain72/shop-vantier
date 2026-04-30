<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(4, true);
        return [
            'blog_id'      => Blog::factory(),
            'title'        => ucwords($title),
            'slug'         => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'summary_html' => '<p>' . fake()->sentence() . '</p>',
            'body_html'    => '<p>' . fake()->paragraph() . '</p>',
            'author'       => fake()->name(),
            'tags'         => ['test'],
            'published'    => true,
            'published_at' => now()->subDays(2),
        ];
    }

    public function unpublished(): static
    {
        return $this->state(['published' => false, 'published_at' => null]);
    }
}
