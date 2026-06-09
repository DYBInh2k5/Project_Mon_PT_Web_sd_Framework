# Hướng dẫn chatbot agent

Tài liệu này mô tả luồng chatbot hiện tại sau khi chuyển sang dùng package `laravel/ai` và mô hình `conversation + message + agent` theo hướng dẫn của cô.

## 1. Tổng quan

Chatbot không còn lưu hội thoại bằng session nữa. Thay vào đó:

- `SupportChatController` render trang chat và xoá hội thoại
- `ChatController` là controller `__invoke`, nhận message và trả JSON
- `AgentConversation` lưu phiên hội thoại
- `AgentConversationMessage` lưu từng câu hỏi/câu trả lời
- `SupportBot` là agent public theo stub của `php artisan make:agent`
- `config/ai.php` dùng default gateway là Gemini

## 2. File chính

- `app/Http/Controllers/SupportChatController.php`
- `app/Http/Controllers/ChatController.php`
- `app/Ai/Agents/SupportBot.php`
- `app/Models/AgentConversation.php`
- `app/Models/AgentConversationMessage.php`
- `resources/views/support/chat.blade.php`
- `database/migrations/2026_06_08_103331_create_agent_conversations_table.php`
- `stubs/agent.stub`
- `stubs/structured-agent.stub`

## 3. Cấu hình AI

File `config/ai.php` bám sát bài giảng:

- `AI_GATEWAY=gemini`
- `AI_MODEL=gemini-1.5-flash`
- `GEMINI_API_KEY=...`

`SupportBot` sẽ gọi AI thông qua package `laravel/ai`.

## 4. Luồng chạy

1. User mở `/support-chat`
2. `SupportChatController@index` tải conversation của user
3. User nhập câu hỏi và bấm Send
4. UI gọi `POST /chat/send` bằng `fetch`
5. `ChatController` lưu message của user
6. `SupportBot` gọi Gemini qua `laravel/ai`
7. Assistant message được lưu lại vào database
8. Controller trả JSON cho UI

## 5. Cách nói khi vấn đáp

Có thể nói ngắn gọn:

> Em đã cài package `laravel/ai`, publish provider, cấu hình `config/ai.php` để default là Gemini, tạo `SupportBot` theo mẫu agent, và dùng `ChatController` dạng `__invoke` để lưu message user/assistant vào database. Các cột `attachments`, `tool_calls`, `tool_results`, `usage` và `meta` đã được để `nullable` đúng theo hướng dẫn.

## 6. Ghi nhớ

- Nút chatbot nhỏ cố định ở góc dưới bên phải vẫn được giữ lại
- `clear chat` sẽ xoá hội thoại của user trong database
- `make:agent` đã có trong Artisan command list
- view chat gửi message tới `POST /chat/send` và nhận JSON trả về
