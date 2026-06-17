<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    // Các thuộc tính có thể gán giá trị hàng loạt (Mass Assignment)
    protected $fillable = [
        'tag', // Nhãn bài viết (ví dụ: Laravel, PHP, Frontend,...)
    ];

    /**
     * Mối quan hệ nhiều-nhiều: Một tag có thể thuộc về nhiều bài viết.
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }
}
