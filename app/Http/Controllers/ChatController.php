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
        $request->validate([
            'message' => 'required',
        ]);

        try {
            $conversation = AgentConversation::firstOrCreate([
                'user_id' => auth()->id(),
                'title' => 'New Conversation',
            ]);

            AgentConversationMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'user',
                'user_id' => auth()->id(),
                'content' => $request->message,
                'agent' => 'SupportBot',
            ]);

            $agent = app(SupportBot::class);
            $response = $agent->prompt($request->message);

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
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
