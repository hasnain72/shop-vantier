<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Collection;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q     = trim($request->get('q', ''));
        $type  = $request->get('type');
        $limit = min((int) $request->get('limit', 10), 50);

        if (strlen($q) < 2) {
            return response()->json(['success' => false, 'message' => 'Query too short.'], 422);
        }

        $products    = [];
        $collections = [];
        $pages       = [];
        $articles    = [];

        if (!$type || $type === 'product') {
            $products = Product::where('status', 'active')
                ->where(fn ($q2) => $q2->where('title', 'like', "%$q%")->orWhere('vendor', 'like', "%$q%")->orWhere('product_type', 'like', "%$q%"))
                ->limit($limit)
                ->get()
                ->map(fn ($p) => [
                    'id'           => $p->id,
                    'title'        => $p->title,
                    'slug'         => $p->slug,
                    'vendor'       => $p->vendor,
                    'price_min'    => $p->variants()->min('price'),
                    'image'        => $p->featured_image ? Storage::disk('public')->url($p->featured_image) : null,
                ])->all();
        }

        if (!$type || $type === 'collection') {
            $collections = Collection::published()
                ->where('title', 'like', "%$q%")
                ->limit($limit)
                ->get()
                ->map(fn ($c) => [
                    'id'    => $c->id,
                    'title' => $c->title,
                    'slug'  => $c->slug,
                    'image' => $c->image ? Storage::disk('public')->url($c->image) : null,
                ])->all();
        }

        if (!$type || $type === 'page') {
            $pages = Page::where('published', true)
                ->where(fn ($q2) => $q2->where('title', 'like', "%$q%")->orWhere('body_html', 'like', "%$q%"))
                ->limit($limit)
                ->get()
                ->map(fn ($p) => [
                    'id'           => $p->id,
                    'title'        => $p->title,
                    'slug'         => $p->handle,
                    'body_excerpt' => strip_tags(substr($p->body_html ?? '', 0, 160)),
                ])->all();
        }

        if (!$type || $type === 'article') {
            $articles = Article::published()
                ->where(fn ($q2) => $q2->where('title', 'like', "%$q%")->orWhere('body_html', 'like', "%$q%"))
                ->limit($limit)
                ->get()
                ->map(fn ($a) => [
                    'id'           => $a->id,
                    'title'        => $a->title,
                    'slug'         => $a->slug,
                    'body_excerpt' => strip_tags(substr($a->body_html ?? '', 0, 160)),
                    'published_at' => $a->published_at?->toIso8601String(),
                ])->all();
        }

        $total = count($products) + count($collections) + count($pages) + count($articles);

        return response()->json([
            'success' => true,
            'data'    => compact('products', 'collections', 'pages', 'articles', 'total'),
        ]);
    }
}
