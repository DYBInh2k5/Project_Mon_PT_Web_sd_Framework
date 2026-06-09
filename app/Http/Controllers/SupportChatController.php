<?php

namespace App\Http\Controllers;

use App\Models\AgentConversation;
use App\Models\AgentConversationMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportChatController extends Controller
{
    public function index(Request $request): View
    {
        // Tạo hoặc lấy conversation hiện tại của người dùng để hiển thị lịch sử chat.
        $conversation = AgentConversation::firstOrCreate([
            'user_id' => $request->user()->id,
            'title' => 'New Conversation',
        ]);

        // Chuyển dữ liệu Eloquent sang mảng đơn giản để view render bằng Alpine.
        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get()
            ->map(function (AgentConversationMessage $message): array {
                return [
                    'role' => $message->role,
                    'content' => $message->content,
                    'agent' => $message->agent,
                    'suggestions' => data_get($message->meta, 'suggestions', []),
                ];
            })
            ->values()
            ->all();

        return view('support.chat', [
            'title' => 'Customer Support Chatbot',
            'conversationId' => $conversation->id,
            'messages' => $messages !== [] ? $messages : $this->defaultMessages(),
            'quickPrompts' => [
                'Kiem tra don ORD-00023',
                'Lam sao cap nhat trang thai don hang?',
                'Mail thong bao hoat dong the nao?',
                'Khach muon huy don thi xu ly sao?',
            ],
        ]);
    }

    public function clear(Request $request): RedirectResponse
    {
        // Xóa toàn bộ conversation của user hiện tại để bắt đầu một phiên chat mới.
        AgentConversation::where('user_id', $request->user()->id)->delete();

        return redirect()
            ->route('support-chat.index')
            ->with('success', 'Đã xóa lịch sử hội thoại chatbot.');
    }

    protected function defaultMessages(): array
    {
        return [
            [
                'role' => 'assistant',
                'content' => 'Xin chào, mình là chatbot hỗ trợ khách hàng của hệ thống bán hàng. Bạn có thể hỏi về đơn hàng, giao hàng, huỷ đơn, danh mục sản phẩm hoặc mail thông báo.',
                'suggestions' => [
                    'Kiem tra don ORD-00023',
                    'Don hang dang xu ly bao lau?',
                    'Khi doi trang thai co gui mail khong?',
                ],
            ],
        ];
    }
}
