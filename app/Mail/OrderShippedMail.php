<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderFulfillment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderShippedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly OrderFulfillment $fulfillment
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Order Has Shipped – #' . $this->order->order_number);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.orders.shipped');
    }
}
