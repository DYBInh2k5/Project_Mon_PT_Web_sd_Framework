<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    // Các hằng số định nghĩa trạng thái của đơn hàng trong hệ thống
    public const STATUSES = [
        'pending',    // Chờ xử lý (mới đặt hàng)
        'processing', // Đang xử lý (đang đóng gói/chuẩn bị gửi)
        'shipped',    // Đang giao hàng
        'completed',  // Đã hoàn thành (giao thành công)
        'cancelled',  // Đã hủy đơn
    ];

    // Các hằng số định nghĩa trạng thái thanh toán của đơn hàng
    public const PAYMENT_STATUSES = [
        'unpaid', // Chưa thanh toán
        'paid',   // Đã thanh toán thành công
    ];

    // Các thuộc tính cho phép gán dữ liệu hàng loạt
    protected $fillable = [
        'order_number',      // Mã đơn hàng duy nhất (ví dụ: ORD-20260617-1234)
        'customer_name',     // Tên khách hàng mua hàng
        'customer_email',    // Email khách hàng nhận hóa đơn/thông báo
        'customer_phone',    // Số điện thoại khách hàng
        'customer_address',  // Địa chỉ giao hàng
        'notes',             // Ghi chú của khách hàng khi đặt hàng
        'status',            // Trạng thái đơn hàng (mặc định pending)
        'payment_status',    // Trạng thái thanh toán (mặc định unpaid)
        'payment_method',    // Phương thức thanh toán (cod hoặc vnpay)
        'transaction_code',  // Mã giao dịch từ cổng thanh toán đối tác (nếu có)
        'paid_at',           // Thời gian thanh toán thành công
        'total_amount',      // Tổng giá trị của đơn hàng
        'placed_at',         // Thời điểm đơn hàng được khởi tạo thành công
    ];

    // Ép kiểu các thuộc tính sang định dạng thích hợp
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'placed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Mối quan hệ 1-nhiều: Một đơn hàng có nhiều chi tiết đơn hàng (các sản phẩm được mua).
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Mối quan hệ 1-nhiều: Một đơn hàng có thể có nhiều bản ghi lịch sử thay đổi trạng thái.
     * Phục vụ ghi nhận log để hiển thị quá trình xử lý đơn hàng.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * Local Query Scope: Hỗ trợ tìm kiếm đơn hàng theo mã đơn hàng, tên, email hoặc số điện thoại.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function (Builder $nested) use ($search) {
            $nested->where('order_number', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%")
                ->orWhere('customer_email', 'like', "%{$search}%")
                ->orWhere('customer_phone', 'like', "%{$search}%");
        });
    }

    /**
     * Local Query Scope: Lọc đơn hàng theo trạng thái cụ thể.
     * Giúp Controller gọi ->status($status) dễ dàng mà không cần lặp lại điều kiện if/else.
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    /**
     * Local Query Scope: Lọc đơn hàng đặt từ ngày cụ thể trở về sau.
     */
    public function scopePlacedFrom(Builder $query, mixed $date): Builder
    {
        return $date ? $query->whereDate('placed_at', '>=', $date) : $query;
    }

    /**
     * Local Query Scope: Lọc đơn hàng đặt trước hoặc bằng ngày cụ thể.
     */
    public function scopePlacedUntil(Builder $query, mixed $date): Builder
    {
        return $date ? $query->whereDate('placed_at', '<=', $date) : $query;
    }
}
