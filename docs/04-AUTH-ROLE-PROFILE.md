# 04. Xác Thực, Phân Quyền Và Hồ Sơ Người Dùng (Auth, Role, Profile)

## 1. Hệ thống Xác thực (Auth)

Các lớp xử lý logic đăng nhập, đăng ký và xác thực tài khoản nằm trong thư mục:
- `app/Http/Controllers/Auth/`

Danh sách các Controller xác thực đang hoạt động trong dự án:
- [LoginController.php](../app/Http/Controllers/Auth/LoginController.php) - Xử lý đăng nhập tài khoản và đăng xuất.
- [RegistrationController.php](../app/Http/Controllers/Auth/RegistrationController.php) - Đăng ký tài khoản người dùng mới và tạo profile mặc định.
- [PasswordResetLinkController.php](../app/Http/Controllers/Auth/PasswordResetLinkController.php) - Gửi liên kết yêu cầu đặt lại mật khẩu qua email.
- [NewPasswordController.php](../app/Http/Controllers/Auth/NewPasswordController.php) - Xử lý lưu mật khẩu mới sau khi đặt lại thành công.
- [ConfirmationController.php](../app/Http/Controllers/Auth/ConfirmationController.php) - Yêu cầu xác nhận mật khẩu hiện tại trước khi thực hiện các hành động bảo mật cao.

## 2. Quản lý Vai trò (Role)

Hệ thống hỗ trợ phân quyền người dùng thông qua trường `role` trong cơ sở dữ liệu:
- Quản lý logic tại: [User.php](../app/Models/User.php)
- Cột lưu trữ: `role` trong bảng dữ liệu `users`.

**Các giá trị vai trò được chấp nhận:**
- `admin` - Quản trị viên cấp cao nhất: Toàn quyền truy cập hệ thống, quản lý tài khoản người dùng, đổi trạng thái và chỉnh sửa thông tin hồ sơ của mọi tài khoản.
- `editor` - Quản trị viên nội dung: Được quyền quản lý danh mục sản phẩm, sản phẩm và quản lý các đơn đặt hàng từ khách hàng.
- `user` - Khách hàng thành viên: Có quyền mua sắm sản phẩm, xem lịch sử đơn hàng cá nhân, cập nhật profile riêng và trò chuyện với chatbot trợ lý AI.

## 3. Middleware Phân quyền Role

Bộ lọc phân quyền được triển khai tại file:
- [EnsureUserHasRole.php](../app/Http/Middleware/EnsureUserHasRole.php)

**Quy trình hoạt động của middleware:**
1. Trích xuất thông tin người dùng hiện tại đang đăng nhập từ Session.
2. Đọc danh sách các vai trò (roles) được cho phép khai báo tại Tuyến đường (Route).
3. Nếu vai trò của người dùng trùng khớp với một trong các vai trò được yêu cầu, cho phép request đi qua (`return $next($request)`).
4. Nếu vai trò không khớp hoặc chưa đăng nhập, dừng xử lý ngay lập tức và trả về trang lỗi HTTP 403 (Forbidden).

**Ví dụ cấu hình tuyến đường trong `routes/web.php`:**
```php
// Chỉ Admin mới được vào trang quản trị User
Route::resource('users', UserController::class)->middleware('role:admin');

// Editor hoặc Admin đều có quyền quản lý sản phẩm
Route::resource('products', ProductController::class)->middleware('role:editor,admin');
```

## 4. Middleware Kiểm tra độ tuổi (CheckAge)

Hệ thống tích hợp một middleware demo kiểm tra tuổi người dùng:
- File xử lý: [CheckAge.php](../app/Http/Middleware/CheckAge.php)

**Luồng hoạt động:**
- Đọc giá trị tuổi từ tham số của URL (ví dụ: `/check_age/25`) hoặc từ input của form.
- Nếu tuổi nhập vào lớn hơn hoặc bằng 200 (mốc tuổi không thực tế), hệ thống tự động chuyển hướng người dùng sang trang báo lỗi `/check_fail` (hiển thị view `home.check-age-demo`).
- Nếu tuổi hợp lệ, request được chuyển tiếp đến Controller xử lý bình thường.

---

## 5. Hồ sơ Người Dùng (Profile)

Các Controller tham gia quản lý thông tin hồ sơ cá nhân:
- [Settings/ProfileController.php](../app/Http/Controllers/Settings/ProfileController.php) - Dành cho người dùng tự cập nhật thông tin cá nhân.
- [UserController.php](../app/Http/Controllers/UserController.php) - Dành cho admin quản trị và cập nhật hồ sơ của các tài khoản khác.

**Nguyên tắc nghiệp vụ quan trọng:**
- Dữ liệu hồ sơ (`profiles`) được liên kết chặt chẽ với tài khoản (`users`) qua quan hệ 1-1 của Eloquent ORM.
- Khi người dùng tự cập nhật hoặc admin thay đổi địa chỉ email của tài khoản, trường `email_verified_at` sẽ được tự động thiết lập về `null` để bắt buộc người dùng thực hiện quy trình xác thực email lại nhằm bảo mật.
- Sử dụng phương thức `firstOrCreate` qua relationship để tự động khởi tạo bản ghi hồ sơ trống nếu tài khoản chưa có dữ liệu hồ sơ cá nhân:
  ```php
  $profile = $user->profile()->firstOrCreate([]);
  ```

## 6. Mối quan hệ 1-1: User ↔ Profile

**Định nghĩa trong mã nguồn:**
- Tại Model [User.php](../app/Models/User.php):
  ```php
  public function profile() {
      return $this->hasOne(Profile::class);
  }
  ```
- Tại Model [Profile.php](../app/Models/Profile.php):
  ```php
  public function user() {
      return $this->belongsTo(User::class);
  }
  ```

**Xử lý trong trang quản trị của Admin:**
- Khi Admin truy cập xem chi tiết người dùng tại `/users/{user}`, hệ thống sử dụng Eager Loading nạp đồng thời bảng `profiles` thông qua `load('profile')` để hiển thị đầy đủ thông tin liên hệ.
- Khi chỉnh sửa tại `/users/{user}/edit`, Admin có thể thay đổi song song thông tin tài khoản chính (`name`, `email`, `role`, `is_active`) và các thông tin hồ sơ cá nhân đi kèm (`full_name`, `address`, `birthday`, `gender`, `phone`, `avatar`).
- Ảnh đại diện sau khi được cập nhật sẽ hiển thị đồng bộ ở:
  - Trang cài đặt tài khoản cá nhân.
  - Trang chi tiết hồ sơ người dùng.
  - Khu vực thanh menu Header góc trên bên phải khi đăng nhập.

## 7. Các File giao diện (Blade Views) sử dụng

- [pages/auth/signin.blade.php](../resources/views/pages/auth/signin.blade.php) - Giao diện Đăng nhập.
- [pages/auth/signup.blade.php](../resources/views/pages/auth/signup.blade.php) - Giao diện Đăng ký tài khoản.
- [pages/auth/forgot-password.blade.php](../resources/views/pages/auth/forgot-password.blade.php) - Giao diện Quên mật khẩu.
- [pages/auth/reset-password.blade.php](../resources/views/pages/auth/reset-password.blade.php) - Giao diện đặt lại mật khẩu mới.
- [pages/auth/confirm-password.blade.php](../resources/views/pages/auth/confirm-password.blade.php) - Giao diện xác nhận mật khẩu bảo mật.
- [pages/auth/settings/profile.blade.php](../resources/views/pages/auth/settings/profile.blade.php) - Giao diện thiết lập hồ sơ cá nhân (đổi avatar, địa chỉ, sđt).
- [pages/auth/settings/password.blade.php](../resources/views/pages/auth/settings/password.blade.php) - Giao diện đổi mật khẩu.
- [pages/profile.blade.php](../resources/views/pages/profile.blade.php) - Trang hiển thị thông tin hồ sơ cá nhân công khai.
