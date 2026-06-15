# 01. Tổng Quan Dự Án (Overview)

## 1. Mục tiêu của dự án

Dự án này là một ứng dụng quản lý bán hàng hoàn chỉnh xây dựng trên nền tảng framework Laravel 12, được thiết kế để học tập, thực hành và trình diễn các kỹ năng phát triển Web chuyên nghiệp:

- **Hệ thống xác thực:** Đăng nhập, đăng ký tài khoản, quên mật khẩu và xác minh email.
- **Phân quyền người dùng (Authorization):** Sử dụng Role (Vai trò) để phân cấp truy cập (`admin`, `editor`, `user`).
- **Quản lý người dùng & Hồ sơ:** Xem danh sách, tạo mới, chỉnh sửa thông tin cá nhân và cập nhật ảnh đại diện (avatar) của người dùng thông qua mối quan hệ 1-1 (`User hasOne Profile`).
- **Quản lý sản phẩm & Danh mục:** Quản lý danh mục sản phẩm và sản phẩm (hỗ trợ lưu trữ hình ảnh sản phẩm) theo mô hình 1-N (`ProductCategory hasMany Product`).
- **Quản lý đơn hàng:** Xem danh sách, lọc theo ngày/trạng thái, cập nhật trạng thái đơn hàng, lưu vết lịch sử trạng thái và tự động gửi email thông báo cho khách hàng.
- **Mặt tiền cửa hàng (Public Shop):** Trang hiển thị sản phẩm công khai, hỗ trợ tìm kiếm, lọc theo danh mục, thêm vào giỏ hàng, cập nhật số lượng và tiến hành đặt hàng.
- **Thanh toán trực tuyến VNPay:** Tích hợp môi trường thử nghiệm (Sandbox) của cổng thanh toán điện tử VNPay với quy trình bảo mật chữ ký và xác nhận thông qua returnUrl và ipnUrl.
- **Trợ lý AI hỗ trợ khách hàng (Chatbot Agent):** Sử dụng package `laravel/ai` kết hợp với mô hình Gemini AI để trò chuyện, giải đáp câu hỏi và hỗ trợ kiểm tra thông tin đơn hàng thực tế của khách thông qua các Tool API tích hợp.
- **Demo quan hệ Many-to-Many:** Hiển thị danh sách bài viết (Articles) và thẻ nhãn (Tags) bằng Eloquent ORM tối ưu tránh lỗi truy vấn N+1 query.

## 2. Công nghệ sử dụng trong dự án

- **Backend Framework:** Laravel 12
- **PHP Version:** PHP 8.2 trở lên
- **Template Engine:** Blade Template
- **Styling (CSS):** Tailwind CSS
- **Frontend Logic:** Alpine.js
- **Database:** SQLite (Hỗ trợ cấu hình chuyển hướng file tạm để tránh lỗi khóa đĩa ghi `disk I/O error` trên hệ thống Windows)

## 3. Các thư mục quan trọng

- `app/Http/Controllers` - Chứa các file Controller xử lý các request từ client và điều phối logic nghiệp vụ.
- `app/Models` - Chứa các thực thể Model ánh xạ tới cơ sở dữ liệu thông qua Eloquent ORM.
- `app/Http/Middleware` - Chứa các bộ lọc request đầu vào (như kiểm tra phân quyền `EnsureUserHasRole` hay lọc độ tuổi `CheckAge`).
- `app/Services` - Tách biệt các logic nghiệp vụ phức tạp (như giỏ hàng, thanh toán VNPay, cập nhật đơn hàng và gọi API Gemini).
- `app/Ai` - Chứa Agent và các Tool thực hiện Function Calling cho Chatbot hỗ trợ khách hàng.
- `resources/views` - Chứa mã nguồn giao diện giao diện hiển thị HTML/Blade.
- `routes` - Định nghĩa toàn bộ các tuyến đường (Route) của hệ thống (`web.php` và `auth.php`).
- `database/migrations` - Quản lý cấu trúc các bảng trong cơ sở dữ liệu.
- `database/seeders` - Đổ dữ liệu mẫu ban đầu để chạy thử nghiệm dự án.

## 4. Các Model hiện có

- `User` - Người dùng hệ thống (chứa vai trò `role` và trạng thái `is_active`).
- `Profile` - Thông tin cá nhân chi tiết của người dùng (Họ tên, địa chỉ, số điện thoại, ngày sinh, giới tính, avatar).
- `ProductCategory` - Danh mục sản phẩm (Tên danh mục, mô tả, slug).
- `Product` - Sản phẩm (Tên sản phẩm, giá bán, tồn kho, hình ảnh, sku, slug).
- `Order` - Đơn đặt hàng (Mã đơn hàng, thông tin giao hàng, tổng tiền, trạng thái đơn hàng, trạng thái thanh toán).
- `OrderItem` - Chi tiết sản phẩm trong đơn hàng.
- `OrderStatusHistory` - Ghi vết lịch sử thay đổi trạng thái đơn hàng.
- `Article` - Bài viết.
- `Tag` - Nhãn bài viết.
- `AgentConversation` - Phiên trò chuyện của người dùng với trợ lý chatbot.
- `AgentConversationMessage` - Chi tiết các tin nhắn trong phiên trò chuyện.

## 5. Các chức năng chính đã hoàn thành

- **Hệ thống Auth đồng bộ:** Đăng nhập, đăng ký, quên mật khẩu được thiết kế nhất quán với giao diện chung của shop công khai.
- **Quản lý danh mục & sản phẩm (CRUD):** Phân quyền chỉ cho vai trò `editor` hoặc `admin` thực hiện. Cho phép upload ảnh sản phẩm và tự động xóa ảnh cũ khi cập nhật.
- **Quy trình xử lý đơn hàng chuyên nghiệp:** Tách biệt logic nghiệp vụ từ Controller sang `OrderService`, ghi lại vết lịch sử trạng thái của đơn hàng và phát event gửi email thông báo tự động.
- **Thanh toán VNPay:** Khách hàng đặt hàng xong sẽ được chuyển hướng sang cổng thanh toán thử nghiệm của VNPay, sau đó phản hồi kết quả về trang returnUrl và xử lý IPN ẩn để đảm bảo tính an toàn của giao dịch.
- **Trợ lý AI Chatbot:** Người dùng trao đổi trực tiếp với AI thông qua cửa sổ chat. Trợ lý AI có khả năng tự động tra cứu mã đơn hàng thật trong cơ sở dữ liệu nếu phát hiện mã đơn hàng trong nội dung hỏi của khách hàng.
- **Blade Component Alert:** Tạo component dùng chung hiển thị các thông báo thành công hoặc lỗi biểu mẫu trong toàn bộ dự án.

## 6. Các file quan trọng cần lưu ý khi thi vấn đáp

- [routes/web.php](../routes/web.php) - File cấu hình tuyến đường chính của ứng dụng.
- [app/Http/Middleware/EnsureUserHasRole.php](../app/Http/Middleware/EnsureUserHasRole.php) - Middleware thực hiện phân quyền truy cập.
- [app/Http/Controllers/UserController.php](../app/Http/Controllers/UserController.php) - Quản lý người dùng và cập nhật profile đi kèm.
- [app/Services/OrderService.php](../app/Services/OrderService.php) - Service xử lý logic cập nhật trạng thái đơn hàng.
- [app/Events/OrderStatusUpdated.php](../app/Events/OrderStatusUpdated.php) - Sự kiện phát đi khi trạng thái đơn hàng thay đổi.
- [app/Listeners/SendOrderStatusUpdatedMail.php](../app/Listeners/SendOrderStatusUpdatedMail.php) - Bộ lắng nghe sự kiện để thực hiện gửi email thông báo.
- [app/Services/VnpayPaymentService.php](../app/Services/VnpayPaymentService.php) - Tạo liên kết và kiểm tra chữ ký xác thực cổng VNPay.
- [app/Ai/Agents/SupportBot.php](../app/Ai/Agents/SupportBot.php) - Cấu hình hệ thống chỉ dẫn và các công cụ cho Bot trợ lý AI.
