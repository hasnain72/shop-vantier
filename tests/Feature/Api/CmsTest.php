<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\Blog;
use App\Models\Page;
use Tests\TestCase;

class CmsTest extends TestCase
{
    // ── Pages ─────────────────────────────────────────────────────────────────

    public function test_can_list_published_pages(): void
    {
        Page::factory()->count(3)->create(['published' => true]);
        Page::factory()->create(['published' => false]);

        $this->getJson('/api/v1/pages')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data.pages');
    }

    public function test_can_get_page_by_slug(): void
    {
        $page = Page::factory()->create(['slug' => 'about-us', 'published' => true]);

        $this->getJson('/api/v1/pages/about-us')
            ->assertStatus(200)
            ->assertJsonPath('data.page.slug', 'about-us');
    }

    public function test_unpublished_page_returns_404(): void
    {
        Page::factory()->create(['slug' => 'hidden-page', 'published' => false]);

        $this->getJson('/api/v1/pages/hidden-page')
            ->assertStatus(404);
    }

    public function test_unknown_page_returns_404(): void
    {
        $this->getJson('/api/v1/pages/does-not-exist')
            ->assertStatus(404);
    }

    // ── Blogs ─────────────────────────────────────────────────────────────────

    public function test_can_list_blogs(): void
    {
        Blog::factory()->count(2)->create();

        $this->getJson('/api/v1/blogs')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data.blogs');
    }

    public function test_can_get_blog_by_handle(): void
    {
        $blog = Blog::factory()->create(['slug' => 'watch-reviews']);

        $this->getJson('/api/v1/blogs/watch-reviews')
            ->assertStatus(200)
            ->assertJsonPath('data.blog.slug', 'watch-reviews');
    }

    public function test_unknown_blog_returns_404(): void
    {
        $this->getJson('/api/v1/blogs/no-such-blog')
            ->assertStatus(404);
    }

    // ── Articles ──────────────────────────────────────────────────────────────

    public function test_can_list_articles_for_blog(): void
    {
        $blog = Blog::factory()->create(['slug' => 'news']);
        Article::factory()->count(3)->create(['blog_id' => $blog->id, 'published' => true]);
        Article::factory()->create(['blog_id' => $blog->id, 'published' => false]);

        $this->getJson('/api/v1/blogs/news/articles')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data.articles');
    }

    public function test_can_get_article_by_id(): void
    {
        $article = Article::factory()->create(['published' => true]);

        $this->getJson("/api/v1/articles/{$article->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.article.id', $article->id);
    }

    public function test_unpublished_article_not_accessible(): void
    {
        $article = Article::factory()->unpublished()->create();

        $this->getJson("/api/v1/articles/{$article->id}")
            ->assertStatus(404);
    }
}
