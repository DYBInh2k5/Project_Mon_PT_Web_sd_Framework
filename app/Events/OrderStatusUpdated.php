<?php

namespace App\Events;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated
{
    use Dispatchable, SerializesModels;

    // Event này chứa dữ liệu cần thiết để Listener gửi mail và/hoặc xử lý thêm sau này.
    // Ví dụ: thông tin đơn hàng hiện tại, trạng thái cũ, và bản ghi lịch sử vừa được tạo.
    public function __construct(
        public Order $order,
        public string $previousStatus,
        public OrderStatusHistory $history,
    ) {
    }
}
