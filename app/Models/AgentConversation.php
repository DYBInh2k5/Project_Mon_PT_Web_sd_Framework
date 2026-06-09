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

    // Conversation dùng UUID để dễ đồng bộ với message và tránh đoán ID.
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'user_id',
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
