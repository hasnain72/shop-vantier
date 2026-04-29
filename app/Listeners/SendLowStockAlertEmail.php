<?php

namespace App\Listeners;

use App\Events\LowStockAlert;
use App\Mail\LowStockAlertMail;
use App\Models\StoreSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendLowStockAlertEmail implements ShouldQueue
{
    public function handle(LowStockAlert $event): void
    {
        $to = StoreSetting::where('key', 'notification_email')->value('value')
            ?? config('mail.from.address');

        if (!$to) {
            return;
        }

        Mail::to($to)->send(new LowStockAlertMail($event->level));
    }
}
