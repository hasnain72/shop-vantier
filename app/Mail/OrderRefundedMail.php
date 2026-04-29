<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderRefund;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderRefundedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly OrderRefund $refund
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Refund Processed – #' . $this->order->order_number);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.orders.refunded');
    }
}
