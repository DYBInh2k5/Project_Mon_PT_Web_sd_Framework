<?php

namespace App\Events;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated
{
    use Dispatchable, SerializesModels;

    // Event nay gom du lieu can thiet de listener gui mail va/hoac xu ly them sau nay.
    // Vi du: order hien tai, status cu, va dong history vua duoc tao.
    public function __construct(
        public Order $order,
        public string $previousStatus,
        public OrderStatusHistory $history,
    ) {
    }
}
