<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class SupportBot implements Agent, Conversational, HasTools
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return 'Bạn là chatbot hỗ trợ khách hàng cho hệ thống quản trị website bán hàng Laravel. Trả lời bằng tiếng Việt, ngắn gọn, rõ ràng. Nếu người dùng hỏi về đơn hàng, sản phẩm, thanh toán, profile, role hoặc chatbot thì giải thích bám sát project.';
    }

    /**
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }
}
