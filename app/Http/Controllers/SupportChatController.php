<?php

namespace App\Http\Controllers;

use App\Support\CustomerSupportChatbot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportChatController extends Controller
{
    public function index(Request $request): View
    {
        return view('support.chat', [
            'title' => 'Customer Support Chatbot',
            'messages' => $request->session()->get('support_chat.messages', $this->defaultMessages()),
            'quickPrompts' => [
                'Kiểm tra đơn ORD-00023',
                'Làm sao cập nhật trạng thái đơn hàng?',
                'Mail thông báo hoạt động thế nào?',
                'Khách muốn hủy đơn thì xử lý sao?',
            ],
        ]);
    }

    public function store(Request $request, CustomerSupportChatbot $chatbot): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $messages = $request->session()->get('support_chat.messages', $this->defaultMessages());
        $messages[] = [
            'role' => 'user',
            'content' => trim($validated['message']),
        ];

        $response = $chatbot->respond(
            $validated['message'],
            $request->session()->get('support_chat.messages', $this->defaultMessages())
        );

        $messages[] = [
            'role' => 'bot',
            'content' => $response['message'],
            'suggestions' => $response['suggestions'] ?? [],
        ];

        $request->session()->put('support_chat.messages', array_slice($messages, -14));

        return redirect()->route('support-chat.index');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('support_chat.messages');

        return redirect()
            ->route('support-chat.index')
            ->with('success', 'Da xoa lich su hoi thoai chatbot.');
    }

    protected function defaultMessages(): array
    {
        return [
            [
                'role' => 'bot',
                'content' => 'Xin chào, mình là chatbot hỗ trợ khách hàng của hệ thống bán hàng. Bạn có thể hỏi về đơn hàng, giao hàng, hủy đơn, danh mục sản phẩm hoặc mail thông báo.',
                'suggestions' => [
                    'Kiểm tra đơn ORD-00023',
                    'Đơn hàng đang xử lý bao lâu?',
                    'Khi đổi trạng thái có gửi mail không?',
                ],
            ],
        ];
    }
}
