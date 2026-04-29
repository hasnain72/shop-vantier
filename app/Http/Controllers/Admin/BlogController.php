<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    // ── Blogs ────────────────────────────────────────────────────────────────

    public function index()
    {
        $blogs = Blog::withCount('articles')->latest()->get();
        return view('admin.blogs.index', compact('blogs'));
    }

    public function storeBlog(Request $request)
    {
        $data = $request->validate(['title' => 'required|string|max:255']);
        $data['slug'] = Str::slug($data['title']);
        Blog::create($data);
        return back()->with('success', 'Blog created.');
    }

    public function destroyBlog(Blog $blog)
    {
        $blog->articles()->delete();
        $blog->delete();
        return back()->with('success', 'Blog deleted.');
    }

    // ── Articles ─────────────────────────────────────────────────────────────

    public function articles(Blog $blog)
    {
        $articles = $blog->articles()->latest()->paginate(20);
        return view('admin.articles.index', compact('blog', 'articles'));
    }

    public function createArticle(Blog $blog)
    {
        return view('admin.articles.create', compact('blog'));
    }

    public function storeArticle(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'body_html'        => 'nullable|string',
            'summary_html'     => 'nullable|string|max:1000',
            'tags'             => 'nullable|string',
            'published'        => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $blog->articles()->create([
            'title'            => $data['title'],
            'slug'             => Str::slug($data['title']),
            'body_html'        => $data['body_html'] ?? null,
            'summary_html'     => $data['summary_html'] ?? null,
            'author'           => auth()->user()->name,
            'tags'             => $data['tags'] ? array_map('trim', explode(',', $data['tags'])) : [],
            'published'        => $request->boolean('published'),
            'published_at'     => $request->boolean('published') ? now() : null,
            'meta_title'       => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
        ]);

        return redirect()->route('admin.blogs.articles', $blog)->with('success', 'Article published.');
    }

    public function editArticle(Blog $blog, Article $article)
    {
        return view('admin.articles.edit', compact('blog', 'article'));
    }

    public function updateArticle(Request $request, Blog $blog, Article $article)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'body_html'        => 'nullable|string',
            'summary_html'     => 'nullable|string|max:1000',
            'tags'             => 'nullable|string',
            'published'        => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $article->update([
            'title'            => $data['title'],
            'body_html'        => $data['body_html'] ?? null,
            'summary_html'     => $data['summary_html'] ?? null,
            'tags'             => $data['tags'] ? array_map('trim', explode(',', $data['tags'])) : [],
            'published'        => $request->boolean('published'),
            'published_at'     => $request->boolean('published') ? ($article->published_at ?? now()) : null,
            'meta_title'       => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
        ]);

        return redirect()->route('admin.blogs.articles', $blog)->with('success', 'Article updated.');
    }

    public function destroyArticle(Blog $blog, Article $article)
    {
        $article->delete();
        return back()->with('success', 'Article deleted.');
    }
}
