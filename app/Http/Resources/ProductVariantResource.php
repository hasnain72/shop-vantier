<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'product_id'           => $this->product_id,
            'title'                => $this->title,
            'sku'                  => $this->sku,
            'barcode'              => $this->barcode,
            'price'                => (string) $this->price,
            'compare_at_price'     => $this->compare_at_price ? (string) $this->compare_at_price : null,
            'option1'              => $this->option1,
            'option2'              => $this->option2,
            'option3'              => $this->option3,
            'weight'               => $this->weight,
            'weight_unit'          => $this->weight_unit,
            'requires_shipping'    => (bool) $this->requires_shipping,
            'taxable'              => (bool) $this->taxable,
            'inventory_quantity'   => (int) $this->inventory_quantity,
            'inventory_policy'     => $this->inventory_policy,
            'inventory_management' => $this->inventory_management,
            'fulfillment_service'  => $this->fulfillment_service,
            'position'             => (int) $this->position,
            'is_active'            => (bool) $this->is_active,
            'created_at'           => $this->created_at?->toISOString(),
            'updated_at'           => $this->updated_at?->toISOString(),
        ];
    }
}
