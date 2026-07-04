<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Accept per_page (frontend) or limit (legacy); default 20, cap 250
        $limit = min((int) ($request->per_page ?? $request->limit ?? 20), 250);

        $query = Product::with(['variants', 'productImages'])
            ->where('status', 'active');

        // ── IDs batch filter (recently-viewed / batch lookup) ────────────────
        // Accept ids[]=1&ids[]=2 to return a specific set of products in one request.
        if ($request->filled('ids')) {
            $ids = array_values(array_filter(array_map('intval', (array) $request->ids)));
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
                // Preserve the client-supplied order via FIELD().
                $query->orderByRaw('FIELD(id, ' . implode(',', $ids) . ')');
                $products = $query->take(count($ids))->get();
                return response()->json([
                    'success' => true,
                    'data'    => ['products' => ProductResource::collection($products)],
                    'meta'    => [
                        'pagination' => [
                            'total'        => $products->count(),
                            'per_page'     => $products->count(),
                            'current_page' => 1,
                            'last_page'    => 1,
                        ],
                    ],
                ]);
            }
        }

        // ── Collection filter ────────────────────────────────────────────────
        // Frontend sends a slug string that may be a collection slug (e.g. "best-sellers")
        // or a category tag slug (e.g. "alligator").  Try the collection table first;
        // fall back to a JSON tag search so category pages work without a matching collection.
        if ($request->filled('collection')) {
            $slug = $request->collection;
            $col = \App\Models\Collection::where('slug', $slug)->first();
            if ($col) {
                $query->whereHas('collections', fn ($q) =>
                    $q->where('collections.id', $col->id)
                );
            } else {
                // Category tag fallback (e.g. alligator, python, lizard…)
                $query->whereJsonContains('tags', $slug);
            }
        } elseif ($request->filled('collection_id')) {
            $query->whereHas('collections', fn ($q) =>
                $q->where('collections.id', $request->collection_id)
            );
        }

        // ── Full-text search ─────────────────────────────────────────────────
        // Frontend sends q=; legacy callers may send title=.
        $search = $request->filled('q') ? $request->q
                : ($request->filled('title') ? $request->title : null);
        if ($search) {
            $query->where(fn ($q) =>
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('body_html', 'like', '%' . $search . '%')
            );
        }

        // ── Price filter ─────────────────────────────────────────────────────
        // Only apply when the value is meaningful (price_min=0 is a no-op).
        $priceMin = $request->has('price_min') ? (float) $request->price_min : null;
        $priceMax = $request->has('price_max') ? (float) $request->price_max : null;

        if ($priceMin !== null && $priceMin > 0) {
            $query->whereHas('variants', fn ($q) =>
                $q->where('is_active', true)->where('price', '>=', $priceMin)
            );
        }
        if ($priceMax !== null && $priceMax > 0) {
            $query->whereHas('variants', fn ($q) =>
                $q->where('is_active', true)->where('price', '<=', $priceMax)
            );
        }

        // ── Stock filter ─────────────────────────────────────────────────────
        // Both checked (or both unchecked) → no filter; show everything.
        $inStock    = $this->parseBool($request->in_stock);
        $outOfStock = $this->parseBool($request->out_of_stock);

        if ($inStock === true && $outOfStock !== true) {
            // At least one active variant has inventory > 0
            $query->whereHas('variants', fn ($q) =>
                $q->where('is_active', true)->where('inventory_quantity', '>', 0)
            );
        } elseif ($outOfStock === true && $inStock !== true) {
            // No active variant has inventory > 0
            $query->whereDoesntHave('variants', fn ($q) =>
                $q->where('is_active', true)->where('inventory_quantity', '>', 0)
            );
        }

        // ── Legacy exact-match filters ────────────────────────────────────────
        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }
        if ($request->filled('vendor')) {
            $query->where('vendor', $request->vendor);
        }
        if ($request->filled('product_category')) {
            $query->where('product_category', $request->product_category);
        }

        // ── Sort ─────────────────────────────────────────────────────────────
        // Frontend sends sort= with one of the 9 ProductSortKey values.
        // Legacy callers may send sort_by= + sort_direction=.
        switch ($request->sort ?? '') {
            case 'alpha-asc':
                $query->orderBy('title', 'asc');
                break;
            case 'alpha-desc':
                $query->orderBy('title', 'desc');
                break;
            case 'price-asc':
                $query->orderByRaw(
                    '(SELECT MIN(price) FROM product_variants WHERE product_id = products.id AND is_active = 1) ASC'
                );
                break;
            case 'price-desc':
                $query->orderByRaw(
                    '(SELECT MAX(price) FROM product_variants WHERE product_id = products.id AND is_active = 1) DESC'
                );
                break;
            case 'date-new':
                $query->orderBy('created_at', 'desc');
                break;
            case 'date-old':
                $query->orderBy('created_at', 'asc');
                break;
            case 'featured':
            case 'best-selling':
            case 'most-relevant':
                $query->orderBy('sort_position', 'asc')->orderBy('created_at', 'desc');
                break;
            default:
                // Legacy sort_by / sort_direction params
                $sortBy  = in_array($request->sort_by, ['title', 'created_at', 'price'])
                    ? $request->sort_by : 'created_at';
                $sortDir = $request->sort_direction === 'asc' ? 'asc' : 'desc';
                if ($sortBy === 'price') {
                    $query->orderByRaw(
                        '(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) ' . $sortDir
                    );
                } else {
                    $query->orderBy($sortBy, $sortDir);
                }
        }

        $products = $query->paginate($limit);

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

    public function show(string $identifier): JsonResponse
    {
        $product = Product::with(['variants', 'productImages', 'collections', 'addons' => fn ($q) => $q->active()])
            ->where(function ($q) use ($identifier) {
                is_numeric($identifier)
                    ? $q->where('id', $identifier)
                    : $q->where('slug', $identifier);
            })
            ->where('status', 'active')
            ->firstOrFail();

        return ApiResponse::success(['product' => new ProductResource($product)]);
    }

    public function showByHandle(string $slug): JsonResponse
    {
        $product = Product::with(['variants', 'productImages', 'collections', 'addons' => fn ($q) => $q->active()])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        return ApiResponse::success(['product' => new ProductResource($product)]);
    }

    public function count(): JsonResponse
    {
        $count = Product::where('status', 'active')->count();

        return ApiResponse::success(['count' => $count]);
    }

    public function priceRange(): JsonResponse
    {
        $base = ProductVariant::query()
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('products.status', 'active')
            ->where('product_variants.is_active', true);

        $min = (float) (clone $base)->min('product_variants.price');
        $max = (float) (clone $base)->max('product_variants.price');

        return ApiResponse::success([
            'price_min' => floor($min),
            'price_max' => ceil($max),
        ]);
    }

    /**
     * Related products — same collection(s) or overlapping tags, excluding the current product.
     * Used by the "People Also Bought" section on the PDP.
     *
     * GET /products/{product}/related?count=4
     */
    public function related(Product $product, Request $request): JsonResponse
    {
        $count = min((int) ($request->count ?? 4), 20);

        $product->loadMissing('collections');
        $collectionIds = $product->collections->pluck('id')->toArray();
        $tags          = is_array($product->tags) ? $product->tags : [];

        $query = Product::with(['variants', 'productImages'])
            ->where('status', 'active')
            ->where('id', '!=', $product->id);

        if (!empty($collectionIds) || !empty($tags)) {
            $query->where(function ($q) use ($collectionIds, $tags) {
                if (!empty($collectionIds)) {
                    $q->whereHas('collections', fn ($cq) =>
                        $cq->whereIn('collections.id', $collectionIds)
                    );
                }
                if (!empty($tags)) {
                    $q->orWhere(function ($tq) use ($tags) {
                        foreach ($tags as $tag) {
                            $tq->orWhereJsonContains('tags', $tag);
                        }
                    });
                }
            });
        }

        $products = $query
            ->orderByRaw('(SELECT COUNT(*) FROM product_images WHERE product_images.product_id = products.id) DESC')
            ->orderBy('sort_position', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit($count)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => ['products' => ProductResource::collection($products)],
            'meta'    => [
                'pagination' => [
                    'total'        => $products->count(),
                    'per_page'     => $count,
                    'current_page' => 1,
                    'last_page'    => 1,
                ],
            ],
        ]);
    }

    /**
     * Compatible straps / bands for a watch product, used by the "Might also be of
     * interest" section on the PDP. The catalog has no explicit watch<->strap link
     * table, so accessories are identified by product_type: anything that isn't a
     * watch, box, winder, or tool is a strap/band (see product_category "Watch Bands").
     *
     * GET /products/{product}/compatible-accessories?count=4
     */
    public function compatibleAccessories(Product $product, Request $request): JsonResponse
    {
        $count = min((int) ($request->count ?? 4), 20);

        $nonAccessoryTypes = ['Watches', 'Watch Boxes', 'Watch Winders', 'Watch Tools'];

        $products = Product::with(['variants', 'productImages'])
            ->where('status', 'active')
            ->where('id', '!=', $product->id)
            ->where(fn ($q) =>
                $q->whereNull('product_type')
                  ->orWhereNotIn('product_type', $nonAccessoryTypes)
            )
            ->orderByRaw('(SELECT COUNT(*) FROM product_images WHERE product_images.product_id = products.id) DESC')
            ->orderBy('sort_position', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit($count)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => ['products' => ProductResource::collection($products)],
            'meta'    => [
                'pagination' => [
                    'total'        => $products->count(),
                    'per_page'     => $count,
                    'current_page' => 1,
                    'last_page'    => 1,
                ],
            ],
        ]);
    }

    /** Convert "true"/"false" strings (sent by Angular HttpParams) to bool or null. */
    private function parseBool(mixed $value): ?bool
    {
        if ($value === null || $value === '') return null;
        if (is_bool($value)) return $value;
        if ($value === 'true'  || $value === '1' || $value === 1) return true;
        if ($value === 'false' || $value === '0' || $value === 0) return false;
        return null;
    }
}
