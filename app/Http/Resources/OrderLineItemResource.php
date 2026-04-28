<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OrderLineItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'variant_id'         => $this->variant_id,
            'product_id'         => $this->product_id,
            'title'              => $this->title,
            'variant_title'      => $this->variant_title,
            'sku'                => $this->sku,
            'quantity'           => $this->quantity,
            'price'              => (string) $this->price,
            'total_discount'     => (string) $this->total_discount,
            'requires_shipping'  => (bool) $this->requires_shipping,
            'taxable'            => (bool) $this->taxable,
            'fulfillment_status' => $this->fulfillment_status,
            'image'              => optional($this->variant?->product)?->featured_image
                ? Storage::disk('public')->url($this->variant->product->featured_image) : null,
        ];
    }
}
