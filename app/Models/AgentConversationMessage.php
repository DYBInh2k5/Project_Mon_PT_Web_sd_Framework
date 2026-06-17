<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AgentConversationMessage extends Model
{
    use HasFactory;

    // Tên bảng tương ứng trong cơ sở dữ liệu
    protected $table = 'agent_conversation_messages';

    // Chỉ định khóa chính dạng chuỗi UUID không tự tăng
    public $incrementing = false;

    // Kiểu dữ liệu của khóa chính
    protected $keyType = 'string';

    // Các thuộc tính có thể gán giá trị hàng loạt (Mass Assignment)
    protected $fillable = [
        'id',              // ID tin nhắn (UUID)
        'conversation_id', // ID cuộc hội thoại chứa tin nhắn này (liên kết bảng agent_conversations)
        'user_id',         // ID người dùng gửi tin nhắn (null nếu là hệ thống/AI trả lời)
        'agent',           // Tên agent xử lý tin nhắn (ví dụ: support_bot)
        'role',            // Vai trò người gửi (user hoặc model/assistant)
        'content',         // Nội dung văn bản tin nhắn
        'attachments',     // Tệp đính kèm (dạng mảng JSON)
        'tool_calls',      // Các yêu cầu gọi hàm (Function Calls) từ AI (dạng mảng JSON)
        'tool_results',    // Kết quả trả về sau khi thực thi hàm (dạng mảng JSON)
        'usage',           // Thông tin tiêu hao token (dạng mảng JSON)
        'meta',            // Dữ liệu bổ sung tùy chọn (dạng mảng JSON)
    ];

    // Tự động ép kiểu các trường JSON/Array sang dạng mảng trong PHP để dễ thao tác
    protected $casts = [
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
