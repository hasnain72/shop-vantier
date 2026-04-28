<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'price'     => (float) $this->price,
            'currency'  => config('app.currency', 'USD'),
            'rate_type' => $this->rate_type,
            'zone'      => $this->whenLoaded('zone', fn () => [
                'id'   => $this->zone->id,
                'name' => $this->zone->name,
            ]),
        ];
    }
}
