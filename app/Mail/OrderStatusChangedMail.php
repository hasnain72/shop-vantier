<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, array{old: string|null, new: string|null}>  $changes
     */
    public function __construct(
        public readonly Order $order,
        public readonly array $changes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Order Update – #' . $this->order->order_number);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.status-changed',
            with: ['changes' => $this->changes],
        );
    }
}
