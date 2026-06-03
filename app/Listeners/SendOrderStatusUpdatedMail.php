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
        // Listener duoc queue de tach viec gui mail khoi controller/service.
        // Try/catch giup thao tac cap nhat don hang khong bi hong neu mail/log co loi moi truong.
        try {
            Mail::to($event->order->customer_email)
                ->send(new OrderStatusUpdatedMail($event->order, $event->previousStatus));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
