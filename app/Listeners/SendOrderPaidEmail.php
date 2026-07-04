<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Mail\OrderPaidMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderPaidEmail implements ShouldQueue
{
    public function handle(OrderPaid $event): void
    {
        $order = $event->order;

        if (!$order->email) {
            return;
        }

        Mail::to($order->email)->send(new OrderPaidMail($order));
    }
}
