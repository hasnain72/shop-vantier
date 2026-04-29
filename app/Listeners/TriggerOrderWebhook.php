<?php

namespace App\Listeners;

use App\Jobs\SendWebhookJob;
use App\Services\WebhookService;
use Illuminate\Contracts\Queue\ShouldQueue;

class TriggerOrderWebhook implements ShouldQueue
{
    public function __construct(private readonly WebhookService $webhookService) {}

    public function handle(object $event): void
    {
        $topic = match (true) {
            $event instanceof \App\Events\OrderCreated   => 'orders/create',
            $event instanceof \App\Events\OrderFulfilled => 'orders/fulfilled',
            $event instanceof \App\Events\OrderCancelled => 'orders/cancelled',
            $event instanceof \App\Events\OrderRefunded  => 'orders/refunded',
            default => null,
        };

        if (!$topic) {
            return;
        }

        $order = $event->order;
        $this->webhookService->dispatch($topic, [
            'id'           => $order->id,
            'order_number' => $order->order_number,
            'email'        => $order->email,
            'total_price'  => $order->total_price,
            'financial_status'  => $order->financial_status,
            'fulfillment_status' => $order->fulfillment_status,
        ]);
    }
}
