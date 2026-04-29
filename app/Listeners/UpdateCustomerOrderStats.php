<?php

namespace App\Listeners;

use App\Events\OrderCreated;

class UpdateCustomerOrderStats
{
    public function handle(OrderCreated $event): void
    {
        $order    = $event->order;
        $customer = $order->customer;

        if (!$customer) {
            return;
        }

        $customer->increment('orders_count');
        $customer->increment('total_spent', $order->total_price);
    }
}
