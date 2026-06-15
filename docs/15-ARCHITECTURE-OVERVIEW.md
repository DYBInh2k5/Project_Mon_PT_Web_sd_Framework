# 15. Bản Đồ Kiến Trúc Hệ Thống (Architecture Overview)

Tài liệu này đóng vai trò là bản đồ thiết kế tổng thể của dự án Laravel 12. Nếu bạn chỉ có thời gian đọc một file duy nhất để nắm bắt toàn bộ luồng vận hành của ứng dụng, hãy đọc file này ngay sau `docs/00-README.md`.

---

## 1. Mục tiêu và cấu trúc phân hệ

Dự án là **Hệ thống quản lý bán hàng** hoàn chỉnh bằng Laravel, được phân chia thành 2 khu vực chính độc lập về mặt chức năng nhưng dùng chung cơ sở dữ liệu:

1. **Phân hệ Cửa hàng công khai (Public Shop Area):**
   - Phục vụ khách vãng lai và thành viên xem sản phẩm.
   - Hỗ trợ xem chi tiết sản phẩm, tìm kiếm, lọc theo danh mục.
   - Thêm sản phẩm vào giỏ hàng và thanh toán trực tiếp qua cổng VNPay Sandbox.
2. **Phân hệ Quản trị (Admin/Editor Area):**
   - Tài khoản vai trò `admin` quản lý người dùng và hồ sơ cá nhân của người dùng.
   - Tài khoản vai trò `editor` quản lý danh mục sản phẩm, sản phẩm và cập nhật đơn đặt hàng của khách.
   - Chatbot AI tích hợp trực tiếp hỗ trợ giải đáp thắc mắc và kiểm tra trạng thái đơn hàng thời gian thực.
   - Demo module bài viết (`articles`) và nhãn bài viết (`tags`) để minh họa quan hệ Nhiều - Nhiều.

---

## 2. Kiến trúc phân tầng (Layered Architecture)

Dự án áp dụng mô hình phân tách trách nhiệm thành các tầng xử lý dữ liệu rõ ràng:

### 2.1. Tầng Tuyến đường (Route Layer)
- **File định cấu hình:** `routes/web.php` và `routes/auth.php`.
- **Nhiệm vụ:** Ánh xạ các đường dẫn URL gửi từ trình duyệt tới các Controller tương ứng và gán Middleware lọc bảo mật đầu vào.

### 2.2. Tầng Bộ lọc (Middleware Layer)
- **Tệp tin chính:** [EnsureUserHasRole.php](../app/Http/Middleware/EnsureUserHasRole.php) và [CheckAge.php](../app/Http/Middleware/CheckAge.php).
- **Nhiệm vụ:** Kiểm tra phân quyền vai trò tài khoản trước khi cho phép đi tiếp vào Controller (tránh truy cập trái phép) và demo kiểm tra độ tuổi.

### 2.3. Tầng Điều phối (Controller Layer)
- **Nhiệm vụ:** Tiếp nhận request từ client, điều phối gọi các lớp nghiệp vụ (Services) hoặc Model để lấy dữ liệu, sau đó hiển thị View tương ứng cho người dùng.
- **Danh sách các Controller cốt lõi:**
  - `UserController`, `ProductController`, `ProductCategoryController`, `OrderController`.
  - `ShopController`, `ShopCartController`, `ShopCheckoutController`.
  - `SupportChatController`, `ChatController`, `ArticleController`.

### 2.4. Tầng Xác thực biểu mẫu (Validation Layer)
- **Nhiệm vụ:** Sử dụng cơ chế Form Request của Laravel để kiểm tra tính đúng đắn của dữ liệu submit (ví dụ: `StoreUserRequest`, `ProductRequest`). Giúp Controller luôn sạch gọn (Skinny Controller).

### 2.5. Tầng Nghiệp vụ (Service Layer)
- **Nhiệm vụ:** Đóng gói toàn bộ các xử lý nghiệp vụ phức tạp hoặc kết nối dịch vụ bên thứ ba ra khỏi Controller.
- **Danh sách Service chính:** `OrderService` (nghiệp vụ đơn hàng), `VnpayPaymentService` (kết nối cổng VNPay), `GeminiChatService` (gọi API AI).

### 2.6. Tầng Mô hình dữ liệu (Model Layer)
- **Nhiệm vụ:** Ánh xạ các thực thể bảng dữ liệu SQLite thành các Object trong PHP qua Eloquent ORM, khai báo mối quan hệ thực thể (1-1, 1-N, N-N) và định nghĩa các Local Scope tối ưu câu lệnh SQL.

### 2.7. Tầng Sự kiện & Gửi mail (Event/Listener Layer)
- **Nhiệm vụ:** Khi trạng thái đơn hàng thay đổi, phát sự kiện `OrderStatusUpdated`. Listener `SendOrderStatusUpdatedMail` sẽ đón nhận để thực hiện gửi email thông báo ngầm dạng HTML cho khách hàng.

### 2.8. Tầng Giao diện (View Layer)
- **Nhiệm vụ:** Hiển thị HTML động bằng Blade Template, kết hợp Tailwind CSS để trang trí giao diện và Alpine.js để thực hiện các xử lý bất đồng bộ (như gọi chatbot hỗ trợ).

---

## 3. Luồng chạy cốt lõi của các nghiệp vụ

### 3.1. Quy trình cập nhật trạng thái đơn hàng và gửi email thông báo

```text
Admin/Editor đổi trạng thái đơn hàng (ví dụ: pending -> processing)
  ↓
Gửi request PATCH /orders/{order}/status
  ↓
UpdateOrderStatusRequest thực hiện validate dữ liệu trạng thái hợp lệ
  ↓
OrderController gọi phương thức updateStatus() trong OrderService
  ↓
OrderService cập nhật bảng orders và tạo bản ghi trong order_status_histories
  ↓
OrderService phát sự kiện OrderStatusUpdated
  ↓
SendOrderStatusUpdatedMail Listener bắt được sự kiện và gửi email thông báo HTML cho khách hàng.
```

### 3.2. Quy trình mua hàng và thanh toán trực tuyến VNPay

```text
Khách thêm sản phẩm vào giỏ hàng và nhấn Checkout
  ↓
Khai báo thông tin giao hàng tại trang /checkout
  ↓
Hệ thống tạo đơn hàng mới với trạng thái pending và unpaid trong SQLite
  ↓
ShopCheckoutController gọi VnpayPaymentService tạo URL thanh toán bảo mật SHA512
  ↓
Khách hàng được chuyển hướng (redirect) sang cổng VNPay Sandbox để thanh toán
  ↓
Khách thanh toán xong, VNPay điều hướng về /checkout/vnpay/return hiển thị kết quả
  ↓
VNPay gửi request IPN ngầm đối soát server-to-server tới đầu cuối /checkout/vnpay/ipn
  ↓
ShopCheckoutController kiểm tra an toàn dữ liệu, cập nhật trạng thái đơn thành paid và processing.
```

### 3.3. Quy trình Trợ lý AI Chatbot tư vấn dữ liệu thực tế (Function Calling)

```text
Người dùng gửi tin nhắn hỏi: "Shop có bán chiếc áo thun nào không?"
  ↓
Fetch gửi request POST /chat/send
  ↓
ChatController lưu tin nhắn user vào database và chuyển tin nhắn sang Agent SupportBot
  ↓
Gemini AI phân tích câu hỏi, chọn chạy công cụ SearchProducts với từ khóa {"query": "áo thun"}
  ↓
SearchProducts Tool truy vấn SQLite và trả về dữ liệu JSON sản phẩm cho AI
  ↓
Gemini AI tổng hợp thông tin sản phẩm thật và phản hồi câu trả lời Tiếng Việt cho khách hàng
  ↓
ChatController lưu câu trả lời của AI vào database và phản hồi cho giao diện hiển thị.
```
