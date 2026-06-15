# 🛒 Xây Dựng Ứng Dụng Quản Trị Website Bán Hàng (Laravel 12)

Chào mừng bạn đến với dự án kết thúc môn học **Phát triển Web sử dụng Framework**. Dự án được xây dựng trên nền tảng **Laravel 12**, **SQLite**, tích hợp **Chatbot AI (Gemini)** hỗ trợ khách hàng và cổng thanh toán trực tuyến **VNPay**.

---

## 🗺️ Bản Đồ Tài Liệu Hướng Dẫn & Ôn Thi Vấn Đáp (docs/)

Toàn bộ tài liệu hướng dẫn đã được chuẩn hóa Tiếng Việt có dấu, cập nhật chi tiết luồng xử lý mới và bổ sung nội dung ôn thi vấn đáp:

1. **[00-README.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/00-README.md)** - Định hướng và phân loại tài liệu hướng dẫn.
2. **[01-OVERVIEW.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/01-OVERVIEW.md)** - Tổng quan đề tài, mục tiêu và danh sách các tính năng.
3. **[02-ROUTES.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/02-ROUTES.md)** - Danh sách toàn bộ các tuyến đường (Route), quyền truy cập và middleware.
4. **[03-DATABASE.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/03-DATABASE.md)** - Cấu trúc các bảng cơ sở dữ liệu SQLite, các mối quan hệ (Relationships) và tối ưu hóa index.
5. **[04-AUTH-ROLE-PROFILE.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/04-AUTH-ROLE-PROFILE.md)** - Luồng xác thực, phân quyền Middleware (`role:admin`, `role:editor`, `role:user`) và cập nhật Profile.
6. **[05-PRODUCTS-USERS-UI.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/05-PRODUCTS-USERS-UI.md)** - Chức năng CRUD Danh mục, Sản phẩm (kèm hình ảnh), tài khoản và tùy biến giao diện.
7. **[06-ORAL-EXAM-NOTES.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/06-ORAL-EXAM-NOTES.md)** - Những câu hỏi vấn đáp cốt lõi và cách trả lời thông minh.
8. **[07-ARTICLE-TAG-FACTORY-SEEDING.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/07-ARTICLE-TAG-FACTORY-SEEDING.md)** - Tính năng bài viết & thẻ bài viết (mối quan hệ Many-to-Many), Factory và Seeder.
9. **[08-CONTROLLER-BY-CONTROLLER.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/08-CONTROLLER-BY-CONTROLLER.md)** - Hướng dẫn chi tiết từng Controller xử lý request trong dự án.
10. **[09-ORDERS-CHATBOT-PAYMENT.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/09-ORDERS-CHATBOT-PAYMENT.md)** - Luồng giỏ hàng, đặt hàng, gửi mail sự kiện, tích hợp thanh toán VNPay và Chatbot hỗ trợ.
11. **[10-LARAVEL-OPTIMIZATION.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/10-LARAVEL-OPTIMIZATION.md)** - Các kỹ thuật tối ưu hóa mã nguồn (Eager Loading, Queue, Service Pattern).
12. **[11-FULL-PROJECT-GUIDE.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/11-FULL-PROJECT-GUIDE.md)** - Hướng dẫn chạy dự án nhanh và danh sách tài khoản dùng thử.
13. **[13-CHATBOT-AGENT-GUIDE.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/13-CHATBOT-AGENT-GUIDE.md)** - Chi tiết cách cấu hình và luồng Agent AI hoạt động.
14. **[14-TAI-LIEU-VAN-DAP-CHI-TIET.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/14-TAI-LIEU-VAN-DAP-CHI-TIET.md)** - Ngân hàng câu hỏi vấn đáp chi tiết nhất cho kì thi.
15. **[15-ARCHITECTURE-OVERVIEW.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/15-ARCHITECTURE-OVERVIEW.md)** - Sơ đồ kiến trúc phân lớp của dự án.
16. **[16-TONG-QUAN-VA-CAI-DAT.md](file:///d:/HSU/2533Semester%203(2025-2026)/Ph%C3%A1t%20tri%E1%BB%83n%20Web%20sd%20Framework/Project/docs/16-TONG-QUAN-VA-CAI-DAT.md)** - Tóm tắt cài đặt nhanh cho hệ thống.

---

## ⚡ Hướng Dẫn Chạy Nhanh Dự Án

### 1. Chuẩn bị Môi trường
- PHP >= 8.2 (đầy đủ extension `pdo_sqlite`, `openssl`, `mbstring`, `zip`, `fileinfo`).
- Composer.
- Node.js & NPM.

### 2. Cài đặt các gói phụ thuộc
```bash
composer install
npm install
```

### 3. Cấu hình môi trường (`.env`)
Tạo file `.env` bằng cách copy từ `.env.example`:
```bash
cp .env.example .env
```
Đảm bảo đã thiết lập kết nối cơ sở dữ liệu SQLite:
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### 4. Tạo cơ sở dữ liệu và nạp dữ liệu mẫu
```bash
# Tạo file sqlite nếu chưa có
touch database/database.sqlite

# Migrate và seed dữ liệu mẫu
php artisan migrate:fresh --seed
```

### 5. Khởi chạy Server
- Chạy PHP Dev Server:
```bash
php artisan serve
```
- Chạy Vite để compile các assets CSS/JS:
```bash
npm run dev
```

---

## 🧪 Chạy Bộ Kiểm Thử (Testing)

Dự án đi kèm bộ kiểm thử tự động gồm 61 test cases bảo đảm toàn bộ logic nghiệp vụ (Auth, Roles, CRUD, Cart, Checkout, VNPay, Chatbot) hoạt động ổn định:
```bash
php artisan test
```

---

## 📝 Tài khoản Đăng nhập Demo

| Vai trò (Role) | Email Đăng nhập | Mật khẩu mặc định | Chức năng truy cập |
|---|---|---|---|
| **Admin** | `admin@example.com` | `password` | Quản lý User, thay đổi Role, xem Profile, và toàn quyền hệ thống. |
| **Editor** | `editor@example.com` | `password` | Quản lý Danh mục, Sản phẩm và Đơn hàng. |
| **User** | `user@example.com` | `password` | Mua sắm, Đặt hàng, Thanh toán VNPay và Chat với AI Support. |
