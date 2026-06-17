<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    // Các thuộc tính có thể gán giá trị hàng loạt (Mass Assignment)
    protected $fillable = [
        'user_id', // ID người viết bài (khóa ngoại liên kết với bảng users)
        'title',   // Tiêu đề của bài viết
        'body',    // Nội dung bài viết
    ];

    /**
     * Mối quan hệ nhiều-1: Một bài viết thuộc về một người dùng viết bài.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ nhiều-nhiều: Một bài viết có thể gán nhiều tag (nhãn) khác nhau.
     * Liên kết qua bảng trung gian mặc định là article_tag.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
