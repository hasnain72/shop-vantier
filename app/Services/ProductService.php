<?php

namespace App\Services;

use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $collectionIds = $data['collection_ids'] ?? [];
            $imageFiles    = $data['image_files'] ?? [];
            $variantData   = $data['variants'] ?? [];
            unset($data['collection_ids'], $data['image_files'], $data['variants']);

            $product = Product::create($data);

            $this->syncCollections($product, $collectionIds);
            $this->uploadImages($product, $imageFiles);

            if (! empty($variantData)) {
                $this->createVariants($product, $variantData);
            } else {
                ProductVariant::create([
                    'product_id'        => $product->id,
                    'title'             => 'Default Title',
                    'price'             => $data['default_price'] ?? 0,
                    'sku'               => $data['default_sku'] ?? null,
                    'requires_shipping' => (bool) ($data['requires_shipping'] ?? true),
                    'taxable'           => (bool) ($data['taxable'] ?? true),
                    'is_active'         => true,
                ]);
            }

            return $product->fresh(['variants', 'images', 'collections']);
        });
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $collectionIds  = $data['collection_ids'] ?? null;
            $imageFiles     = $data['image_files'] ?? [];
            $deleteImageIds = $data['delete_image_ids'] ?? [];
            unset($data['collection_ids'], $data['image_files'], $data['delete_image_ids']);

            $product->update($data);

            if ($collectionIds !== null) {
                $this->syncCollections($product, $collectionIds);
            }

            if (! empty($deleteImageIds)) {
                $this->deleteImages($product, $deleteImageIds);
            }

            if (! empty($imageFiles)) {
                $this->uploadImages($product, $imageFiles);
            }

            $firstImage = ProductImage::where('product_id', $product->id)->orderBy('position')->first();
            $product->update(['featured_image' => $firstImage?->src]);

            return $product->fresh(['variants', 'images', 'collections']);
        });
    }

    /**
     * Generate variant combinations from option arrays.
     *
     * $options = [
     *   ['name' => 'Size',  'values' => ['S', 'M', 'L']],
     *   ['name' => 'Color', 'values' => ['Red', 'Blue']],
     * ]
     */
    public function generateVariants(Product $product, array $options): void
    {
        $combinations = [[]];

        foreach ($options as $option) {
            $newCombinations = [];
            foreach ($combinations as $existing) {
                foreach ($option['values'] as $value) {
                    $newCombinations[] = array_merge($existing, [$value]);
                }
            }
            $combinations = $newCombinations;
        }

        $position = 1;
        foreach ($combinations as $combo) {
            ProductVariant::create([
                'product_id' => $product->id,
                'title'      => implode(' / ', $combo),
                'option1'    => $combo[0] ?? null,
                'option2'    => $combo[1] ?? null,
                'option3'    => $combo[2] ?? null,
                'price'      => 0,
                'position'   => $position++,
                'is_active'  => true,
            ]);
        }

        $product->update([
            'has_only_default_variant' => false,
            'options' => collect($options)->map(fn ($o) => [
                'name'   => $o['name'],
                'values' => $o['values'],
            ])->all(),
        ]);
    }

    public function syncCollections(Product $product, array $collectionIds): void
    {
        $product->collections()->sync($collectionIds);
    }

    /**
     * @param  UploadedFile[]  $images
     */
    public function uploadImages(Product $product, array $images): void
    {
        $nextPosition = ProductImage::where('product_id', $product->id)->max('position') ?? 0;

        foreach ($images as $file) {
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
    }

    private function createVariants(Product $product, array $variantData): void
    {
        foreach ($variantData as $position => $v) {
            ProductVariant::create(array_merge($v, [
                'product_id' => $product->id,
                'position'   => $position + 1,
                'is_active'  => true,
            ]));
        }
    }

    private function deleteImages(Product $product, array $imageIds): void
    {
        $images = ProductImage::where('product_id', $product->id)
            ->whereIn('id', $imageIds)
            ->get();

        foreach ($images as $img) {
            Storage::disk('public')->delete($img->src);
            $img->delete();
        }
    }
}
