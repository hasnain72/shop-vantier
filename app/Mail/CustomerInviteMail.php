<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Customer $customer,
        public readonly string $inviteUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'You\'ve been invited to create an account');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.customers.invite');
    }
}
