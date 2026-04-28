<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'body_html'    => $this->body_html,
            'vendor'       => $this->vendor,
            'product_type' => $this->product_type,
            'tags'         => $this->tags ?? [],
            'status'       => $this->status,
            'published_at' => $this->published_at?->toISOString(),
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
            'images'       => $this->whenLoaded('images', function () {
                return $this->images->map(fn ($img) => [
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
        ];
    }
}
