<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'order_number'       => $this->order_number,
            'name'               => $this->name,
            'email'              => $this->email,
            'phone'              => $this->phone,
            'financial_status'   => $this->financial_status,
            'fulfillment_status' => $this->fulfillment_status,
            'currency'           => $this->currency,
            'subtotal_price'     => (string) $this->subtotal_price,
            'total_discounts'    => (string) $this->total_discounts,
            'total_tax'          => (string) $this->total_tax,
            'total_shipping'     => (string) $this->total_shipping,
            'total_price'        => (string) $this->total_price,
            'discount_codes'     => $this->discount_codes,
            'note'               => $this->note,
            'tags'               => $this->tags,
            'shipping_address'   => $this->shipping_address,
            'billing_address'    => $this->billing_address,
            'line_items'         => OrderLineItemResource::collection($this->whenLoaded('lineItems')),
            'customer'           => new CustomerResource($this->whenLoaded('customer')),
            'transactions'       => TransactionResource::collection($this->whenLoaded('transactions')),
            'fulfillments'       => FulfillmentResource::collection($this->whenLoaded('fulfillments')),
            'processed_at'       => $this->processed_at?->toISOString(),
            'cancelled_at'       => $this->cancelled_at?->toISOString(),
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),
        ];
    }
}
