<?php

namespace App\Listeners;

use App\Events\CustomerRegistered;
use App\Mail\CustomerWelcomeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendCustomerWelcomeEmail implements ShouldQueue
{
    public function handle(CustomerRegistered $event): void
    {
        $customer = $event->customer;
        Mail::to($customer->email)->send(new CustomerWelcomeMail($customer));
    }
}
