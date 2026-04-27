<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'accepts_marketing' => (bool) $this->accepts_marketing,
            'currency' => $this->currency,
            'locale' => $this->locale,
            'orders_count' => (int) $this->orders_count,
            'total_spent' => (string) $this->total_spent,
            'created_at' => optional($this->created_at)?->toISOString(),
            'addresses' => CustomerAddressResource::collection($this->whenLoaded('addresses')),
        ];
    }
}

