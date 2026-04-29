<?php

namespace App\Listeners;

use App\Events\OrderFulfilled;
use App\Mail\OrderShippedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderFulfilledEmail implements ShouldQueue
{
    public function handle(OrderFulfilled $event): void
    {
        $order = $event->order;

        if (!$order->email) {
            return;
        }

        Mail::to($order->email)->send(new OrderShippedMail($order, $event->fulfillment));
    }
}
