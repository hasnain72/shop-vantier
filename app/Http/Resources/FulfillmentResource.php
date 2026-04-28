<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FulfillmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'status'           => $this->status,
            'tracking_company' => $this->tracking_company,
            'tracking_number'  => $this->tracking_number,
            'tracking_url'     => $this->tracking_url,
            'shipment_status'  => $this->shipment_status,
            'notify_customer'  => (bool) $this->notify_customer,
            'created_at'       => $this->created_at?->toISOString(),
        ];
    }
}
