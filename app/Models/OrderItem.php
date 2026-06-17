<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    // Các thuộc tính có thể gán giá trị hàng loạt (Mass Assignment)
    protected $fillable = [
        'order_id',     // ID đơn hàng chứa sản phẩm này (khóa ngoại liên kết bảng orders)
        'product_id',    // ID sản phẩm được mua (khóa ngoại liên kết bảng products, có thể null nếu sản phẩm bị xóa)
        'product_name',  // Lưu lại tên sản phẩm tại thời điểm mua (để tránh mất thông tin khi sản phẩm đổi tên)
        'quantity',      // Số lượng mua của sản phẩm này
        'unit_price',    // Đơn giá sản phẩm tại thời điểm đặt hàng
        'line_total',    // Thành tiền (bằng unit_price * quantity)
    ];

    // Ép kiểu các thuộc tính số lượng và giá tiền sang định dạng thích hợp
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    /**
     * Mối quan hệ nhiều-1: Một chi tiết đơn hàng thuộc về một đơn hàng cụ thể.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Mối quan hệ nhiều-1: Một chi tiết đơn hàng liên kết tới một sản phẩm trong kho.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
