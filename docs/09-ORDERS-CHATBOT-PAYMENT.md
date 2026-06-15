# 09. Đơn Hàng, Chatbot Trợ Lý AI Và Thanh Toán VNPay (Orders, Chatbot, Payment)

## 1. Nghiệp vụ Quản lý Đơn hàng (Order Management)

### Các Model tham gia:
- [Order.php](../app/Models/Order.php) - Lưu trữ thông tin tổng quát của đơn đặt hàng và thông tin khách hàng.
- [OrderItem.php](../app/Models/OrderItem.php) - Lưu chi tiết các sản phẩm đã mua tại thời điểm đặt hàng.
- [OrderStatusHistory.php](../app/Models/OrderStatusHistory.php) - Ghi nhật ký lịch sử thay đổi trạng thái đơn hàng.

### Luồng xử lý nghiệp vụ đơn hàng (Đã được tối ưu bằng Event/Listener):

```text
Nhân viên cập nhật trạng thái đơn (pending -> processing -> completed)
  ↓
Gọi phương thức updateStatus() trong OrderController.php
  ↓
Chuyển tiếp xử lý sang OrderService.php
  ↓
Mở Database Transaction
  - Cập nhật trường status trong bảng orders
  - Tạo một dòng ghi vết lịch sử trong bảng order_status_histories
  ↓
Phát ra sự kiện OrderStatusUpdated
  ↓
Listener SendOrderStatusUpdatedMail bắt được sự kiện
  ↓
Gửi email thông báo OrderStatusUpdatedMail (dạng HTML) tới email của khách hàng.
```

**Các View Blade liên quan:**
- [orders/index.blade.php](../resources/views/orders/index.blade.php) - Danh sách đơn hàng cho editor/admin.
- [orders/show.blade.php](../resources/views/orders/show.blade.php) - Trang chi tiết đơn hàng (Xem sản phẩm đã đặt, lịch sử đổi trạng thái đơn và form đổi trạng thái).

**Lưu ý kỹ thuật:**
- **Môi trường cục bộ:** File cấu hình `.env` thiết lập `MAIL_MAILER=log`, nghĩa là email thông báo được ghi nhận trực tiếp vào file log hệ thống (`storage/logs/laravel.log`) thay vì gửi qua SMTP hộp thư thật.
- **Hàng đợi Queue:** Cấu hình `QUEUE_CONNECTION=sync` giúp bộ lắng nghe (Listener) chạy đồng bộ ngay lập tức để phục vụ kiểm tra nhanh lúc thi vấn đáp.

---

## 2. Trợ lý AI Hỗ trợ Khách hàng (Chatbot Agent)

Hệ thống đã nâng cấp toàn diện chatbot sang sử dụng package **`laravel/ai`** chính thức và mô hình lưu trữ lịch sử qua cơ sở dữ liệu thay vì sử dụng session tạm thời:

### Các file cốt lõi:
- [SupportChatController.php](../app/Http/Controllers/SupportChatController.php) - Render trang chat và tải lịch sử tin nhắn từ database để hiển thị cho Alpine.js.
- [ChatController.php](../app/Http/Controllers/ChatController.php) - Single Action Controller nhận câu hỏi bằng ajax, lưu tin nhắn, gọi Agent AI tạo phản hồi và lưu phản hồi của AI.
- [SupportBot.php](../app/Ai/Agents/SupportBot.php) - Lớp Agent cấu hình System Instructions và đăng ký các công cụ truy xuất dữ liệu thực tế.
- [AgentConversation.php](../app/Models/AgentConversation.php) - Model lưu trữ phiên trò chuyện của người dùng đăng nhập.
- [AgentConversationMessage.php](../app/Models/AgentConversationMessage.php) - Model lưu chi tiết tin nhắn của user và AI phản hồi (kèm thông tin cuộc gọi công cụ `tool_calls` và `tool_results`).

### Cơ chế gọi công cụ (Function Calling) trong dự án:
AI Gemini không trực tiếp kết nối với cơ sở dữ liệu. Thay vào đó, Agent `SupportBot` đăng ký 3 công cụ (Tools) sau để AI tự động chọn gọi khi cần thông tin thực tế:
1. [SearchProducts.php](../app/Ai/Tools/SearchProducts.php) - Tìm kiếm sản phẩm theo từ khóa tên.
2. [GetProductDetails.php](../app/Ai/Tools/GetProductDetails.php) - Lấy thông số kỹ thuật chi tiết của sản phẩm bằng ID.
3. [ListCategories.php](../app/Ai/Tools/ListCategories.php) - Liệt kê tất cả danh mục sản phẩm hiện có.

**Quy trình hoạt động:**
```text
Người dùng nhập: "Xem cho tôi chi tiết sản phẩm có ID là 5"
  ↓
ChatController nhận tin nhắn và chuyển sang Agent SupportBot
  ↓
Gemini AI phân tích câu hỏi, phát hiện cần dùng công cụ GetProductDetails
  ↓
Gemini AI phản hồi yêu cầu chạy công cụ với tham số {"product_id": 5}
  ↓
Laravel chạy code PHP trong GetProductDetails@handle, truy vấn sản phẩm trong SQLite
  ↓
Kết quả JSON sản phẩm được trả ngược lại cho Gemini AI
  ↓
Gemini AI tổng hợp thông tin và phản hồi câu trả lời Tiếng Việt hoàn chỉnh cho khách hàng.
```

---

## 3. Tích hợp cổng thanh toán trực tuyến VNPay

Dự án thay thế phương thức thanh toán ví Momo cũ bằng cổng **VNPay Sandbox** (môi trường thử nghiệm):

### Các file xử lý:
- [VnpayPaymentService.php](../app/Services/VnpayPaymentService.php) - Đóng gói logic sắp xếp tham số alphabet, mã hóa chữ ký SHA512 và trích xuất số tiền.
- [ShopCheckoutController.php](../app/Http/Controllers/ShopCheckoutController.php) - Quản lý quy trình đặt hàng từ giỏ hàng và tiếp nhận phản hồi từ VNPay.

### Luồng xử lý giao dịch VNPay:
1. Khách hàng lựa chọn sản phẩm, thêm vào giỏ hàng và truy cập trang `/checkout`.
2. Khách điền thông tin cá nhân. Hệ thống khởi tạo một đơn hàng mới trong bảng `orders` ở trạng thái `pending` và `unpaid`.
3. Hệ thống gọi `VnpayPaymentService` để sinh liên kết thanh toán VNPay và thực hiện chuyển hướng khách hàng (Redirect).
4. Khách hàng thực hiện thanh toán trên cổng VNPay Sandbox.
5. Cổng VNPay điều hướng trình duyệt quay về trang kết quả của shop (`returnUrl`):
   - `ShopCheckoutController@vnpayReturn` kiểm tra chữ ký checksum và tổng số tiền đơn hàng.
   - Nếu hợp lệ, hệ thống cập nhật đơn hàng thành đã thanh toán và hiển thị thông báo thành công cho khách hàng, đồng thời xóa sạch giỏ hàng hiện tại trong Session.
6. **Xác nhận IPN bảo mật (ipnUrl):** Cổng VNPay tự động gọi ngầm một request bất đồng bộ tới đầu cuối IPN của shop. `ShopCheckoutController@ipn` thực hiện quy trình kiểm tra 5 bước bảo mật bắt buộc của VNPay. Nếu hợp lệ, cập nhật trạng thái đơn hàng sang `processing` và trạng thái thanh toán thành `paid` để đảm bảo đơn hàng được xác nhận thành công ngay cả khi khách hàng tắt trình duyệt.

## 4. Cách demo nghiệp vụ khi vấn đáp
1. **Demo Đơn hàng:** Vào trang chi tiết đơn hàng `/orders/{id}` bất kỳ. Thay đổi trạng thái đơn hàng và kiểm tra lịch sử trạng thái hiển thị bên dưới. Kiểm tra file log `storage/logs/laravel.log` để xem nội dung email thông báo dạng HTML đã được tạo thành công.
2. **Demo Thanh toán VNPay:** Thêm sản phẩm vào giỏ hàng, mở trang `/checkout`, nhập thông tin giao hàng và chọn VNPay. Hệ thống sẽ redirect sang cổng thanh toán VNPay. Sử dụng thẻ test của VNPay Sandbox để thanh toán, sau đó trình duyệt tự động chuyển hướng về trang kết quả thành công của cửa hàng.
3. **Demo Chatbot AI:** Mở trang `/support-chat`. Hãy nhập các câu hỏi để AI gọi công cụ như: *"Tìm giúp mình sản phẩm áo thun"* hoặc *"Trong shop có các danh mục sản phẩm nào?"*. Chatbot AI sẽ tự động kích hoạt Tool tương ứng để truy vấn database SQLite và phản hồi thông tin thực tế.
