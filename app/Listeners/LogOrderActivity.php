<?php

namespace App\Listeners;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class LogOrderActivity
{
    public function handle(object $event): void
    {
        $order = $event->order ?? null;

        if (!$order instanceof Order) {
            return;
        }

        $action = class_basename($event);

        Log::channel('stack')->info("Order event: {$action}", [
            'order_id'     => $order->id,
            'order_number' => $order->order_number,
            'email'        => $order->email,
        ]);
    }
}
