<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use HasFactory;

    // Các thuộc tính có thể gán giá trị hàng loạt (Mass Assignment)
    protected $fillable = [
        'name',        // Tên danh mục sản phẩm (ví dụ: Điện thoại, Máy tính bảng, Laptop,...)
        'slug',        // Slug đường dẫn thân thiện (phục vụ SEO URL)
        'description', // Mô tả danh mục
        'is_active',   // Trạng thái kích hoạt (cho phép hiển thị danh mục hay không)
        'created_by',  // ID của người dùng tạo danh mục này (liên kết khóa ngoại đến bảng users)
    ];

    // Ép kiểu dữ liệu tự động khi Eloquent truy xuất thuộc tính
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Mối quan hệ nhiều-1: Một danh mục được tạo bởi một người dùng cụ thể.
     * Khóa ngoại created_by liên kết đến id của bảng users.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Mối quan hệ 1-nhiều: Một danh mục có thể chứa nhiều sản phẩm.
     * Laravel mặc định tìm khóa ngoại là product_category_id trên bảng products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
