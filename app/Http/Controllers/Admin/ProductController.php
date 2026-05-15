<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductType;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()->withCount('variants');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('product_type_id')) {
            $query->where('product_type_id', $request->product_type_id);
        }

        if ($request->filled('vendor')) {
            $query->where('vendor', $request->vendor);
        }

        if ($request->filled('collection_id')) {
            $query->whereHas('collections', fn ($q) => $q->where('collections.id', $request->collection_id));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('variants', fn ($v) => $v->where('sku', 'like', "%{$search}%"));
            });
        }

        $sortMap = [
            'oldest'     => ['id', 'asc'],
            'title-asc'  => ['title', 'asc'],
            'title-desc' => ['title', 'desc'],
            'price-asc'  => ['sort_position', 'asc'],
            'price-desc' => ['sort_position', 'desc'],
        ];
        [$sortCol, $sortDir] = $sortMap[$request->sort] ?? ['id', 'desc'];
        $query->orderBy($sortCol, $sortDir);

        $products = $query->paginate(20)->withQueryString();

        $productTypes = ProductType::active()->orderBy('name')->get();
        $collections  = Collection::orderBy('title')->get();
        $vendors      = Product::whereNotNull('vendor')->distinct()->pluck('vendor');

        return view('admin.products.index', compact('products', 'productTypes', 'collections', 'vendors'));
    }

    public function create(): View
    {
        $productTypes = ProductType::query()->active()->orderBy('name')->get();
        $collections  = Collection::query()->orderBy('title')->get();
        $product      = null;

        return view('admin.products.create', compact('productTypes', 'collections', 'product'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $collectionIds = $data['collection_ids'] ?? [];
        $imageFiles    = $request->file('images', []);
        unset($data['collection_ids']);

        DB::transaction(function () use ($data, $collectionIds, $imageFiles, &$product) {
            $product = Product::create($data);

            if (! empty($collectionIds)) {
                $product->collections()->sync($collectionIds);
            }

            ProductVariant::create([
                'product_id'       => $product->id,
                'title'            => 'Default',
                'sku'              => $data['default_sku'] ?? null,
                'price'            => $data['default_price'],
                'requires_shipping' => (bool) ($data['requires_shipping'] ?? true),
                'taxable'          => (bool) ($data['taxable'] ?? true),
                'is_active'        => true,
            ]);

            foreach ($imageFiles as $position => $file) {
                $path = $file->store("products/{$product->id}", 'public');
                [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];
                $img = ProductImage::create([
                    'product_id' => $product->id,
                    'src'        => $path,
                    'alt'        => $product->title,
                    'position'   => $position + 1,
                    'width'      => $width,
                    'height'     => $height,
                ]);
                if ($position === 0) {
                    $product->update(['featured_image' => $path]);
                }
            }
        });

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $product->load(['collections', 'variants', 'productImages', 'addons']);

        $productTypes = ProductType::query()->active()->orderBy('name')->get();
        $collections = Collection::query()->orderBy('title')->get();

        return view('admin.products.edit', compact('product', 'productTypes', 'collections'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        $collectionIds  = $data['collection_ids'] ?? [];
        $imageFiles     = $request->file('images', []);
        $deleteImageIds = $data['delete_image_ids'] ?? [];
        unset($data['collection_ids'], $data['delete_image_ids']);

        DB::transaction(function () use ($data, $collectionIds, $imageFiles, $deleteImageIds, $product) {
            $product->update($data);
            $product->collections()->sync($collectionIds);

            // Delete removed images
            if (! empty($deleteImageIds)) {
                $toDelete = ProductImage::where('product_id', $product->id)
                    ->whereIn('id', $deleteImageIds)
                    ->get();
                foreach ($toDelete as $img) {
                    Storage::disk('public')->delete($img->src);
                    $img->delete();
                }
            }

            // Upload new images
            $nextPosition = ProductImage::where('product_id', $product->id)->max('position') ?? 0;
            foreach ($imageFiles as $file) {
                $nextPosition++;
                $path = $file->store("products/{$product->id}", 'public');
                [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];
                ProductImage::create([
                    'product_id' => $product->id,
                    'src'        => $path,
                    'alt'        => $product->title,
                    'position'   => $nextPosition,
                    'width'      => $width,
                    'height'     => $height,
                ]);
            }

            // Keep featured_image in sync with first image
            $firstImage = ProductImage::where('product_id', $product->id)->orderBy('position')->first();
            $product->update(['featured_image' => $firstImage?->src]);
        });

        return back()->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    /**
     * Download external (Shopify CDN) images for the next batch of products
     * and save them locally. Returns JSON with progress stats.
     */
    public function downloadImages(Request $request): JsonResponse
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $batchSize = (int) $request->input('batch', 20);

        // Pick next N products that still have at least one external image
        $productIds = ProductImage::where('src', 'like', '%cdn.shopify.com%')
            ->select('product_id')
            ->distinct()
            ->limit($batchSize)
            ->pluck('product_id');

        $downloaded = 0;
        $failed     = 0;
        $errors     = [];

        foreach ($productIds as $productId) {
            $images = ProductImage::where('product_id', $productId)
                ->where('src', 'like', '%cdn.shopify.com%')
                ->get();

            foreach ($images as $image) {
                try {
                    $originalUrl = $image->src;

                    // Strip query string for a clean filename
                    $cleanUrl  = strtok($originalUrl, '?');
                    $filename  = basename($cleanUrl);
                    $ext       = strtolower(pathinfo($filename, PATHINFO_EXTENSION)) ?: 'jpg';

                    // Sanitize: keep only safe chars
                    $safeName  = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
                    $dir       = "products/{$productId}";
                    $localPath = "{$dir}/{$safeName}";

                    // Download with 30s timeout (SSL verify off — CDN is trusted)
                    $response = Http::withoutVerifying()->timeout(30)->get($originalUrl);

                    if (! $response->successful()) {
                        $failed++;
                        $errors[] = "HTTP {$response->status()}: {$originalUrl}";
                        continue;
                    }

                    Storage::disk('public')->makeDirectory($dir);
                    Storage::disk('public')->put($localPath, $response->body());

                    $localUrl = $localPath;

                    // Update product_images.src
                    $image->src = $localUrl;
                    $image->save();

                    // Update products.featured_image (bypass Spatie slug events)
                    DB::table('products')
                        ->where('featured_image', $originalUrl)
                        ->update(['featured_image' => $localUrl]);

                    $downloaded++;

                } catch (\Throwable $e) {
                    $failed++;
                    $errors[] = $e->getMessage();
                }
            }
        }

        $remaining = ProductImage::where('src', 'like', '%cdn.shopify.com%')
            ->select('product_id')->distinct()->count();

        return response()->json([
            'downloaded' => $downloaded,
            'failed'     => $failed,
            'remaining'  => $remaining,
            'done'       => $remaining === 0,
            'errors'     => array_slice($errors, 0, 5),
        ]);
    }

    public function bulkPriceUpdate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_ids'   => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
            'price_action'  => 'required|in:set_fixed,increase_percent,decrease_percent,increase_fixed,decrease_fixed',
            'price_value'   => 'required|numeric|min:0',
        ]);

        $variants = ProductVariant::whereIn('product_id', $data['product_ids'])->get();

        if ($variants->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No variants found for selected products.'], 422);
        }

        $updated = 0;

        DB::transaction(function () use ($variants, $data, &$updated) {
            foreach ($variants as $variant) {
                $currentPrice = (float) $variant->price;

                $newPrice = match ($data['price_action']) {
                    'set_fixed'        => (float) $data['price_value'],
                    'increase_fixed'   => $currentPrice + (float) $data['price_value'],
                    'decrease_fixed'   => max(0, $currentPrice - (float) $data['price_value']),
                    'increase_percent' => $currentPrice * (1 + $data['price_value'] / 100),
                    'decrease_percent' => $currentPrice * (1 - $data['price_value'] / 100),
                };

                $newPrice = max(0, round($newPrice, 2));

                $variant->update(['price' => $newPrice]);
                $updated++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => "{$updated} variant(s) updated across " . count($data['product_ids']) . " product(s).",
        ]);
    }

    /** Return count of products still having external images (for the button badge). */
    public function imageDownloadStatus(): JsonResponse
    {
        $remaining = ProductImage::where('src', 'like', '%cdn.shopify.com%')
            ->select('product_id')->distinct()->count();

        return response()->json(['remaining' => $remaining]);
    }
}

