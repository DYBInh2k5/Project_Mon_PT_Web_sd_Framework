<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $previousStatus,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order '.$this->order->order_number.' status updated',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.status-updated',
        );
    }
}
