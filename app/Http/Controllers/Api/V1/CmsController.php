<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Blog;
use App\Models\Page;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function pages(Request $request)
    {
        $pages = Page::where('published', true)->latest()->paginate(config('api.per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => ['pages' => $pages->map(fn ($p) => $this->pageData($p))->values()],
            'meta'    => ['pagination' => ['total' => $pages->total(), 'current_page' => $pages->currentPage(), 'last_page' => $pages->lastPage()]],
        ]);
    }

    public function page(string $handle)
    {
        $page = Page::where('slug', $handle)->where('published', true)->firstOrFail();
        return response()->json(['success' => true, 'data' => ['page' => $this->pageData($page)]]);
    }

    public function blogs(Request $request)
    {
        $locale = $this->resolveLocale($request);
        $blogs  = Blog::withCount('articles')->get()->map(fn ($b) => [
            'id'             => $b->id,
            'title'          => $this->pick($b->title_ar, $b->title, $locale),
            'slug'           => $b->slug,
            'articles_count' => $b->articles_count,
        ]);
        return response()->json(['success' => true, 'data' => ['blogs' => $blogs->values()]]);
    }

    public function blog(Request $request, string $handle)
    {
        $locale = $this->resolveLocale($request);
        $blog   = Blog::where('slug', $handle)->firstOrFail();
        return response()->json(['success' => true, 'data' => ['blog' => [
            'id'    => $blog->id,
            'title' => $this->pick($blog->title_ar, $blog->title, $locale),
            'slug'  => $blog->slug,
        ]]]);
    }

    public function articles(Request $request, string $handle)
    {
        $locale   = $this->resolveLocale($request);
        $blog     = Blog::where('slug', $handle)->firstOrFail();
        $articles = $blog->articles()->published()->latest('published_at')
            ->paginate(config('api.per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => ['articles' => $articles->map(fn ($a) => $this->articleData($a, $locale))->values()],
            'meta'    => ['pagination' => ['total' => $articles->total(), 'current_page' => $articles->currentPage(), 'last_page' => $articles->lastPage()]],
        ]);
    }

    public function article(Request $request, int $id)
    {
        $locale  = $this->resolveLocale($request);
        $article = Article::published()->findOrFail($id);
        return response()->json(['success' => true, 'data' => ['article' => $this->articleData($article, $locale)]]);
    }

    /** Look up a published article by its slug (used by the storefront). */
    public function articleBySlug(Request $request, string $slug)
    {
        $locale  = $this->resolveLocale($request);
        $article = Article::published()->where('slug', $slug)->firstOrFail();
        return response()->json(['success' => true, 'data' => ['article' => $this->articleData($article, $locale)]]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /** Resolve locale from ?locale= query param or Accept-Language header. */
    private function resolveLocale(Request $request): string
    {
        $locale = $request->query('locale') ?? $request->getPreferredLanguage(['en', 'ar']) ?? 'en';
        return in_array($locale, ['ar', 'en']) ? $locale : 'en';
    }

    /** Return Arabic value when locale=ar and it exists, otherwise fall back to English. */
    private function pick(?string $ar, ?string $en, string $locale): ?string
    {
        if ($locale === 'ar' && !empty($ar)) {
            return $ar;
        }
        return $en;
    }

    private function pageData(Page $p): array
    {
        return [
            'id'               => $p->id,
            'title'            => $p->title,
            'slug'             => $p->slug,
            'body_html'        => $p->body_html,
            'published_at'     => $p->published_at?->toIso8601String(),
            'meta_title'       => $p->meta_title,
            'meta_description' => $p->meta_description,
        ];
    }

    private function articleData(Article $a, string $locale = 'en'): array
    {
        return [
            'id'               => $a->id,
            'blog_id'          => $a->blog_id,
            'title'            => $this->pick($a->title_ar, $a->title, $locale),
            'title_en'         => $a->title,
            'title_ar'         => $a->title_ar,
            'slug'             => $a->slug,
            'author'           => $this->pick($a->author_ar, $a->author, $locale),
            'body_html'        => $this->pick($a->body_html_ar, $a->body_html, $locale),
            'summary_html'     => $this->pick($a->summary_html_ar, $a->summary_html, $locale),
            'image'            => $a->image,
            'tags'             => $a->tags ?? [],
            'published_at'     => $a->published_at?->toIso8601String(),
            'meta_title'       => $a->meta_title,
            'meta_description' => $a->meta_description,
        ];
    }
}
