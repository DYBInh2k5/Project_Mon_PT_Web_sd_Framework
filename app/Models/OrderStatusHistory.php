<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    use HasFactory;

    // Các thuộc tính có thể gán giá trị hàng loạt (Mass Assignment)
    protected $fillable = [
        'order_id',        // ID đơn hàng bị thay đổi trạng thái (khóa ngoại liên kết bảng orders)
        'changed_by',      // ID người thực hiện thay đổi (khóa ngoại liên kết bảng users, có thể null nếu tự động)
        'previous_status', // Trạng thái trước khi thay đổi (ví dụ: pending)
        'new_status',      // Trạng thái mới được thiết lập (ví dụ: processing)
        'note',            // Ghi chú lý do thay đổi trạng thái (ví dụ: khách yêu cầu hủy, đã nhận thanh toán,...)
    ];

    public function order(): BelongsTo
    {
        // Quan hệ thuộc về (1-N): Mỗi dòng lịch sử thuộc về một đơn hàng cụ thể.
        return $this->belongsTo(Order::class);
    }

    public function changer(): BelongsTo
    {
        // Quan hệ thuộc về (1-N): changed_by trỏ về người dùng thực hiện thay đổi trạng thái.
        // Giá trị này có thể bằng null nếu hệ thống tự động thay đổi (ví dụ: qua cổng thanh toán IPN).
        return $this->belongsTo(User::class, 'changed_by');
    }
}
