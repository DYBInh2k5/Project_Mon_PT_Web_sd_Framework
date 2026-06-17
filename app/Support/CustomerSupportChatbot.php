<?php

namespace App\Support;

use App\Models\Order;
use App\Services\GeminiChatService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CustomerSupportChatbot
{
    public function __construct(
        protected GeminiChatService $geminiChatService
    ) {
    }

    /**
     * Xử lý câu hỏi của người dùng gửi tới Chatbot và trả về phản hồi kèm gợi ý.
     * Tự động bổ sung ngữ cảnh về đơn hàng nếu phát hiện mã đơn hàng trong tin nhắn.
     * Sử dụng Gemini API (hoặc Groq Llama) thông qua GeminiChatService để trả lời.
     */
    public function respond(string $message, array $history = []): array
    {
        $normalized = Str::lower(trim($message));

        if ($normalized === '') {
            return $this->fallback();
        }

        // Kiểm tra xem tin nhắn có chứa mã đơn hàng không để lấy ngữ cảnh đơn hàng
        $orderContext = $this->handleOrderLookup($message);
        $projectContext = $this->buildProjectContext();
        $boostContext = $this->buildBoostContext();
        $historyContext = $this->buildHistoryContext($history);

        // Gọi Service Gemini để lấy câu trả lời kèm các tham số ngữ cảnh
        $geminiResponse = $this->geminiChatService->answer($message, [
            'module' => 'support-chat',
            'order_context' => $orderContext,
            'project_context' => $projectContext,
            'boost_context' => $boostContext,
            'chat_history' => $historyContext,
            'answer_strategy' => 'answer_everything_with_project_context',
        ]);

        if (is_array($geminiResponse) && ! empty($geminiResponse['message'])) {
            if (is_array($orderContext)) {
                $geminiResponse['message'] = $geminiResponse['message']."\n\nThông tin đơn hàng liên quan:\n".$orderContext['message'];
            }

            return $geminiResponse;
        }

        if (is_array($orderContext)) {
            return $orderContext;
        }

        // Nếu Gemini lỗi/hết quota, sử dụng dữ liệu phản hồi nội bộ (Local fallback)
        return $this->mergeWithLocalAnswer($this->fallback(), $normalized);
    }

    /**
     * Tra cứu thông tin đơn hàng dựa trên định dạng mã ORD-XXXXX.
     */
    protected function handleOrderLookup(string $message): ?array
    {
        preg_match('/ORD-\d{5}/i', $message, $matches);
        $orderNumber = $matches[0] ?? null;

        if (! $orderNumber) {
            return null;
        }

        $order = Order::query()
            ->where('order_number', Str::upper($orderNumber))
            ->first();

        if (! $order) {
            return [
                'message' => 'Mình chưa tìm thấy mã đơn '.$orderNumber.'. Bạn thử kiểm tra lại mã đơn hoặc vào danh sách Orders để đối chiếu.',
                'suggestions' => [
                    'Mở danh sách Orders',
                    'Tìm đơn theo ngày',
                    'Đơn hàng đang xử lý bao lâu?',
                ],
            ];
        }

        return [
            'message' => 'Đơn '.$order->order_number.' của '.$order->customer_name.' hiện ở trạng thái '.Str::headline($order->status).', tổng tiền $'.number_format((float) $order->total_amount, 2).', đặt lúc '.$order->placed_at?->format('d/m/Y H:i').'.',
            'suggestions' => [
                'Cập nhật trạng thái đơn hàng',
                'Khi nào nên chuyển sang completed?',
                'Gửi mail cho khách khi đổi trạng thái',
            ],
        ];
    }

    /**
     * Xây dựng ngữ cảnh cấu trúc dự án gửi cho AI.
     */
    protected function buildProjectContext(): array
    {
        return [
            'title' => config('app.name'),
            'modules' => [
                'auth',
                'roles',
                'users',
                'profile',
                'products',
                'categories',
                'orders',
                'chatbot',
                'VNPay checkout',
                'articles',
                'tags',
            ],
            'routes' => [
                '/support-chat',
                '/orders',
                '/products',
                '/product-categories',
                '/users',
                '/articles',
                '/settings/profile',
            ],
            'key_rules' => [
                'admin manages users and profile',
                'editor manages products, categories, and orders',
                'user can access support chatbot and profile',
                'order updates send mail and record history',
                'profile avatar is stored on public disk',
            ],
        ];
    }

    /**
     * Đọc nội dung tài liệu hướng dẫn để gửi làm ngữ cảnh tham chiếu cho AI.
     */
    protected function buildBoostContext(): array
    {
        return [
            'package' => 'laravel/boost',
            'guideline_file' => '.ai/guidelines/project-chatbot.md',
            'guideline_excerpt' => $this->readContextFile('.ai/guidelines/project-chatbot.md', 2200),
            'docs_excerpt' => $this->readContextFile('docs/11-FULL-PROJECT-GUIDE.md', 2200),
            'notes' => [
                'use Boost guidance as project context',
                'keep answers consistent with docs/11-FULL-PROJECT-GUIDE.md',
                'prefer local project knowledge first, Gemini second',
            ],
        ];
    }

    /**
     * Lấy 8 tin nhắn hội thoại gần nhất để gửi làm ngữ cảnh lịch sử trò chuyện.
     */
    protected function buildHistoryContext(array $history): array
    {
        return collect($history)
            ->take(-8)
            ->map(function (array $message): array {
                return [
                    'role' => $message['role'] ?? 'unknown',
                    'content' => Str::limit((string) ($message['content'] ?? ''), 240, '...'),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Câu trả lời mặc định khi hệ thống không kết nối được AI.
     */
    protected function fallback(): array
    {
        return [
            'message' => 'Mình chưa lấy được câu trả lời từ Gemini lúc này. Bạn có thể thử hỏi lại ngắn gọn hơn hoặc gửi kèm mã đơn như ORD-00023.',
            'suggestions' => [
                'Kiểm tra đơn ORD-00023',
                'Làm sao cập nhật trạng thái đơn hàng?',
                'Mail thông báo hoạt động thế nào?',
            ],
        ];
    }

    /**
     * Hợp nhất kết quả trả về với câu trả lời từ thư viện tri thức nội bộ.
     */
    protected function mergeWithLocalAnswer(array $baseResponse, string $message): array
    {
        $local = $this->answerFromProjectKnowledge($message);

        if ($local === null) {
            return $baseResponse;
        }

        return [
            'message' => $local['message'] ?? $baseResponse['message'],
            'suggestions' => $local['suggestions'] ?? ($baseResponse['suggestions'] ?? []),
        ];
    }

    /**
     * Tri thức nội bộ về dự án để phản hồi nhanh không cần gọi API ngoài (hoặc dùng khi API lỗi).
     */
    protected function answerFromProjectKnowledge(string $message): ?array
    {
        $normalized = Str::lower($message);

        if ($this->containsAny($normalized, ['dang nhap', 'login', 'auth', 'role', 'middleware'])) {
            return [
                'message' => 'Phần auth của project dùng middleware `auth`, `guest` và middleware role tự viết `EnsureUserHasRole`. Route quan trọng sẽ được chặn theo `admin`, `editor`, `user`, nên đúng role mới vào được màn tương ứng.',
                'suggestions' => [
                    'Role admin có gì?',
                    'Middleware role hoạt động thế nào?',
                    'Xem luồng đăng nhập',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['profile', 'avatar', 'thong tin ca nhan'])) {
            return [
                'message' => 'Profile của project dùng quan hệ `User hasOne Profile`. Avatar được upload lên `storage/app/public/profiles` và hiển thị qua `public/storage`. Sau khi upload, avatar sẽ hiện ở trang profile và dropdown user trên header.',
                'suggestions' => [
                    'Avatar lưu ở đâu?',
                    'Cách update profile',
                    'User - Profile relation',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['san pham', 'product', 'danh muc', 'category'])) {
            return [
                'message' => 'Module sản phẩm và danh mục dùng Eloquent CRUD. `editor` hoặc `admin` được quản lý sản phẩm/danh mục, còn sản phẩm có ảnh upload riêng. Đây là phần dễ đem đi vấn đáp vì có đủ controller, request validation và view.',
                'suggestions' => [
                    'Quản lý sản phẩm gồm gì?',
                    'Danh mục sản phẩm có gì?',
                    'Upload ảnh sản phẩm thế nào?',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['don hang', 'order', 'trang thai', 'status', 'payment', 'mail'])) {
            return [
                'message' => 'Module đơn hàng có danh sách, chi tiết, lọc ngày, lọc trạng thái, xem khách hàng, cập nhật trạng thái và gửi mail khi đổi status. Thanh toán VNPay có luồng riêng để tạo đơn, chuyển sang cổng thanh toán, rồi cập nhật `payment_status`, `payment_method`, `transaction_code` và `paid_at` khi giao dịch thành công.',
                'suggestions' => [
                    'Xem chi tiết đơn hàng',
                    'Gửi mail khi đổi status',
                    'Thanh toán VNPay hoạt động sao?',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['article', 'tag', 'articles', 'tags'])) {
            return [
                'message' => 'Bài articles dùng Eloquent relationship: `Article belongsTo User` và `Article belongsToMany Tag`. Trang `/articles` hiển thị title, user, body, ngày tạo và tags tương ứng.',
                'suggestions' => [
                    'Quan hệ Article - Tag',
                    'Trang articles hiển thị gì?',
                    'Tại sao dùng Eloquent?',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['chatbot', 'support chat', 'gemini', 'boost'])) {
            return [
                'message' => 'Chatbot của project là module hỗ trợ khách hàng trong admin. Bot ưu tiên đọc dữ liệu đơn hàng thật khi có mã đơn, còn các câu hỏi khác sẽ được xử lý bằng Gemini nếu API còn quota; nếu Gemini lỗi, bot vẫn có câu trả lời nội bộ theo context của project.',
                'suggestions' => [
                    'Chatbot đọc dữ liệu gì?',
                    'Gemini dùng khi nào?',
                    'Boost có vai trò gì?',
                ],
            ];
        }

        return [
            'message' => 'Mình có thể giải thích theo đúng project Laravel này: auth, role, user, profile, sản phẩm, danh mục, đơn hàng, chatbot, VNPay checkout, articles và tags. Nếu bạn hỏi cụ thể hơn, mình sẽ bám đúng module đó và trả lời chi tiết hơn.',
            'suggestions' => [
                'TÃ³m táº¯t project',
                'Giáº£i thÃ­ch module orders',
                'Giáº£i thÃ­ch module profile',
            ],
        ];
    }

    protected function containsAny(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (Str::contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }

    protected function readContextFile(string $relativePath, int $limit): ?string
    {
        $path = base_path($relativePath);

        if (! File::exists($path)) {
            return null;
        }

        return Str::limit(trim(File::get($path)), $limit, '...');
    }
}


