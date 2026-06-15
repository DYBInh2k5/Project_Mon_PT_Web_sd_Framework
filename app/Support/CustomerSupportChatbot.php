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

    public function respond(string $message, array $history = []): array
    {
        $normalized = Str::lower(trim($message));

        if ($normalized === '') {
            return $this->fallback();
        }

        $orderContext = $this->handleOrderLookup($message);
        $projectContext = $this->buildProjectContext();
        $boostContext = $this->buildBoostContext();
        $historyContext = $this->buildHistoryContext($history);

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
                $geminiResponse['message'] = $geminiResponse['message']."\n\nThÃ´ng tin Ä‘Æ¡n hÃ ng liÃªn quan:\n".$orderContext['message'];
            }

            return $geminiResponse;
        }

        if (is_array($orderContext)) {
            return $orderContext;
        }

        return $this->mergeWithLocalAnswer($this->fallback(), $normalized);
    }

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
                'message' => 'MÃ¬nh chÆ°a tÃ¬m tháº¥y mÃ£ Ä‘Æ¡n '.$orderNumber.'. Báº¡n thá»­ kiá»ƒm tra láº¡i mÃ£ Ä‘Æ¡n hoáº·c vÃ o danh sÃ¡ch Orders Ä‘á»ƒ Ä‘á»‘i chiáº¿u.',
                'suggestions' => [
                    'Má»Ÿ danh sÃ¡ch Orders',
                    'TÃ¬m Ä‘Æ¡n theo ngÃ y',
                    'ÄÆ¡n hÃ ng Ä‘ang xá»­ lÃ½ bao lÃ¢u?',
                ],
            ];
        }

        return [
            'message' => 'ÄÆ¡n '.$order->order_number.' cá»§a '.$order->customer_name.' hiá»‡n á»Ÿ tráº¡ng thÃ¡i '.Str::headline($order->status).', tá»•ng tiá»n $'.number_format((float) $order->total_amount, 2).', Ä‘áº·t lÃºc '.$order->placed_at?->format('d/m/Y H:i').'.',
            'suggestions' => [
                'Cáº­p nháº­t tráº¡ng thÃ¡i Ä‘Æ¡n hÃ ng',
                'Khi nÃ o nÃªn chuyá»ƒn sang completed?',
                'Gá»­i mail cho khÃ¡ch khi Ä‘á»•i tráº¡ng thÃ¡i',
            ],
        ];
    }

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

    protected function fallback(): array
    {
        return [
            'message' => 'MÃ¬nh chÆ°a láº¥y Ä‘Æ°á»£c cÃ¢u tráº£ lá»i tá»« Gemini lÃºc nÃ y. Báº¡n cÃ³ thá»ƒ thá»­ há»i láº¡i ngáº¯n gá»n hÆ¡n hoáº·c gá»­i kÃ¨m mÃ£ Ä‘Æ¡n nhÆ° ORD-00023.',
            'suggestions' => [
                'Kiá»ƒm tra Ä‘Æ¡n ORD-00023',
                'LÃ m sao cáº­p nháº­t tráº¡ng thÃ¡i Ä‘Æ¡n hÃ ng?',
                'Mail thÃ´ng bÃ¡o hoáº¡t Ä‘á»™ng tháº¿ nÃ o?',
            ],
        ];
    }

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

    protected function answerFromProjectKnowledge(string $message): ?array
    {
        $normalized = Str::lower($message);

        if ($this->containsAny($normalized, ['dang nhap', 'login', 'auth', 'role', 'middleware'])) {
            return [
                'message' => 'Pháº§n auth cá»§a project dÃ¹ng middleware `auth`, `guest` vÃ  middleware role tá»± viáº¿t `EnsureUserHasRole`. Route quan trá»ng sáº½ Ä‘Æ°á»£c cháº·n theo `admin`, `editor`, `user`, nÃªn Ä‘Ãºng role má»›i vÃ o Ä‘Æ°á»£c mÃ n tÆ°Æ¡ng á»©ng.',
                'suggestions' => [
                    'Role admin cÃ³ gÃ¬?',
                    'Middleware role hoáº¡t Ä‘á»™ng tháº¿ nÃ o?',
                    'Xem luá»“ng Ä‘Äƒng nháº­p',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['profile', 'avatar', 'thong tin ca nhan'])) {
            return [
                'message' => 'Profile cá»§a project dÃ¹ng quan há»‡ `User hasOne Profile`. Avatar Ä‘Æ°á»£c upload lÃªn `storage/app/public/profiles` vÃ  hiá»ƒn thá»‹ qua `public/storage`. Sau khi upload, avatar sáº½ hiá»‡n á»Ÿ trang profile vÃ  dropdown user trÃªn header.',
                'suggestions' => [
                    'Avatar lÆ°u á»Ÿ Ä‘Ã¢u?',
                    'CÃ¡ch update profile',
                    'User - Profile relation',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['san pham', 'product', 'danh muc', 'category'])) {
            return [
                'message' => 'Module sáº£n pháº©m vÃ  danh má»¥c dÃ¹ng Eloquent CRUD. `editor` hoáº·c `admin` Ä‘Æ°á»£c quáº£n lÃ½ sáº£n pháº©m/danh má»¥c, cÃ²n sáº£n pháº©m cÃ³ áº£nh upload riÃªng. ÄÃ¢y lÃ  pháº§n dá»… Ä‘em Ä‘i váº¥n Ä‘Ã¡p vÃ¬ cÃ³ Ä‘á»§ controller, request validation vÃ  view.',
                'suggestions' => [
                    'Quáº£n lÃ½ sáº£n pháº©m gá»“m gÃ¬?',
                    'Danh má»¥c sáº£n pháº©m cÃ³ gÃ¬?',
                    'Upload áº£nh sáº£n pháº©m tháº¿ nÃ o?',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['don hang', 'order', 'trang thai', 'status', 'payment', 'mail'])) {
            return [
                'message' => 'Module đơn hàng có danh sách, chi tiết, lọc ngày, lọc trạng thái, xem khách hàng, cập nhật trạng thái và gửi mail khi đổi status. Thanh toán VNPay có luồng riêng để tạo đơn, chuyển sang cổng thanh toán, rồi cập nhật `payment_status`, `payment_method`, `transaction_code` và `paid_at` khi giao dịch thành công.',
                'suggestions' => [
                    'Xem chi tiáº¿t Ä‘Æ¡n hÃ ng',
                    'Gá»­i mail khi Ä‘á»•i status',
                    'Thanh toán VNPay hoạt động sao?',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['article', 'tag', 'articles', 'tags'])) {
            return [
                'message' => 'BÃ i articles dÃ¹ng Eloquent relationship: `Article belongsTo User` vÃ  `Article belongsToMany Tag`. Trang `/articles` hiá»ƒn thá»‹ title, user, body, ngÃ y táº¡o vÃ  tags tÆ°Æ¡ng á»©ng.',
                'suggestions' => [
                    'Quan há»‡ Article - Tag',
                    'Trang articles hiá»ƒn thá»‹ gÃ¬?',
                    'Táº¡i sao dÃ¹ng Eloquent?',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['chatbot', 'support chat', 'gemini', 'boost'])) {
            return [
                'message' => 'Chatbot cá»§a project lÃ  module há»— trá»£ khÃ¡ch hÃ ng trong admin. Bot Æ°u tiÃªn Ä‘á»c dá»¯ liá»‡u Ä‘Æ¡n hÃ ng tháº­t khi cÃ³ mÃ£ Ä‘Æ¡n, cÃ²n cÃ¡c cÃ¢u há»i khÃ¡c sáº½ Ä‘Æ°á»£c xá»­ lÃ½ báº±ng Gemini náº¿u API cÃ²n quota; náº¿u Gemini lá»—i, bot váº«n cÃ³ cÃ¢u tráº£ lá»i ná»™i bá»™ theo context cá»§a project.',
                'suggestions' => [
                    'Chatbot Ä‘á»c dá»¯ liá»‡u gÃ¬?',
                    'Gemini dÃ¹ng khi nÃ o?',
                    'Boost cÃ³ vai trÃ² gÃ¬?',
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


