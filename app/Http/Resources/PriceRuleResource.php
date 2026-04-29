<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceRuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                          => $this->id,
            'title'                       => $this->title,
            'value_type'                  => $this->value_type,
            'value'                       => (float) $this->value,
            'target_type'                 => $this->target_type,
            'target_selection'            => $this->target_selection,
            'customer_selection'          => $this->customer_selection,
            'once_per_customer'           => $this->once_per_customer,
            'usage_limit'                 => $this->usage_limit,
            'usage_count'                 => $this->usage_count,
            'starts_at'                   => $this->starts_at?->toIso8601String(),
            'ends_at'                     => $this->ends_at?->toIso8601String(),
            'prerequisite_subtotal_range' => $this->prerequisite_subtotal_range,
            'prerequisite_quantity_range' => $this->prerequisite_quantity_range,
            'entitled_product_ids'        => $this->entitled_product_ids ?? [],
            'entitled_collection_ids'     => $this->entitled_collection_ids ?? [],
            'discount_codes'              => DiscountCodeResource::collection($this->whenLoaded('discountCodes')),
            'created_at'                  => $this->created_at->toIso8601String(),
        ];
    }
}
