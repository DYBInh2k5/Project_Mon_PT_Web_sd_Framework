<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeminiChatService
{
    /**
     * Gửi tin nhắn và ngữ cảnh của người dùng tới Gemini API, trả về mảng dữ liệu gồm câu trả lời và các gợi ý.
     *
     * @param  string  $message  Tin nhắn câu hỏi từ người dùng
     * @param  array  $context  Thông tin ngữ cảnh bổ sung (như đơn hàng, sản phẩm)
     * @return array|null Phản hồi đã được chuẩn hóa hoặc null nếu gặp lỗi
     */
    public function answer(string $message, array $context = []): ?array
    {
        $apiKey = config('ai.providers.gemini.api_key') ?: config('services.gemini.key');

        if (! $apiKey) {
            return null;
        }

        $model = config('ai.model', config('services.gemini.model', 'gemini-2.0-flash'));
        $baseUrl = rtrim(config('ai.providers.gemini.base_url', config('services.gemini.base_url', 'https://generativelanguage.googleapis.com')), '/');

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        [
                            'text' => $this->buildPrompt($message, $context),
                        ],
                    ],
                ],
            ],
            // generationConfig cấu đặt các tham số điều khiển mức độ sáng tạo của AI.
            'generationConfig' => [
                'temperature' => 0.35, // Đặt nhiệt độ thấp để câu trả lời mang tính chính xác cao, bám sát context thực tế.
                'maxOutputTokens' => 768, // Giới hạn số lượng token phản hồi tối đa.
            ],
        ];

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post(sprintf('%s/v1beta/models/%s:generateContent?key=%s', $baseUrl, $model, $apiKey), $payload);

            if (! $response->successful()) {
                Log::warning('Gemini chatbot request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

            if (!is_string($text) || trim($text) === '') {
                return null;
            }

            // Chuẩn hóa và làm sạch phản hồi JSON nhận được từ Gemini.
            return $this->normalizeGeminiResponse($text);
        } catch (Throwable $e) {
            Log::warning('Gemini chatbot exception', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Xây dựng Prompt hệ thống (System Prompt) và chèn ngữ cảnh ứng dụng gửi cho Gemini.
     */
    protected function buildPrompt(string $message, array $context = []): string
    {
        $contextText = $context !== []
            ? "\n\nContext from the app:\n".json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            : '';

        return <<<PROMPT
You are the AI support assistant for a Laravel 12 admin project.
Answer in Vietnamese.
You must be able to answer broad questions about the project, including:
- Laravel architecture
- auth, middleware, roles
- users, profiles, products, categories
- orders, order status, mail notifications
- VNPay checkout
- articles and tags
- chatbot implementation
- Laravel Boost / AI workflow in this project

Rules:
- If the user asks about an order code like ORD-00023, use the provided order context first.
- If the answer depends on project implementation, prefer the provided app context and docs.
- If the answer is not fully known, explain clearly what is known and what the user should check in the app/code.
- Return JSON only in this exact shape:
{"message":"...","suggestions":["...","...","..."]}
- Suggestions should be short, useful follow-up prompts.

User question:
{$message}{$contextText}
PROMPT;
    }

    /**
     * Chuẩn hóa kết quả phản hồi của Gemini đảm bảo có cấu trúc JSON hợp lệ để render ở Frontend.
     *
     * @return array{message:string,suggestions:array<int, string>}
     */
    protected function normalizeGeminiResponse(string $text): array
    {
        $trimmed = trim($text);
        $decoded = json_decode($trimmed, true);

        // Trường hợp Gemini bọc JSON trong Markdown code fence (ví dụ: ```json ... ```).
        if (! is_array($decoded)) {
            $decoded = $this->extractJsonObject($trimmed);
        }

        if (is_array($decoded) && isset($decoded['message'])) {
            return [
                'message' => (string) $decoded['message'],
                'suggestions' => collect($decoded['suggestions'] ?? [])
                    ->filter(fn ($item) => is_string($item) && trim($item) !== '')
                    ->map(fn (string $item) => trim($item))
                    ->take(3)
                    ->values()
                    ->all(),
            ];
        }

        // Fallback trong trường hợp phản hồi là một chuỗi văn bản thuần túy không thể parse JSON.
        return [
            'message' => $trimmed,
            'suggestions' => [],
        ];
    }

    /**
     * Trích xuất đối tượng JSON từ chuỗi phản hồi thô bằng biểu thức chính quy (Regex).
     */
    protected function extractJsonObject(string $text): ?array
    {
        if (preg_match('/\{.*\}/s', $text, $matches)) {
            $decoded = json_decode($matches[0], true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}


