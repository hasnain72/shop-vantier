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
        $request->validate(['q' => 'required|string|min:1']);

        $q     = trim($request->get('q', ''));
        $type  = $request->get('type');
        $limit = min((int) $request->get('limit', 10), 50);

        $results = [];

        if (!$type || $type === 'product') {
            $products = Product::where('status', 'active')
                ->where(fn ($q2) => $q2->where('title', 'like', "%$q%")->orWhere('vendor', 'like', "%$q%")->orWhere('product_type', 'like', "%$q%"))
                ->limit($limit)
                ->get()
                ->map(fn ($p) => [
                    'type'      => 'product',
                    'id'        => $p->id,
                    'title'     => $p->title,
                    'slug'      => $p->slug,
                    'vendor'    => $p->vendor,
                    'price_min' => $p->variants()->min('price'),
                    'image'     => $p->featured_image ? Storage::disk('public')->url($p->featured_image) : null,
                ])->all();
            array_push($results, ...$products);
        }

        if (!$type || $type === 'collection') {
            $collections = Collection::published()
                ->where('title', 'like', "%$q%")
                ->limit($limit)
                ->get()
                ->map(fn ($c) => [
                    'type'  => 'collection',
                    'id'    => $c->id,
                    'title' => $c->title,
                    'slug'  => $c->slug,
                    'image' => $c->image ? Storage::disk('public')->url($c->image) : null,
                ])->all();
            array_push($results, ...$collections);
        }

        if (!$type || $type === 'page') {
            $pages = Page::where('published', true)
                ->where(fn ($q2) => $q2->where('title', 'like', "%$q%")->orWhere('body_html', 'like', "%$q%"))
                ->limit($limit)
                ->get()
                ->map(fn ($p) => [
                    'type'         => 'page',
                    'id'           => $p->id,
                    'title'        => $p->title,
                    'slug'         => $p->slug,
                    'body_excerpt' => strip_tags(substr($p->body_html ?? '', 0, 160)),
                ])->all();
            array_push($results, ...$pages);
        }

        if (!$type || $type === 'article') {
            $articles = Article::published()
                ->where(fn ($q2) => $q2->where('title', 'like', "%$q%")->orWhere('body_html', 'like', "%$q%"))
                ->limit($limit)
                ->get()
                ->map(fn ($a) => [
                    'type'         => 'article',
                    'id'           => $a->id,
                    'title'        => $a->title,
                    'slug'         => $a->slug,
                    'body_excerpt' => strip_tags(substr($a->body_html ?? '', 0, 160)),
                    'published_at' => $a->published_at?->toIso8601String(),
                ])->all();
            array_push($results, ...$articles);
        }

        return response()->json([
            'success' => true,
            'data'    => ['results' => $results],
        ]);
    }
}
