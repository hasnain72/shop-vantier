<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use App\Mail\OrderCancelledMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderCancelledEmail implements ShouldQueue
{
    public function handle(OrderCancelled $event): void
    {
        $order = $event->order;

        if (!$order->email) {
            return;
        }

        Mail::to($order->email)->send(new OrderCancelledMail($order));
    }
}
