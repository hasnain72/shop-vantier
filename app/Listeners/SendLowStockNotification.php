<?php

namespace App\Listeners;

use App\Events\LowStockAlert;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendLowStockNotification
{
    public function handle(LowStockAlert $event): void
    {
        $level   = $event->level;
        $variant = $level->inventoryItem?->variant;
        $product = $variant?->product;

        if (! $variant || ! $product) return;

        $admins = User::role('super_admin')->get();

        foreach ($admins as $admin) {
            // Mail::to($admin->email)->send(new \App\Mail\LowStockAlert($product, $variant, $level));
            // Placeholder: log warning instead
            \Illuminate\Support\Facades\Log::warning("Low stock alert", [
                'product' => $product->title,
                'variant' => $variant->title,
                'sku'     => $variant->sku,
                'qty'     => $level->available,
            ]);
        }
    }
}
