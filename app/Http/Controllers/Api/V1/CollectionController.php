<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\ProductResource;
use App\Models\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min((int) ($request->limit ?? 20), 250);

        $query = Collection::withCount('products');

        if ($request->filled('published')) {
            $request->boolean('published')
                ? $query->where('published', true)
                : $query->where('published', false);
        } else {
            $query->where('published', true);
        }

        $collections = $query->orderBy('sort_position')->paginate($limit);

        return ApiResponse::paginated($collections, CollectionResource::class);
    }

    public function show(string $identifier): JsonResponse
    {
        $collection = Collection::withCount('products')
            ->where(function ($q) use ($identifier) {
                is_numeric($identifier)
                    ? $q->where('id', $identifier)
                    : $q->where('slug', $identifier);
            })
            ->where('published', true)
            ->firstOrFail();

        return ApiResponse::success(new CollectionResource($collection));
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
            ->with(['variants', 'images'])
            ->where('status', 'active')
            ->paginate($limit);

        return ApiResponse::paginated($products, ProductResource::class);
    }
}
