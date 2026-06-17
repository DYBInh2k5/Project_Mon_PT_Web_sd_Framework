<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AgentConversation extends Model
{
    use HasFactory;

    // Chỉ định khóa chính không tự động tăng vì dùng định dạng UUID
    public $incrementing = false;

    // Kiểu dữ liệu của khóa chính là chuỗi ký tự (UUID)
    protected $keyType = 'string';

    // Các thuộc tính có thể gán giá trị hàng loạt (Mass Assignment)
    protected $fillable = [
        'title',   // Tiêu đề cuộc hội thoại (ví dụ: Chat hỗ trợ mua sản phẩm,...)
        'user_id', // ID người dùng thực hiện cuộc trò chuyện này (có thể null nếu khách vãng lai)
    ];

    protected static function booted(): void
    {
        // Tự sinh UUID khi tạo conversation mới.
        static::creating(function (self $model): void {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function messages(): HasMany
    {
        // Một conversation có nhiều tin nhắn qua lại giữa user và assistant.
        return $this->hasMany(AgentConversationMessage::class, 'conversation_id', 'id');
    }

    public function user(): BelongsTo
    {
        // Conversation thuộc về một user đã đăng nhập.
        return $this->belongsTo(User::class);
    }
}
