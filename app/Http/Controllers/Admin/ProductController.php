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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $collections = Collection::query()->orderBy('title')->get();

        return view('admin.products.create', compact('productTypes', 'collections'));
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
        $product->load(['collections', 'variants', 'images']);

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
}

