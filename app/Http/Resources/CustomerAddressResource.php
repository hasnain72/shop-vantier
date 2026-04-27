<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company' => $this->company,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'city' => $this->city,
            'province' => $this->province,
            'province_code' => $this->province_code,
            'country' => $this->country,
            'country_code' => $this->country_code,
            'zip' => $this->zip,
            'phone' => $this->phone,
            'is_default' => (bool) $this->is_default,
            'name' => trim(($this->first_name ?? '').' '.($this->last_name ?? '')),
        ];
    }
}

