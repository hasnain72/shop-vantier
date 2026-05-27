<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = in_array($request->query('locale'), ['ar', 'en']) ? $request->query('locale') : 'en';

        return [
            'id'           => $this->id,
            'title'        => ($locale === 'ar' && !empty($this->title_ar)) ? $this->title_ar : $this->title,
            'title_en'     => $this->title,
            'title_ar'     => $this->title_ar,
            'slug'         => $this->slug,
            'body_html'    => ($locale === 'ar' && !empty($this->body_html_ar)) ? $this->body_html_ar : $this->body_html,
            'body_html_ar' => $this->body_html_ar,
            'vendor'           => $this->vendor,
            'product_type'     => $this->product_type,
            'product_category' => $this->product_category,
            'tags'         => $this->tags ?? [],
            'status'       => $this->status,
            'published_at' => $this->published_at?->toISOString(),
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
            'images'       => $this->whenLoaded('productImages', function () {
                return $this->productImages->map(fn ($img) => [
                    'id'       => $img->id,
                    'src'      => Storage::disk('public')->url($img->src),
                    'alt'      => $img->alt,
                    'position' => $img->position,
                    'width'    => $img->width,
                    'height'   => $img->height,
                ])->values();
            }, []),
            'variants'     => ProductVariantResource::collection($this->whenLoaded('variants')),
            'options'      => $this->options ?? [],
            'addons'       => ProductAddonResource::collection($this->whenLoaded('addons')),
        ];
    }
}
