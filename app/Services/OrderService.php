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

        // Nếu trạng thái không đổi thì không ghi lịch sử và không phát event.
        if ($previousStatus === $newStatus) {
            return null;
        }

        // Transaction đảm bảo cập nhật bảng orders và ghi lịch sử đi cùng nhau.
        // Nếu một bước lỗi, database sẽ rollback để không bị lệch dữ liệu.
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

        // Sau khi database đã cập nhật xong mới phát event.
        // Listener sẽ nhận event này để gửi mail thông báo cho khách hàng.
        event(new OrderStatusUpdated($order->refresh(), $previousStatus, $history));

        return $history;
    }
}
