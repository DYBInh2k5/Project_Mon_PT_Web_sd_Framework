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

    /**
     * Khởi tạo mailable nhận vào đối tượng đơn hàng và trạng thái trước đó.
     */
    public function __construct(
        public Order $order,
        public string $previousStatus,
    ) {
    }

    /**
     * Thiết lập Envelope của email bao gồm tiêu đề (subject).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cập nhật trạng thái đơn hàng ' . $this->order->order_number,
        );
    }

    /**
     * Thiết lập Content của email bao gồm đường dẫn tới Blade view hiển thị nội dung email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.status-updated',
        );
    }
}
