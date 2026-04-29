<?php

namespace App\Listeners;

use App\Events\CustomerInvited;
use App\Mail\CustomerInviteMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendCustomerInviteEmail implements ShouldQueue
{
    public function handle(CustomerInvited $event): void
    {
        $customer = $event->customer;
        Mail::to($customer->email)->send(new CustomerInviteMail($customer, $event->inviteUrl));
    }
}
