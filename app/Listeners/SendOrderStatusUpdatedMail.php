<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Mail\OrderStatusUpdatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendOrderStatusUpdatedMail implements ShouldQueue
{
    public function handle(OrderStatusUpdated $event): void
    {
        // Listener được đưa vào hàng đợi (Queue) để tách tác vụ gửi mail ra khỏi luồng xử lý chính của Controller/Service.
        // Khối Try/Catch giúp quá trình cập nhật trạng thái đơn hàng không bị lỗi hoặc gián đoạn nếu hệ thống gửi email gặp sự cố môi trường.
        try {
            Mail::to($event->order->customer_email)
                ->send(new OrderStatusUpdatedMail($event->order, $event->previousStatus));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
