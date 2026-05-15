<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductAddonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'product_id'         => $this->product_id,
            'name'               => $this->name,
            'sku'                => $this->sku,
            'price'              => (float) $this->price,
            'image_url'          => $this->image ? Storage::disk('public')->url($this->image) : null,
            'inventory_quantity' => $this->inventory_quantity,
            'inventory_policy'   => $this->inventory_policy,
            'available'          => $this->hasStock(),
            'is_default'         => $this->is_default,
            'is_active'          => $this->is_active,
            'position'           => $this->position,
        ];
    }
}
