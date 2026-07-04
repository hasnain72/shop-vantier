<?php

namespace App\Providers;

use App\Events\CustomerInvited;
use App\Events\CustomerRegistered;
use App\Events\LowStockAlert;
use App\Events\OrderCancelled;
use App\Events\OrderCreated;
use App\Events\OrderFulfilled;
use App\Events\OrderPaid;
use App\Events\OrderRefunded;
use App\Events\OrderStatusChanged;
use App\Listeners\LogOrderActivity;
use App\Listeners\SendCustomerInviteEmail;
use App\Listeners\SendCustomerWelcomeEmail;
use App\Listeners\SendLowStockAlertEmail;
use App\Listeners\SendOrderCancelledEmail;
use App\Listeners\SendOrderConfirmationEmail;
use App\Listeners\SendOrderFulfilledEmail;
use App\Listeners\SendOrderPaidEmail;
use App\Listeners\SendOrderRefundedEmail;
use App\Listeners\SendOrderStatusChangedEmail;
use App\Listeners\TriggerOrderWebhook;
use App\Listeners\UpdateCustomerOrderStats;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderCreated::class => [
            SendOrderConfirmationEmail::class,
            UpdateCustomerOrderStats::class,
            TriggerOrderWebhook::class,
            LogOrderActivity::class,
        ],
        OrderFulfilled::class => [
            SendOrderFulfilledEmail::class,
            TriggerOrderWebhook::class,
            LogOrderActivity::class,
        ],
        OrderCancelled::class => [
            SendOrderCancelledEmail::class,
            TriggerOrderWebhook::class,
            LogOrderActivity::class,
        ],
        OrderRefunded::class => [
            SendOrderRefundedEmail::class,
            TriggerOrderWebhook::class,
            LogOrderActivity::class,
        ],
        OrderPaid::class => [
            SendOrderPaidEmail::class,
            LogOrderActivity::class,
        ],
        OrderStatusChanged::class => [
            SendOrderStatusChangedEmail::class,
            LogOrderActivity::class,
        ],
        CustomerRegistered::class => [
            SendCustomerWelcomeEmail::class,
        ],
        CustomerInvited::class => [
            SendCustomerInviteEmail::class,
        ],
        LowStockAlert::class => [
            SendLowStockAlertEmail::class,
        ],
    ];
}
