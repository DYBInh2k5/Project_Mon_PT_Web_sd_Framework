<?php

namespace App\Http\Controllers;

use App\Ai\Agents\SupportBot;
use App\Models\AgentConversation;
use App\Models\AgentConversationMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ChatController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // Luôn validate message trước khi xử lý để tránh lưu dữ liệu rỗng vào database.
        $request->validate([
            'message' => 'required',
        ]);

        try {
            // Mỗi người dùng có một conversation riêng để lưu toàn bộ lịch sử chat.
            $conversation = AgentConversation::firstOrCreate([
                'user_id' => auth()->id(),
                'title' => 'New Conversation',
            ]);

            // Lưu tin nhắn của user trước, sau đó mới gọi agent để sinh phản hồi.
            AgentConversationMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'user',
                'user_id' => auth()->id(),
                'content' => $request->message,
                'agent' => 'SupportBot',
            ]);

            $agent = app(SupportBot::class);
            $response = $agent->prompt($request->message);

            // Lưu lại câu trả lời của assistant để có thể xem lại lịch sử hội thoại sau này.
            AgentConversationMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $response->text,
                'user_id' => auth()->id(),
                'agent' => 'SupportBot',
                'usage' => $response->usage->toArray(),
                'meta' => $response->meta->toArray(),
                'tool_calls' => $response->toolCalls->toArray(),
                'tool_results' => $response->toolResults->toArray(),
            ]);

            return response()->json([
                'message' => $response->text,
            ]);
        } catch (Throwable $e) {
            // Trả lỗi dạng JSON để giao diện chat hiển thị được thông báo rõ ràng.
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
