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
            'data'    => $pages->map(fn ($p) => $this->pageData($p)),
            'meta'    => ['pagination' => ['total' => $pages->total(), 'current_page' => $pages->currentPage(), 'last_page' => $pages->lastPage()]],
        ]);
    }

    public function page(string $handle)
    {
        $page = Page::where('handle', $handle)->where('published', true)->firstOrFail();
        return response()->json(['success' => true, 'data' => $this->pageData($page)]);
    }

    public function blogs()
    {
        $blogs = Blog::withCount('articles')->get()->map(fn ($b) => [
            'id'             => $b->id,
            'title'          => $b->title,
            'handle'         => $b->slug,
            'articles_count' => $b->articles_count,
        ]);
        return response()->json(['success' => true, 'data' => $blogs]);
    }

    public function blog(string $handle)
    {
        $blog = Blog::where('slug', $handle)->firstOrFail();
        return response()->json(['success' => true, 'data' => [
            'id'    => $blog->id,
            'title' => $blog->title,
            'handle'=> $blog->slug,
        ]]);
    }

    public function articles(Request $request, string $handle)
    {
        $blog     = Blog::where('slug', $handle)->firstOrFail();
        $articles = $blog->articles()->published()->latest('published_at')
            ->paginate(config('api.per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $articles->map(fn ($a) => $this->articleData($a)),
            'meta'    => ['pagination' => ['total' => $articles->total(), 'current_page' => $articles->currentPage(), 'last_page' => $articles->lastPage()]],
        ]);
    }

    public function article(int $id)
    {
        $article = Article::published()->findOrFail($id);
        return response()->json(['success' => true, 'data' => $this->articleData($article)]);
    }

    private function pageData(Page $p): array
    {
        return [
            'id'               => $p->id,
            'title'            => $p->title,
            'handle'           => $p->handle,
            'body_html'        => $p->body_html,
            'published_at'     => $p->published_at?->toIso8601String(),
            'meta_title'       => $p->meta_title,
            'meta_description' => $p->meta_description,
        ];
    }

    private function articleData(Article $a): array
    {
        return [
            'id'               => $a->id,
            'blog_id'          => $a->blog_id,
            'title'            => $a->title,
            'handle'           => $a->slug,
            'author'           => $a->author,
            'body_html'        => $a->body_html,
            'summary_html'     => $a->summary_html,
            'tags'             => $a->tags ?? [],
            'published_at'     => $a->published_at?->toIso8601String(),
            'meta_title'       => $a->meta_title,
            'meta_description' => $a->meta_description,
        ];
    }
}
