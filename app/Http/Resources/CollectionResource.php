<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'title_ar'       => $this->title_ar,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'description_ar' => $this->description_ar,
            'image'          => $this->image
                                   ? Storage::disk('public')->url($this->image)
                                   : ($this->thumbnail_src ? Storage::disk('public')->url($this->thumbnail_src) : null),
            'sort_order'     => $this->sort_order,
            'published'      => (bool) $this->published,
            'published_at'   => $this->published_at?->toISOString(),
            'meta_title'     => $this->meta_title,
            'meta_description' => $this->meta_description,
            'products_count' => $this->whenCounted('products'),
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
        ];
    }
}
