<?php

namespace App\Ai\Agents;

use App\Ai\Tools\GetProductDetails;
use App\Ai\Tools\ListCategories;
use App\Ai\Tools\SearchProducts;
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

    /**
     * Định nghĩa System Instructions (chỉ dẫn hệ thống) cho Agent AI.
     * Đây là những hướng dẫn bắt buộc mô hình phải tuân theo khi tương tác với người dùng.
     */
    public function instructions(): Stringable|string
    {
        return 'Bạn là chatbot hỗ trợ khách hàng cho hệ thống quản trị website bán hàng Laravel. Trả lời bằng tiếng Việt, ngắn gọn, rõ ràng. Nếu người dùng hỏi về đơn hàng, sản phẩm, thanh toán, profile, role hoặc chatbot thì giải thích bám sát project. Bạn có các công cụ để tìm kiếm và liệt kê sản phẩm/danh mục trực tiếp trong hệ thống.';
    }

    /**
     * Danh sách các tin nhắn ngữ cảnh ban đầu (nếu có).
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Đăng ký các công cụ (Tools) mà Agent được quyền gọi thông qua cơ chế Function Calling của AI.
     * Khi người dùng hỏi thông tin cần truy vấn dữ liệu thực tế trong DB, AI sẽ tự động chọn gọi các Tool này.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new SearchProducts(),
            new GetProductDetails(),
            new ListCategories(),
        ];
    }
}

