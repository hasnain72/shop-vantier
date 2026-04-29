<?php

namespace App\Listeners;

use App\Events\OrderRefunded;
use App\Mail\OrderRefundedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderRefundedEmail implements ShouldQueue
{
    public function handle(OrderRefunded $event): void
    {
        $order = $event->order;

        if (!$order->email) {
            return;
        }

        Mail::to($order->email)->send(new OrderRefundedMail($order, $event->refund));
    }
}
