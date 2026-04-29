<?php

namespace App\Mail;

use App\Models\InventoryLevel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly InventoryLevel $level) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Low Stock Alert: ' . ($this->level->variant->product->title ?? 'Product'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin.low-stock');
    }
}
