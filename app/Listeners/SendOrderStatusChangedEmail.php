<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Mail\OrderStatusChangedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusChangedEmail implements ShouldQueue
{
    public function handle(OrderStatusChanged $event): void
    {
        if (!$event->notifyCustomer) {
            return;
        }

        $order = $event->order;

        if (!$order->email || empty($event->changes)) {
            return;
        }

        Mail::to($order->email)->send(new OrderStatusChangedMail($order, $event->changes));
    }
}
