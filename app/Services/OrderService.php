<?php

namespace App\Services;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function updateStatus(Order $order, string $newStatus, ?User $changedBy = null, ?string $note = null): ?OrderStatusHistory
    {
        $previousStatus = $order->status;

        // Neu status khong doi thi khong ghi history va khong gui mail.
        if ($previousStatus === $newStatus) {
            return null;
        }

        // Transaction dam bao update bang orders va ghi history di cung nhau.
        // Neu mot buoc loi, database se rollback de khong bi lech du lieu.
        $history = DB::transaction(function () use ($order, $newStatus, $changedBy, $note, $previousStatus): OrderStatusHistory {
            $order->update([
                'status' => $newStatus,
            ]);

            return $order->statusHistories()->create([
                'changed_by' => $changedBy?->id,
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'note' => $note,
            ]);
        });

        // Sau khi database da cap nhat xong moi phat event.
        // Listener se nhan event nay de gui mail thong bao cho khach hang.
        event(new OrderStatusUpdated($order->refresh(), $previousStatus, $history));

        return $history;
    }
}
