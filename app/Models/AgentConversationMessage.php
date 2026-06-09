<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AgentConversationMessage extends Model
{
    use HasFactory;

    // Bảng messages lưu từng câu hỏi và từng câu trả lời của chatbot.
    protected $table = 'agent_conversation_messages';

    // Message cũng dùng UUID để dễ truy vết và đồng bộ với conversation.
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'conversation_id',
        'user_id',
        'agent',
        'role',
        'content',
        'attachments',
        'tool_calls',
        'tool_results',
        'usage',
        'meta',
    ];

    protected $casts = [
        // Các cột này được lưu dạng mảng/JSON để có thể mở rộng khi dùng tool hoặc metadata.
        'attachments' => 'array',
        'tool_calls' => 'array',
        'tool_results' => 'array',
        'usage' => 'array',
        'meta' => 'array',
    ];

    protected static function booted(): void
    {
        // Tự sinh UUID khi tạo message mới.
        static::creating(function (self $model): void {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function conversation(): BelongsTo
    {
        // Mỗi message thuộc về đúng một conversation.
        return $this->belongsTo(AgentConversation::class, 'conversation_id', 'id');
    }

    public function user(): BelongsTo
    {
        // Ghi lại người đã gửi message.
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
