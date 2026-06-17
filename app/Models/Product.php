<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    // Các thuộc tính có thể gán giá trị hàng loạt (Mass Assignment)
    protected $fillable = [
        'product_category_id', // ID danh mục sản phẩm (khóa ngoại liên kết bảng product_categories)
        'name',                // Tên sản phẩm
        'slug',                // Slug đường dẫn thân thiện (SEO URL)
        'sku',                 // Mã định danh lưu kho duy nhất (Stock Keeping Unit)
        'price',               // Giá bán sản phẩm
        'stock',               // Số lượng sản phẩm còn lại trong kho
        'description',         // Mô tả chi tiết sản phẩm
        'image_path',          // Đường dẫn lưu trữ file ảnh sản phẩm trong thư mục storage
        'is_active',           // Trạng thái kích hoạt (cho phép hiển thị trên shop hay không)
        'created_by',          // ID của admin/editor đã tạo sản phẩm này (khóa ngoại liên kết bảng users)
    ];

    // Ép kiểu dữ liệu tự động khi Eloquent truy xuất thuộc tính từ cơ sở dữ liệu
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Mối quan hệ nhiều-1: Một sản phẩm phải thuộc về một danh mục sản phẩm.
     * Khóa ngoại là product_category_id liên kết sang id của bảng product_categories.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    /**
     * Mối quan hệ nhiều-1: Một sản phẩm được tạo bởi một người dùng (thường là Admin hoặc Editor).
     * Khóa ngoại là created_by liên kết sang id của bảng users.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Mối quan hệ 1-nhiều: Một sản phẩm có thể xuất hiện trong nhiều chi tiết đơn hàng (OrderItem).
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Hàm helper lấy URL đầy đủ của ảnh sản phẩm để hiển thị trên giao diện.
     * Sử dụng Storage facade để sinh ra link public trỏ vào thư mục lưu trữ thực tế.
     */
    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }
}
