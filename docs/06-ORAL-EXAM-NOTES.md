# 06. Ghi Chú Và Hướng Dẫn Vấn Đáp (Oral Exam Notes)

## 1. Giới thiệu ngắn gọn về dự án (Khi cô hỏi giới thiệu đề tài)

**Mẫu trả lời:**
> "Dự án của em là Xây dựng ứng dụng phần quản trị của Website bán hàng bằng Laravel 12. Hệ thống được chia làm hai khu vực chính: Mặt tiền cửa hàng công khai dành cho khách xem sản phẩm, giỏ hàng, thanh toán VNPay và Khu vực quản trị dành cho Admin/Editor quản lý tài khoản người dùng, hồ sơ (profile), danh mục sản phẩm, sản phẩm, quản lý đơn hàng cùng với một Chatbot AI hỗ trợ khách hàng tích hợp. Dự án áp dụng đầy đủ các kỹ năng nâng cao của Laravel như Middleware phân quyền, Form Request Validation, Eloquent Relationship (1-1, 1-N, N-N), Blade Component, Service Pattern, và hệ thống Event/Listener gửi mail tự động."

---

## 2. Các câu hỏi thường gặp và gợi ý câu trả lời tuyển chọn

### Câu 2.1: Route hoạt động thế nào trong dự án của em?
- **Trả lời:**
  - Tuyến đường định nghĩa trong 2 file: `routes/web.php` (quản trị, giỏ hàng, shop công khai) và `routes/auth.php` (xác thực).
  - Khi người dùng gửi request, nó sẽ đi qua các bộ lọc Middleware (như `auth` kiểm tra đăng nhập, `role` kiểm tra quyền hạn) trước khi chuyển tiếp vào các phương thức tương ứng trong các Controller xử lý.

### Câu 2.2: Hệ thống phân quyền (Role) hoạt động ra sao?
- **Trả lời:**
  - Cột vai trò (`role`) được lưu trực tiếp trong bảng `users` với các giá trị: `admin`, `editor`, `user`.
  - Em viết một middleware tùy chỉnh là [EnsureUserHasRole.php](../app/Http/Middleware/EnsureUserHasRole.php) để chặn các truy cập trái phép. Middleware sẽ lấy thông tin user đăng nhập từ Session, đối chiếu với danh sách role khai báo ở tuyến đường. Nếu không khớp sẽ dừng xử lý và trả về mã lỗi HTTP 403.

### Câu 2.3: Hồ sơ người dùng (Profile) liên kết thế nào?
- **Trả lời:**
  - Em tách thông tin tài khoản và thông tin cá nhân thành 2 bảng (`users` và `profiles`) để tối ưu thiết kế, liên kết thông qua quan hệ 1-1: `User hasOne Profile` và `Profile belongsTo User` trong Model [User.php](../app/Models/User.php) và [Profile.php](../app/Models/Profile.php).
  - Trong trang quản trị người dùng xử lý bởi [UserController.php](../app/Http/Controllers/UserController.php), Admin có thể cập nhật song song thông tin tài khoản chính và thông tin cá nhân của người dùng trên cùng một biểu mẫu.

### Câu 2.4: Tại sao sử dụng thuộc tính `novalidate` trên thẻ `<form>`?
- **Trả lời:**
  - Để tắt cơ chế validate mặc định của trình duyệt (vốn hiển thị bong bóng thông báo tiếng Anh/tiếng Việt không đồng bộ).
  - Em muốn form luôn submit lên server để Laravel validate thông qua Form Request (ví dụ: [ProductRequest.php](../app/Http/Requests/ProductRequest.php)), sau đó trả về danh sách lỗi chuẩn để hiển thị đồng bộ qua Blade Component `<x-package-alert>`.

### Câu 2.5: Em thiết lập quan hệ Nhiều-Nhiều (Many-to-Many) thế nào?
- **Trả lời:**
  - Em demo mối quan hệ nhiều-nhiều giữa Bài viết (`articles`) và Nhãn (`tags`) xử lý tại [ArticleController.php](../app/Http/Controllers/ArticleController.php). Một bài viết có nhiều nhãn và một nhãn có thể gán cho nhiều bài viết.
  - Em xây dựng bảng trung gian `article_tag` chứa khóa ngoại `article_id` và `tag_id`.
  - Định nghĩa mối quan hệ bằng phương thức `belongsToMany()` trong cả hai Model [Article.php](../app/Models/Article.php) và [Tag.php](../app/Models/Tag.php).

### Câu 2.6: Eager Loading là gì và tại sao lại dùng?
- **Trả lời:**
  - Eager Loading giúp nạp trước các dữ liệu liên quan thông qua phương thức `with()` (ví dụ: `Article::with(['user', 'tags'])->get()` trong [ArticleController.php](../app/Http/Controllers/ArticleController.php)).
  - Mục đích là giải quyết lỗi truy vấn **N+1 query**, gom nhiều truy vấn lẻ thành một vài truy vấn chính nhằm giảm tải cho cơ sở dữ liệu và tăng tốc độ tải trang.

### Câu 2.7: Cơ chế cập nhật trạng thái đơn hàng và gửi email hoạt động ra sao?
- **Trả lời:**
  - Quy trình xử lý nghiệp vụ đơn hàng được tách ra khỏi Controller và đặt vào lớp nghiệp vụ [OrderService.php](../app/Services/OrderService.php).
  - Khi trạng thái đơn hàng thay đổi, [OrderService.php](../app/Services/OrderService.php) tiến hành cập nhật bảng `orders`, tạo bản ghi lịch sử trạng thái mới trong bảng `order_status_histories`, sau đó phát sự kiện [OrderStatusUpdated.php](../app/Events/OrderStatusUpdated.php).
  - Bộ lắng nghe sự kiện [SendOrderStatusUpdatedMail.php](../app/Listeners/SendOrderStatusUpdatedMail.php) sẽ bắt sự kiện này và tự động gửi email thông báo định dạng HTML qua lớp [OrderStatusUpdatedMail.php](../app/Mail/OrderStatusUpdatedMail.php) cho khách hàng.

### Câu 2.8: Thanh toán trực tuyến VNPay hoạt động như thế nào?
- **Trả lời:**
  - Khi khách hàng nhấn thanh toán, hệ thống sử dụng [VnpayPaymentService.php](../app/Services/VnpayPaymentService.php) sắp xếp các tham số, băm chữ ký bảo mật secureHash theo chuẩn SHA512 và redirect khách hàng sang cổng VNPay Sandbox.
  - Sau khi khách hàng thanh toán xong, VNPay điều hướng về trang kết quả trong [ShopCheckoutController.php](../app/Http/Controllers/ShopCheckoutController.php) (phương thức `vnpayReturn`) để báo cho khách hàng và đồng thời gửi thông tin ngầm về máy chủ qua IPN (phương thức `ipn`) để đối soát số tiền, kiểm tra chữ ký và cập nhật trạng thái đơn hàng thành `processing` và trạng thái thanh toán thành `paid`.

### Câu 2.9: Chatbot hỗ trợ khách hàng hoạt động như thế nào?
- **Trả lời:**
  - Em sử dụng package `laravel/ai` cấu hình kết nối trực tiếp tới mô hình Groq Llama 3 / Gemini.
  - Khi người dùng nhắn tin, [ChatController.php](../app/Http/Controllers/ChatController.php) lưu tin nhắn vào database, sau đó chuyển tin nhắn sang Agent [SupportBot.php](../app/Ai/Agents/SupportBot.php) xử lý.
  - Đặc biệt, chatbot tích hợp 3 Tools: [SearchProducts.php](../app/Ai/Tools/SearchProducts.php), [GetProductDetails.php](../app/Ai/Tools/GetProductDetails.php), và [ListCategories.php](../app/Ai/Tools/ListCategories.php) giúp AI tự động gọi cơ sở dữ liệu thật của dự án để tìm kiếm và trả về thông tin sản phẩm chuẩn xác nhất cho khách hàng.
- **Mẹo vấn đáp:** Đăng nhập tài khoản Admin/Editor, mở khung chat lên và thử hỏi: *"Tìm sản phẩm áo thun"* hoặc *"Kiểm tra danh mục sản phẩm của cửa hàng"*, AI sẽ tự gọi công cụ và liệt kê thông tin.
