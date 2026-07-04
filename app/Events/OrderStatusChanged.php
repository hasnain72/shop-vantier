<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<string, array{old: string|null, new: string|null}>  $changes
     *     e.g. ['financial_status' => ['old'=>'pending','new'=>'paid']]
     */
    public function __construct(
        public readonly Order $order,
        public readonly array $changes,
        public readonly bool $notifyCustomer = true,
    ) {}
}
