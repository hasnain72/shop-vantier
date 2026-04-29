<?php

namespace App\Events;

use App\Models\Order;
use App\Models\OrderFulfillment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderFulfilled
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly OrderFulfillment $fulfillment
    ) {}
}
