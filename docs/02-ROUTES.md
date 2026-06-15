# 02. Tuyến Đường (Routes)

## 1. Hai file cấu hình tuyến đường chính

Hệ thống quản lý định nghĩa tuyến đường trong hai file chính nằm ở thư mục `routes/`:

- [routes/web.php](../routes/web.php) - Các tuyến đường phục vụ giao diện Web chính của ứng dụng và khu vực quản trị.
- [routes/auth.php](../routes/auth.php) - Các tuyến đường chuyên biệt dành cho xác thực (được nạp vào ở cuối file `web.php`).

## 2. Danh sách tuyến đường chi tiết trong `web.php`

### Tuyến đường Công khai (Public - Không yêu cầu đăng nhập)

- `/` hoặc `/shop` - Trang chủ cửa hàng công khai hiển thị danh sách sản phẩm, hỗ trợ tìm kiếm và lọc.
- `/shop/products/{product}` - Xem chi tiết một sản phẩm cụ thể.
- `/cart` - Xem giỏ hàng hiện tại.
- `/checkout` - Trang điền thông tin đơn hàng và chọn cổng thanh toán.
- `/checkout/vnpay/return` - URL nhận phản hồi trực tiếp của cổng thanh toán VNPay trên trình duyệt (returnUrl).
- `/checkout/vnpay/ipn` - URL nhận phản hồi ngầm từ VNPay (IPN) để tự động cập nhật trạng thái đơn hàng an toàn.
- `/check_fail` - Trang hiển thị thông báo kiểm tra tuổi thất bại (khi tuổi từ 200 trở lên).
- `/check_age/{age?}` - Tuyến đường kiểm tra middleware tuổi (`CheckAge`).
- `/articles` - Hiển thị danh sách bài viết và nhãn bài viết (Many-to-Many).
- `/articles/{article}` - Chi tiết bài viết.

### Tuyến đường Yêu cầu Đăng nhập (Middleware: `auth`)

#### Khu vực trang chủ quản trị & thông tin cá nhân
- `/dashboard` - Trang tổng quan quản trị hiển thị các biểu đồ thống kê cơ bản.
- `/profile` - Trang hiển thị thông tin cá nhân (Profile) của người dùng hiện tại.
- `/settings/profile` - Chỉnh sửa thông tin cá nhân hoặc xóa tài khoản.
- `/settings/password` - Đổi mật khẩu tài khoản.

#### Quản lý người dùng (Middleware: `role:admin`)
- `/users` - Danh sách người dùng, hỗ trợ tìm kiếm và bộ lọc vai trò/trạng thái.
- `/users/create` - Form tạo tài khoản người dùng mới đi kèm tạo profile tự động.
- `/users/{user}` - Xem chi tiết thông tin và hồ sơ cá nhân của người dùng.
- `/users/{user}/edit` - Form chỉnh sửa thông tin tài khoản và profile người dùng.
- `PATCH /users/{user}/status` - Chuyển nhanh trạng thái kích hoạt hoạt động tài khoản (`toggleStatus`).

#### Quản lý danh mục & sản phẩm (Middleware: `role:editor,admin`)
- `/product-categories` - Quản lý danh mục sản phẩm (CRUD).
- `/products` - Quản lý sản phẩm (CRUD), hỗ trợ tải ảnh lên và xóa file ảnh cũ.

#### Quản lý đơn hàng (Middleware: `role:editor,admin`)
- `/orders` - Xem danh sách đơn đặt hàng từ khách, hỗ trợ tìm kiếm và lọc theo trạng thái/ngày tháng.
- `/orders/{order}` - Chi tiết đơn đặt hàng bao gồm danh sách mặt hàng đã mua và lịch sử trạng thái đơn.
- `PATCH /orders/{order}/status` - Cập nhật trạng thái đơn hàng và gửi email tự động (`updateStatus`).

#### Hỗ trợ trực tuyến & Chatbot AI (Middleware: `role:user,editor,admin`)
- `/support-chat` - Giao diện trò chuyện trực tuyến với Bot hỗ trợ AI.
- `POST /chat/send` hoặc `POST /support-chat` - Nhận tin nhắn từ giao diện, lưu vào database và gọi API Gemini xử lý.
- `POST /support-chat/clear` - Xóa lịch sử hội thoại của người dùng trong hệ thống.

#### Kiểm tra chức năng phân quyền vai trò (Role Demo)
- `/role-demo` - Trang tổng hợp liên kết thử nghiệm.
- `/role-demo/admin` - Route chỉ cho phép vai trò `admin` truy cập.
- `/role-demo/editor` - Route cho phép vai trò `editor` hoặc `admin` truy cập.
- `/role-demo/user` - Route cho phép vai trò `user`, `editor` hoặc `admin` truy cập.

## 3. Danh sách tuyến đường xác thực trong `auth.php`

Mọi tuyến đường trong file này được gom nhóm bằng các middleware cụ thể:
- **Middleware: `guest` (Dành cho khách chưa đăng nhập)**
  - `register` - Đăng ký tài khoản mới.
  - `login` - Đăng nhập vào hệ thống.
  - `forgot-password` - Yêu cầu gửi email liên kết đặt lại mật khẩu.
  - `reset-password` - Nhận mã và thực hiện thay đổi mật khẩu mới.
- **Middleware: `auth` (Dành cho người dùng đã đăng nhập)**
  - `verify-email` - Trang xác minh địa chỉ email và gửi lại link kích hoạt.
  - `confirm-password` - Trang yêu cầu nhập lại mật khẩu trước khi thực hiện hành động nhạy cảm.
  - `logout` (POST) - Đăng xuất khỏi hệ thống.

## 4. Cách đọc hiểu cấu trúc định nghĩa tuyến đường

### Ví dụ 1: Tuyến đường Quản lý Người dùng
```php
Route::resource('users', UserController::class)->middleware('role:admin');
```
**Giải nghĩa:**
- Sử dụng phương thức `Route::resource` để tự động tạo ra 7 định nghĩa tuyến đường chuẩn RESTful (index, create, store, show, edit, update, destroy) cho nguồn dữ liệu `users`.
- Toàn bộ 7 tuyến đường này đều sử dụng Controller xử lý là `UserController`.
- Áp dụng middleware phân quyền `role:admin` ở phía sau, có nghĩa là chỉ những tài khoản có trường `role = 'admin'` trong database mới được quyền truy cập.

### Ví dụ 2: Tuyến đường Công khai mặt tiền Shop
```php
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
```
**Giải nghĩa:**
- Đây là tuyến đường xử lý phương thức HTTP `GET` cho đường dẫn `/shop`.
- Khi người dùng truy cập, hệ thống sẽ thực hiện gọi phương thức `index` bên trong lớp `ShopController`.
- Tuyến đường này được gán tên định danh (name) là `shop.index` để dễ dàng tạo URL động trong các file Blade view bằng cú pháp `route('shop.index')`.

## 5. Luồng xử lý một Request đi qua Tuyến đường

```text
Người dùng truy cập URL 
  ↓
Laravel đối chiếu URL với file Routes
  ↓
Chạy qua các lớp Middleware đăng ký (Xác thực Auth, Phân quyền Role, Lọc tuổi...)
  ↓
Request hợp lệ đi vào Controller tương ứng
  ↓
Controller xử lý nghiệp vụ, giao tiếp Model lấy dữ liệu
  ↓
Controller trả về View giao diện hoặc thực hiện Redirect
```
