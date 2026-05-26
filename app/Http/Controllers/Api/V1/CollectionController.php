<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\ProductResource;
use App\Models\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Accept per_page (frontend) or limit (legacy); default 20, cap 250
        $limit = min((int) ($request->per_page ?? $request->limit ?? 20), 250);

        $query = Collection::withCount('products');

        if ($request->filled('published')) {
            $request->boolean('published')
                ? $query->where('published', true)
                : $query->where('published', false);
        } else {
            $query->where('published', true);
        }

        $collections = $query->orderBy('sort_position')->paginate($limit);

        // One extra query: pull one product image per collection to use as thumbnail
        // when the collection itself has no image set.
        $ids = $collections->pluck('id');
        $thumbnails = DB::table('collection_product as cp')
            ->join('products as p', function ($join) {
                $join->on('p.id', '=', 'cp.product_id')
                     ->where('p.status', 'active')
                     ->whereNull('p.deleted_at');
            })
            ->join('product_images as pi', 'pi.product_id', '=', 'p.id')
            ->whereIn('cp.collection_id', $ids)
            ->select('cp.collection_id', DB::raw('MIN(pi.src) as src'))
            ->groupBy('cp.collection_id')
            ->get()
            ->keyBy('collection_id');

        $collections->each(function ($c) use ($thumbnails) {
            $c->thumbnail_src = $thumbnails->get($c->id)?->src ?? null;
        });

        return response()->json([
            'success' => true,
            'data'    => ['collections' => CollectionResource::collection($collections->items())],
            'meta'    => [
                'pagination' => [
                    'total'        => $collections->total(),
                    'per_page'     => $collections->perPage(),
                    'current_page' => $collections->currentPage(),
                    'last_page'    => $collections->lastPage(),
                ],
            ],
        ]);
    }

    public function show(string $identifier): JsonResponse
    {
        $collection = Collection::withCount('products')
            ->where(function ($q) use ($identifier) {
                is_numeric($identifier)
                    ? $q->where('id', $identifier)
                    : $q->where('slug', $identifier);
            })
            ->firstOrFail();

        return ApiResponse::success(['collection' => new CollectionResource($collection)]);
    }

    public function count(): JsonResponse
    {
        return ApiResponse::success(['count' => Collection::where('published', true)->count()]);
    }

    public function showByHandle(string $slug): JsonResponse
    {
        $collection = Collection::withCount('products')
            ->where('slug', $slug)->firstOrFail();
        return ApiResponse::success(['collection' => new CollectionResource($collection)]);
    }

    public function products(Request $request, string $identifier): JsonResponse
    {
        $collection = Collection::where(function ($q) use ($identifier) {
            is_numeric($identifier)
                ? $q->where('id', $identifier)
                : $q->where('slug', $identifier);
        })->firstOrFail();

        $limit = min((int) ($request->limit ?? 20), 250);

        $products = $collection->products()
            ->with(['variants', 'productImages'])
            ->where('status', 'active')
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data'    => ['products' => ProductResource::collection($products->items())],
            'meta'    => [
                'pagination' => [
                    'total'        => $products->total(),
                    'per_page'     => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page'    => $products->lastPage(),
                ],
            ],
        ]);
    }
}
