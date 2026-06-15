# 13. Hướng Dẫn Chatbot Agent AI (Chatbot Agent Guide)

Tài liệu này mô tả chi tiết kiến trúc và luồng hoạt động của Chatbot hỗ trợ khách hàng sau khi dự án chuyển sang tích hợp package **`laravel/ai`** chính thức và mô hình lưu trữ lịch sử qua cơ sở dữ liệu bền vững.

## 1. Tổng quan cấu trúc hệ thống

Chatbot không còn lưu trữ lịch sử trò chuyện bằng Session tạm thời nữa, thay vào đó:
- **`SupportChatController`**: Phụ trách hiển thị giao diện trang chat (`support.chat`) và xóa phiên hội thoại của người dùng khi được yêu cầu.
- **`ChatController`**: Là Single Action Controller (`__invoke`), tiếp nhận nội dung câu hỏi gửi lên bằng Ajax, lưu tin nhắn, kích hoạt AI xử lý và trả kết quả dạng JSON.
- **`AgentConversation`**: Lưu trữ phiên hội thoại của người dùng đăng nhập trong cơ sở dữ liệu (Bảng `agent_conversations`).
- **`AgentConversationMessage`**: Lưu chi tiết từng tin nhắn hỏi và đáp tương ứng (Bảng `agent_conversations_messages`).
- **`SupportBot`**: Lớp cấu hình Agent kế thừa các giao diện của package `laravel/ai` để định nghĩa System Instructions và danh sách Tools.
- **`config/ai.php`**: Khai báo nhà cung cấp AI mặc định là Gemini, chỉ định model và kết nối API Key từ file `.env`.

## 2. Danh sách các file tham gia xử lý

- **Controller:**
  - [app/Http/Controllers/SupportChatController.php](../app/Http/Controllers/SupportChatController.php)
  - [app/Http/Controllers/ChatController.php](../app/Http/Controllers/ChatController.php)
- **AI Agent & Tools:**
  - [app/Ai/Agents/SupportBot.php](../app/Ai/Agents/SupportBot.php)
  - [app/Ai/Tools/SearchProducts.php](../app/Ai/Tools/SearchProducts.php)
  - [app/Ai/Tools/GetProductDetails.php](../app/Ai/Tools/GetProductDetails.php)
  - [app/Ai/Tools/ListCategories.php](../app/Ai/Tools/ListCategories.php)
- **Models:**
  - [app/Models/AgentConversation.php](../app/Models/AgentConversation.php)
  - [app/Models/AgentConversationMessage.php](../app/Models/AgentConversationMessage.php)
- **Database Schema:**
  - Bảng `agent_conversations` và `agent_conversations_messages`.
- **Giao diện Blade View:**
  - [resources/views/support/chat.blade.php](../resources/views/support/chat.blade.php)

## 3. Cấu hình AI trong dự án

Tài liệu cấu hình bám sát bài giảng tại file `config/ai.php`:
- Môi trường sử dụng API Gateway mặc định là `gemini`.
- Mô hình mặc định (AI Model): `gemini-2.0-flash` hoặc `gemini-1.5-flash`.
- Các biến cấu hình tương ứng được lấy từ file `.env`:
  ```env
  GEMINI_API_KEY=AIzaSy...
  GEMINI_MODEL=gemini-2.0-flash
  GEMINI_BASE_URL=https://generativelanguage.googleapis.com
  ```

## 4. Luồng xử lý một lượt trò chuyện (Chat Flow)

1. Người dùng mở trang Hỗ trợ trực tuyến tại địa chỉ `/support-chat`.
2. `SupportChatController@index` kiểm tra và tải phiên hội thoại `AgentConversation` của người dùng. Nếu chưa có, tự động tạo mới một phiên hội thoại trống.
3. Người dùng nhập câu hỏi và nhấn nút gửi.
4. Giao diện (sử dụng thư viện Alpine.js) gửi request `POST /chat/send` bằng phương thức `fetch()` bất đồng bộ kèm tin nhắn dạng JSON.
5. `ChatController` tiếp nhận request, lưu trữ tin nhắn của người dùng vào bảng `agent_conversations_messages` với vai trò `role = 'user'`.
6. `ChatController` khởi tạo Agent `SupportBot` và gọi phương thức `prompt()` gửi tin nhắn tới Gemini API qua package `laravel/ai`.
7. Gemini AI tiếp nhận câu hỏi. Nếu phát hiện câu hỏi cần dữ liệu thực tế (như tìm kiếm sản phẩm), Gemini sẽ yêu cầu gọi Tool tương ứng. Hệ thống chạy code PHP của Tool đó và trả dữ liệu ngược lại cho AI xử lý tiếp.
8. Sau khi có câu trả lời hoàn chỉnh từ AI, `ChatController` thực hiện lưu tin nhắn phản hồi vào bảng `agent_conversations_messages` với vai trò `role = 'assistant'` (Lưu kèm thông tin thống kê token đã sử dụng, thông tin cuộc gọi công cụ).
9. Controller trả về kết quả dạng JSON chứa câu trả lời hoàn chỉnh để giao diện hiển thị cho người dùng.

## 5. Ghi nhớ quan trọng cho thi vấn đáp
- **Lưu lịch sử:** Không dùng session để lưu lịch sử, mà sử dụng hoàn toàn cơ sở dữ liệu giúp tin nhắn không bị mất đi khi người dùng tải lại trang hoặc đăng xuất.
- **Nút xóa chat:** Nút "Xóa lịch sử chat" gọi tới route `POST /support-chat/clear` để xóa toàn bộ các bản ghi hội thoại của người dùng trong database.
- **Cơ chế gọi công cụ (Tools):** Giúp AI trả lời thông tin chính xác về kho hàng, sản phẩm của shop thay vì tự suy đoán bừa bãi.
