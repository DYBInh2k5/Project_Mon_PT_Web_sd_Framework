# 11. Hướng Dẫn Toàn Diện Về Dự Án (Full Project Guide)

## 1. Tên đề tài môn học

**XÂY DỰNG ỨNG DỤNG PHẦN QUẢN TRỊ CỦA WEBSITE BÁN HÀNG BẰNG LARAVEL**

Dự án tập trung triển khai và làm nổi bật các chức năng sau:
- **Xác thực người dùng:** Đăng ký, đăng nhập, quên mật khẩu và đổi mật khẩu.
- **Middleware phân quyền & tuổi:** Kiểm tra vai trò của tài khoản truy cập (`EnsureUserHasRole`) và lọc độ tuổi (`CheckAge`).
- **Phân cấp vai trò người dùng (Role):** `admin`, `editor`, `user` kết hợp phân quyền tuyến đường.
- **Quản lý người dùng:** CRUD danh sách tài khoản và cập nhật hồ sơ cá nhân (`Profile`) đi kèm thông qua quan hệ 1-1.
- **Quản lý danh mục & sản phẩm:** CRUD danh mục và sản phẩm (tải lên/xóa hình ảnh trên đĩa cứng) qua quan hệ 1-N.
- **Quản lý đơn hàng:** Xem danh sách, tìm kiếm nâng cao, lọc theo trạng thái/ngày, xem chi tiết mặt hàng và khách hàng.
- **Tối ưu hóa đơn hàng:** Tách rời logic Controller qua `OrderService`, ghi vết lịch sử trạng thái trong bảng `order_status_histories`, phát sự kiện gửi mail thông báo tự động cho khách hàng bằng mô hình Event & Listener.
- **Cửa hàng công khai (Public Shop):** Xem sản phẩm, tìm kiếm, lọc theo danh mục, quản lý giỏ hàng thông qua Session và đặt hàng.
- **Thanh toán trực tuyến VNPay:** Tích hợp liên kết cổng thanh toán Sandbox VNPay, xác nhận giao dịch qua returnUrl (Frontend hiển thị) và ipnUrl (Backend đối soát ẩn bảo mật).
- **Trợ lý AI hỗ trợ khách hàng (Chatbot Agent):** Tích hợp package `laravel/ai` và mô hình Gemini AI, lưu lịch sử trò chuyện trong database qua quan hệ 1-N (`AgentConversation` và `AgentConversationMessage`), hỗ trợ gọi công cụ tự động (Function Calling: `SearchProducts`, `GetProductDetails`, `ListCategories`).
- **Demo quan hệ Nhiều-Nhiều (Many-to-Many):** Bài viết (`articles`) và nhãn (`tags`) kết nối qua bảng trung gian `article_tag` và tối ưu truy vấn bằng Eager Loading.

---

## 2. Công nghệ sử dụng trong dự án

- **Framework chính:** Laravel 12
- **Phiên bản PHP:** PHP 8.2 trở lên
- **Template Engine:** Blade Template (Giao diện sạch sẽ, chuyên nghiệp)
- **CSS Utility:** Tailwind CSS
- **JS Interactivity:** Alpine.js
- **Database Engine:** SQLite (Lưu trữ tệp tin)

---

## 3. Cách chạy dự án dưới máy cục bộ

### Bước 1: Mở terminal tại thư mục gốc của dự án
Đảm bảo bạn đang đứng tại thư mục:
```text
D:\HSU\2533Semester 3(2025-2026)\Phát triển Web sd Framework\Project
```

### Bước 2: Cài đặt các thư viện PHP (dependencies)
```powershell
composer install
```

### Bước 3: Cài đặt các thư viện Frontend (node_modules)
```powershell
npm install
```

### Bước 4: Tạo file môi trường `.env`
```powershell
copy .env.example .env
```
*Mẹo:* Đảm bảo các cấu hình VNPay và Gemini API Key được khai báo đầy đủ trong `.env`.

### Bước 5: Tạo mã khóa ứng dụng (App Key)
```powershell
php artisan key:generate
```

### Bước 6: Chạy cấu trúc bảng dữ liệu (Migrations)
```powershell
php artisan migrate
```

### Bước 7: Đổ dữ liệu thử nghiệm ban đầu (Seeders)
```powershell
php artisan db:seed
```

### Bước 8: Khởi chạy dự án (Local Server)
Do máy tính có thể chưa cài đặt PHP toàn cục (Global), bạn hãy sử dụng bộ PHP cục bộ đi kèm dự án trong thư mục [tools/php/](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/tools/php/) để khởi chạy:

#### 1. Khởi chạy Laravel Web Server (Cửa sổ Terminal thứ nhất)
Mở cửa sổ PowerShell tại thư mục dự án và chạy:
```powershell
.\tools\php\php.exe -c .\tools\php\php.ini -S 127.0.0.1:8000 -t public
```
*Mẹo:* Hoặc bạn có thể thêm tạm thời thư mục PHP cục bộ vào phiên làm việc hiện tại của PowerShell bằng lệnh `$env:Path += ";$(Get-Location)\tools\php"` rồi chạy lệnh chuẩn `php artisan serve`.

#### 2. Khởi chạy Vite Dev Server (Cửa sổ Terminal thứ hai)
Mở một cửa sổ PowerShell mới tại thư mục dự án và chạy:
```powershell
npm run dev
```
Mở trình duyệt truy cập đường dẫn: `http://127.0.0.1:8000`.

### Bước 9: Cách tắt dự án (Shutdown)
Để dừng chạy dự án hoàn toàn:
1. Tại cửa sổ chạy Laravel Server (Terminal 1), nhấn tổ hợp phím `Ctrl + C` để tắt Web Server.
2. Tại cửa sổ chạy Vite Server (Terminal 2), nhấn tổ hợp phím `Ctrl + C` (gõ `Y` và nhấn `Enter` nếu hệ thống hỏi xác nhận) để tắt trình biên dịch frontend.

---

## 4. Tài khoản thử nghiệm có sẵn (Demo Accounts)

Sau khi chạy câu lệnh `db:seed`, hệ thống khởi tạo sẵn các tài khoản sau với mật khẩu mặc định là `password`:
- **Tài khoản Admin:** `admin@example.com`
  - Vai trò: `admin`
  - Quyền hạn: Quản lý người dùng, xem và sửa thông tin profile người dùng.
- **Tài khoản Editor:** `support@example.com`
  - Vai trò: `editor`
  - Quyền hạn: Quản lý danh mục, sản phẩm, và đơn hàng.
- **Tài khoản User thông thường:** Khách hàng đăng ký trên giao diện.

---

## 5. Mối quan hệ giữa các thực thể (Eloquent Relationships)

- **User ↔ Profile (1-1):** `User hasOne Profile` và `Profile belongsTo User`. Sử dụng để mở rộng thông tin cá nhân mở rộng mà không làm phình bảng `users`.
- **ProductCategory ↔ Product (1-N):** `ProductCategory hasMany Product` và `Product belongsTo ProductCategory`. Dùng để hiển thị danh mục sản phẩm và lọc sản phẩm.
- **Order ↔ OrderItem (1-N):** `Order hasMany OrderItem`. Một đơn hàng chứa nhiều sản phẩm đặt mua.
- **Product ↔ OrderItem (1-N):** `Product hasMany OrderItem` và `OrderItem belongsTo Product`. Link thông tin chi tiết sản phẩm đã mua.
- **Order ↔ OrderStatusHistory (1-N):** `Order hasMany OrderStatusHistory`. Lưu lịch sử thay đổi trạng thái đơn hàng.
- **Article ↔ Tag (Many-to-Many):** `Article belongsToMany Tag` và `Tag belongsToMany Article` kết nối qua bảng trung gian `article_tag`.

---

## 6. Cơ chế hoạt động của Chatbot AI và Cổng thanh toán VNPay

### 6.1. Chatbot AI hỗ trợ
- Hệ thống không sử dụng session để lưu lịch sử chat tạm thời, thay vào đó tạo bảng `agent_conversations` và `agent_conversations_messages` trong database để lưu vết hội thoại bền vững cho mỗi tài khoản người dùng đăng nhập.
- Khi gửi tin nhắn qua `POST /chat/send`, `ChatController` đón nhận, chuyển tiếp cho Agent `SupportBot`.
- AI sử dụng cơ chế Function Calling qua các Tool đã đăng ký (`SearchProducts`, `GetProductDetails`, `ListCategories`) để tự động tra cứu dữ liệu thật từ SQLite.

### 6.2. Thanh toán trực tuyến VNPay
- Giao dịch thanh toán được mã hóa chữ ký HMAC SHA512 bằng khóa bảo mật secret key.
- Khi checkout, hệ thống sinh URL thanh toán VNPay và redirect khách hàng sang cổng VNPay.
- Sau khi thanh toán, VNPay điều hướng về `returnUrl` trên trình duyệt để hiển thị kết quả cho khách hàng, đồng thời gửi request IPN (`ipnUrl`) ẩn phía backend-to-backend để đối soát an toàn, phòng ngừa lỗi mất gói tin hoặc khách tắt trình duyệt giữa chừng.

---

## 7. Các câu trả lời vấn đáp ngắn gọn phục vụ thi cử

### Hỏi: Tại sao phải tách nghiệp vụ đơn hàng ra lớp `OrderService`?
- **Trả lời:** Nếu để toàn bộ logic validate dữ liệu, cập nhật cơ sở dữ liệu, ghi nhật ký thay đổi và gửi mail thông báo trực tiếp trong Controller, Controller sẽ bị phình to (Fat Controller) và rất khó kiểm thử hoặc tái sử dụng. Việc tách logic đơn hàng ra `OrderService` giúp giữ cho Controller luôn gọn gàng (Skinny Controller), chỉ làm nhiệm vụ điều phối request và response.

### Hỏi: Vai trò của Event và Listener trong luồng gửi email là gì?
- **Trả lời:** Event và Listener giúp thực hiện lập trình hướng sự kiện (Event-Driven Development) và tách rời logic nghiệp vụ. Khi đơn hàng đổi trạng thái, Service chỉ cần phát đi sự kiện `OrderStatusUpdated`. Tác vụ gửi email (vốn tốn thời gian kết nối server mail) sẽ được giao cho Listener xử lý ở chế độ nền (Queue), giúp phản hồi giao diện tải nhanh hơn.

### Hỏi: Eager Loading giải quyết vấn đề gì và dùng như thế nào?
- **Trả lời:** Eager Loading giải quyết lỗi truy vấn **N+1 query** (lỗi thực hiện quá nhiều câu lệnh SELECT lặp lại trong vòng lặp hiển thị dữ liệu quan hệ). Em sử dụng phương thức `with()` trong Eloquent (ví dụ: `Article::with(['user', 'tags'])->get()`) để nạp trước toàn bộ dữ liệu quan hệ vào bộ nhớ chỉ trong 1 hoặc 2 câu truy vấn duy nhất.
