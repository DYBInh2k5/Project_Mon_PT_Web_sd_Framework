# 16. Tổng Quan Và Cài Đặt Dự Án (Tong Quan Va Cai Dat)

Tài liệu này cung cấp bản đọc và nắm bắt nhanh toàn bộ dự án theo đúng hướng dẫn làm đồ án thực tế.

## 1. Mục tiêu dự án
Dự án xây dựng **Hệ thống quản lý bán hàng** toàn diện sử dụng Laravel 12. Hệ thống được thiết kế tối ưu phục vụ hai phân hệ chính:
- **Trang bán hàng công khai (Public Shop):** Khách hàng vãng lai duyệt sản phẩm, tìm kiếm, lọc theo danh mục, quản lý giỏ hàng và thanh toán trực tiếp qua VNPay.
- **Khu vực quản trị (Admin/Editor Area):** Người dùng có quyền truy cập vào trang Dashboard để theo dõi thống kê nhanh, quản lý danh sách tài khoản, cập nhật profile đi kèm, quản lý danh mục và sản phẩm, cập nhật đơn hàng và giao tiếp trực tiếp với chatbot AI.

## 2. Các chức năng cốt lõi đã hoàn thành
- Xác thực người dùng hoàn chỉnh (Auth) và phân quyền kiểm soát thông qua các vai trò (`admin`, `editor`, `user`).
- Quản lý tài khoản kết hợp hồ sơ cá nhân theo quan hệ 1-1.
- Quản lý danh mục và sản phẩm có tải lên/xóa hình ảnh sản phẩm.
- Quản lý đơn hàng: Tách biệt logic nghiệp vụ qua `OrderService`, lưu nhật ký thay đổi lịch sử trạng thái đơn hàng, tự động gửi mail thông báo dạng HTML cho khách hàng thông qua Event & Listener.
- Thanh toán trực tuyến VNPay: Thiết lập môi trường thử nghiệm VNPay Sandbox, thực hiện xử lý chữ ký bảo mật secureHash và tự động cập nhật đơn hàng thông qua returnUrl và ipnUrl.
- Trợ lý AI Chatbot: Trò chuyện và tư vấn hỗ trợ khách hàng sử dụng API Gemini. AI có khả năng tự động truy vấn thông tin sản phẩm từ cơ sở dữ liệu thật của dự án nhờ cơ chế gọi công cụ (Function Calling: SearchProducts, GetProductDetails, ListCategories).

---

## 3. Cấu hình các dịch vụ cần nhớ

### 3.1 Cấu hình VNPay (Môi trường Sandbox)
Cấu hình các tham số VNPay trong file `.env`:
```env
VNPAY_TMN_CODE=...
VNPAY_HASH_SECRET=...
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_VERSION=2.1.0
VNPAY_LOCALE=vn
VNPAY_ORDER_TYPE=other
VNPAY_BANK_CODE=
VNPAY_EXPIRE_MINUTES=15
```
*Lưu ý:* Để trống `VNPAY_BANK_CODE` để hệ thống hiển thị danh sách các phương thức thanh toán đa dạng trên cổng VNPay Sandbox.

### 3.2 Cấu hình Chatbot AI
Khai báo API Key của Gemini trong file `.env`:
```env
GEMINI_API_KEY=...
GEMINI_MODEL=gemini-2.0-flash
GEMINI_BASE_URL=https://generativelanguage.googleapis.com
```

---

## 4. Luồng xử lý thanh toán VNPay Sandbox

1. Khách hàng lựa chọn các sản phẩm ưa thích và nhấn thanh toán từ trang giỏ hàng.
2. Khai báo thông tin nhận hàng (Tên, SĐT, Email, Địa chỉ) tại trang `/checkout`.
3. Hệ thống tạo đơn hàng mới ở trạng thái `pending` và `unpaid` trong SQLite.
4. Hệ thống mã hóa thông tin đơn hàng bằng thuật toán SHA512, tạo đường dẫn chuyển hướng sang cổng thanh toán VNPay Sandbox.
5. Khách hàng tiến hành thanh toán trực tuyến trên cổng VNPay Sandbox.
6. Sau khi hoàn tất giao dịch, VNPay chuyển hướng trình duyệt của khách về lại trang kết quả của cửa hàng (`returnUrl`). Hệ thống kiểm tra chữ ký và hiển thị kết quả.
7. Cổng VNPay đồng thời gửi truy vấn IPN ẩn (`ipnUrl`) từ máy chủ của VNPay tới máy chủ của shop để thực hiện kiểm tra 5 bước bảo mật. Nếu hợp lệ, cập nhật trạng thái đơn hàng thành `processing` và trạng thái thanh toán thành `paid` để đảm bảo không bị mất thông tin đơn.

---

## 5. Quy trình Trợ lý AI Chatbot hoạt động

1. Người dùng mở cửa sổ chat hỗ trợ trực tuyến tại trang `/support-chat`.
2. Hệ thống tải lịch sử hội thoại bền vững từ database hiển thị lên giao diện.
3. Người dùng nhập nội dung câu hỏi (ví dụ: *"Tìm các danh mục sản phẩm của shop"*).
4. Hệ thống gửi Ajax tới đầu cuối `POST /chat/send`.
5. `ChatController` lưu tin nhắn và gọi Agent `SupportBot`.
6. Gemini AI phát hiện câu hỏi cần dữ liệu thực tế, tự động kích hoạt gọi công cụ `ListCategories` đã đăng ký.
7. Công cụ `ListCategories` truy vấn SQLite và trả về danh sách danh mục.
8. Gemini AI tổng hợp kết quả và trả về câu trả lời Tiếng Việt rõ ràng cho khách hàng.

---

## 6. Mẫu phát biểu tóm tắt dự án khi vấn đáp

> "Dự án của em là Hệ thống quản trị website bán hàng bằng Laravel 12. Hệ thống được chia thành phân hệ shop công khai phục vụ khách đặt hàng thanh toán VNPay và khu vực quản trị dành cho Admin/Editor quản lý tài khoản, danh mục, sản phẩm và đơn hàng. Để nâng cao chất lượng mã nguồn và tối ưu hiệu năng, em đã áp dụng Service Pattern đóng gói logic đơn hàng và thanh toán VNPay, sử dụng Event & Listener để gửi email thông báo tự động ở chế độ hàng đợi nền, áp dụng Eager Loading loại bỏ lỗi N+1 query và xây dựng một Trợ lý AI Chatbot tích hợp trực tiếp có thể gọi các công cụ (Tools) để truy vấn thông tin sản phẩm thời gian thực trong cơ sở dữ liệu."
