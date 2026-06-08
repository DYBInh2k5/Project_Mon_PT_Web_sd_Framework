# Chatbot Agent Guide

Tep nay mo ta luong chatbot hien tai sau khi chuyen sang dung package
`laravel/ai` va mo hinh `conversation + message + agent` theo huong dan bai giang.

## 1. Tong quan

Chatbot khong con luu hoi thoai bang session nua. Thay vao do:

- `SupportChatController` render trang chat va xoa hoi thoai
- `ChatController` la controller `__invoke` nhan message va tra JSON
- `AgentConversation` luu phien hoi thoai
- `AgentConversationMessage` luu tung cau hoi/cau tra loi
- `SupportBot` la agent public theo stub cua `php artisan make:agent`
- `config/ai.php` dung default gateway la Gemini

## 2. File chinh

- `app/Http/Controllers/SupportChatController.php`
- `app/Http/Controllers/ChatController.php`
- `app/Ai/Agents/SupportBot.php`
- `app/Models/AgentConversation.php`
- `app/Models/AgentConversationMessage.php`
- `resources/views/support/chat.blade.php`
- `database/migrations/2026_06_08_103331_create_agent_conversations_table.php`
- `stubs/agent.stub`
- `stubs/structured-agent.stub`

## 3. Cau hinh AI

File `config/ai.php` gan voi bai giang:

- `AI_GATEWAY=gemini`
- `AI_MODEL=gemini-1.5-flash`
- `GEMINI_API_KEY=...`

`SupportBot` se goi AI thong qua package `laravel/ai`.

## 4. Luong chay

1. User mo `/support-chat`
2. `SupportChatController@index` load conversation cua user
3. User nhap cau hoi va bam Send
4. UI goi `POST /chat/send` bang fetch
5. `ChatController` luu message cua user
6. `SupportBot` goi Gemini qua `laravel/ai`
7. Assistant message duoc luu lai vao database
8. Controller tra JSON cho UI

## 5. Cach doan cho van dap

Co the noi ngan gon:

> Em da cai package `laravel/ai`, publish provider, cau hinh `config/ai.php` de default la Gemini, tao `SupportBot` theo mau agent, va dung `ChatController` dang `__invoke` de luu message user/assistant vao database. Cac cot attachments, tool_calls, tool_results, usage va meta da duoc de nullable theo huong dan.

## 6. Ghi nho

- Nut chatbot nho co dinh o goc duoi ben phai van duoc giu lai
- `clear chat` se xoa hoi thoai cua user trong database
- `make:agent` da co trong Artisan command list
- view chat gui message toi `POST /chat/send` va nhan JSON tra ve
