<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'kind'         => $this->kind,
            'gateway'      => $this->gateway,
            'status'       => $this->status,
            'amount'       => (string) $this->amount,
            'currency'     => $this->currency,
            'message'      => $this->message,
            'processed_at' => $this->processed_at?->toISOString(),
            'created_at'   => $this->created_at?->toISOString(),
        ];
    }
}
