# Hướng Dẫn Làm Project - Bản Đồ Ánh Xạ Mã Nguồn

Dưới đây là bảng đối chiếu chi tiết giữa các **Yêu cầu chức năng của Project** (thang điểm 10) và các **Tệp tin mã nguồn (Source files)** tương ứng đang chạy trong dự án. 

---

## 1. Bản Đồ Ánh Xạ Chức Năng & File Code Chi Tiết

### 📌 Chức năng 1: Trang quản lý danh mục sản phẩm (`role: editor` hoặc `admin`)
*   **Tuyến đường (Routes):**
    *   `routes/web.php` (Định nghĩa Resource `product-categories`)
*   **Bộ xử lý (Controller):**
    *   [ProductCategoryController.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Http/Controllers/ProductCategoryController.php)
*   **Xác thực dữ liệu (Form Request):**
    *   [ProductCategoryRequest.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Http/Requests/ProductCategoryRequest.php) (Kiểm tra trùng Slug, tên bắt buộc, ép kiểu hoạt động)
*   **Mô hình dữ liệu (Model):**
    *   [ProductCategory.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Models/ProductCategory.php) (Quan hệ `hasMany` với sản phẩm)
*   **Giao diện (Views):**
    *   `resources/views/product-categories/index.blade.php` (Danh sách danh mục có đếm số sản phẩm bằng `withCount`)
    *   `resources/views/product-categories/create.blade.php` (Màn hình thêm mới)
    *   `resources/views/product-categories/edit.blade.php` (Màn hình cập nhật)
    *   `resources/views/product-categories/show.blade.php` (Xem chi tiết và các sản phẩm con)

---

### 📌 Chức năng 2: Quản lý người dùng, xem & cập nhật hồ sơ (`role: admin`)
*   **Tuyến đường (Routes):**
    *   `routes/web.php` (Định nghĩa Resource `users` và chuyển trạng thái `users.toggle-status`)
*   **Bộ xử lý (Controller):**
    *   [UserController.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Http/Controllers/UserController.php)
*   **Xác thực dữ liệu (Form Requests):**
    *   [StoreUserRequest.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Http/Requests/StoreUserRequest.php) (Validate khi thêm tài khoản mới)
    *   [UpdateUserRequest.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Http/Requests/UpdateUserRequest.php) (Validate khi admin cập nhật thông tin và profile)
*   **Mô hình dữ liệu (Models - Quan hệ 1-1):**
    *   [User.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Models/User.php) (Liên kết `hasOne` tới Profile)
    *   [Profile.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Models/Profile.php) (Lưu trữ: Họ tên, địa chỉ, ảnh đại diện, ngày sinh, giới tính, SĐT)
*   **Giao diện (Views):**
    *   `resources/views/users/index.blade.php` (Bảng danh sách, hiển thị Avatar, vai trò, nút bật tắt status nhanh)
    *   `resources/views/users/edit.blade.php` (Form sửa thông tin người dùng và tải lên Avatar kèm theo)
    *   `resources/views/users/show.blade.php` (Xem toàn bộ hồ sơ chi tiết của người dùng)

---

### 📌 Chức năng 3: Quản lý sản phẩm có hình ảnh (`role: editor` hoặc `admin`)
*   **Tuyến đường (Routes):**
    *   `routes/web.php` (Định nghĩa Resource `products`)
*   **Bộ xử lý (Controller):**
    *   [ProductController.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Http/Controllers/ProductController.php) (Xử lý upload ảnh và xóa file ảnh cũ khi thay thế/xóa sản phẩm)
*   **Xác thực dữ liệu (Form Request):**
    *   [ProductRequest.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Http/Requests/ProductRequest.php) (Giới hạn kích thước file ảnh tối đa 2MB, định dạng mimes)
*   **Mô hình dữ liệu (Model):**
    *   [Product.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Models/Product.php)
*   **Giao diện (Views):**
    *   `resources/views/products/index.blade.php` (Danh sách sản phẩm kèm ảnh thu nhỏ, cột tồn kho, giá tiền)
    *   `resources/views/products/create.blade.php` (Màn hình thêm sản phẩm, cho phép chọn file ảnh tải lên)
    *   `resources/views/products/edit.blade.php` (Màn hình cập nhật sản phẩm)
    *   `resources/views/products/show.blade.php` (Xem chi tiết thông số sản phẩm)

---

### 📌 Chức năng 4: Quản lý đơn đặt hàng (`role: editor` hoặc `admin`)
*   **Yêu cầu chi tiết:**
    *   `[x]` Xem danh sách đơn hàng được sắp xếp mới -> cũ.
    *   `[x]` Lọc danh sách theo trạng thái.
    *   `[x]` Tìm kiếm theo ngày (Từ ngày -> Đến ngày).
    *   `[x]` Tìm kiếm theo thông tin khách hàng (tên, SĐT, email).
    *   `[x]` Xem chi tiết đơn hàng & cập nhật trạng thái đơn hàng.
*   **Tuyến đường (Routes):**
    *   `routes/web.php` (Gồm danh sách `orders.index`, chi tiết `orders.show` và đổi trạng thái `orders.update-status`)
*   **Bộ xử lý (Controller):**
    *   [OrderController.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Http/Controllers/OrderController.php) (Sắp xếp `orderByDesc('placed_at')`)
*   **Lớp Nghiệp vụ chính (Service):**
    *   [OrderService.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Services/OrderService.php) (Xử lý DB transaction để cập nhật trạng thái và ghi nhận lịch sử đổi trạng thái)
*   **Mô hình dữ liệu (Models):**
    *   [Order.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Models/Order.php) (Các scopes: `placedFrom`, `placedUntil`, `status`, `search`)
    *   [OrderItem.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Models/OrderItem.php) (Chi tiết sản phẩm mua)
    *   [OrderStatusHistory.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Models/OrderStatusHistory.php) (Nhật ký lịch sử thay đổi trạng thái đơn)
*   **Giao diện (Views):**
    *   `resources/views/orders/index.blade.php` (Danh sách đơn, thanh tìm kiếm nâng cao theo khoảng ngày/trạng thái)
    *   `resources/views/orders/show.blade.php` (Chi tiết đơn, phần thông tin khách hàng, form đổi trạng thái, và lịch sử trạng thái)

---

### 📌 Chức năng 5: Trang đăng nhập dùng Middleware và phân quyền Role
*   **Tuyến đường (Routes):**
    *   `routes/auth.php` (Định nghĩa login, register, logout)
*   **Tuyến đường phân quyền:**
    *   `routes/web.php` (Nhóm route được bao bọc bởi `auth` và `role:admin` hoặc `role:editor,admin`)
*   **Middleware xử lý:**
    *   [EnsureUserHasRole.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Http/Middleware/EnsureUserHasRole.php) (Bí danh `role` - Kiểm tra quyền hạn trước khi cho phép vào Controller)
    *   [CheckAge.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Http/Middleware/CheckAge.php) (Tích hợp middleware phụ trợ theo slide bài giảng tuần 5)
*   **Khai báo Middleware:**
    *   [app.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/bootstrap/app.php) (Đăng ký `CheckAge` dạng toàn cục và aliased `role`)
*   **Giao diện (Views):**
    *   `resources/views/auth/login.blade.php` (Trang đăng nhập giao diện thiết kế chuyên nghiệp)

---

### 📌 Chức năng 6: Chatbot AI hỗ trợ khách hàng
*   **Tuyến đường (Routes):**
    *   `routes/web.php` (Các route `support-chat.index` và `chat.send`)
*   **Bộ xử lý (Controllers):**
    *   [SupportChatController.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Http/Controllers/SupportChatController.php) (Tải lịch sử chat từ Database)
    *   [ChatController.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Http/Controllers/ChatController.php) (Xử lý lưu tin nhắn, gọi Agent AI và lưu kết quả)
*   **Lớp Nghiệp vụ Agent AI & Tools (Package `laravel/ai`):**
    *   [SupportBot.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Ai/Agents/SupportBot.php) (Chỉ dẫn hệ thống, cấu hình model Groq fallback và khai báo các công cụ bổ trợ)
    *   [SearchProducts.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Ai/Tools/SearchProducts.php) (Công cụ AI tự gọi để tìm sản phẩm theo từ khóa)
    *   [GetProductDetails.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Ai/Tools/GetProductDetails.php) (Công cụ AI tự gọi lấy thông số giá bán, số lượng tồn kho)
    *   [ListCategories.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Ai/Tools/ListCategories.php) (Công cụ AI tự gọi lấy danh sách các nhóm danh mục của shop)
*   **Mô hình dữ liệu lưu trữ (Models - UUID primary keys):**
    *   `app/Models/AgentConversation.php` (Lưu hội thoại)
    *   `app/Models/AgentConversationMessage.php` (Lưu từng dòng hội thoại, kể cả log gọi tools của AI)
*   **Giao diện (View):**
    *   [chat.blade.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/resources/views/support/chat.blade.php) (Giao diện chat trực tiếp thời gian thực sử dụng Alpine.js)

---

### 📌 Chức năng 7: Gửi Mail thông báo khi cập nhật trạng thái đơn hàng
*   **Nơi phát sự kiện (Trigger):**
    *   [OrderService.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Services/OrderService.php#L37-L39) (Dòng phát sự kiện: `event(new OrderStatusUpdated(...))`)
*   **Lớp Sự kiện (Event):**
    *   [OrderStatusUpdated.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Events/OrderStatusUpdated.php)
*   **Bộ lắng nghe (Listener - Đẩy vào hàng đợi hàng chờ ShouldQueue):**
    *   [SendOrderStatusUpdatedMail.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Listeners/SendOrderStatusUpdatedMail.php)
*   **Mẫu Email (Mailable):**
    *   [OrderStatusUpdatedMail.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Mail/OrderStatusUpdatedMail.php)
*   **Thiết lập Email (.env):**
    *   Thiết lập `MAIL_MAILER=log` để xuất nội dung HTML email trực tiếp vào file nhật ký `storage/logs/laravel.log` phục vụ chấm thi nhanh và an toàn.

---

### 📌 Chức năng 8: Thanh toán trực tuyến (Cộng 1 điểm bonus)
*   **Tuyến đường (Routes):**
    *   `routes/web.php` (Tuyến đường checkout `shop.checkout.store`, đường dẫn trả kết quả `shop.checkout.return`, và IPN đối soát giao dịch `shop.checkout.ipn`)
*   **Bộ xử lý (Controller):**
    *   [ShopCheckoutController.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Http/Controllers/ShopCheckoutController.php) (Tích hợp tạo đơn hàng ở trạng thái tạm `pending`, chuyển hướng VNPay, xử lý return và IPN)
*   **Lớp Nghiệp vụ VNPay (Service):**
    *   [VnpayPaymentService.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Services/VnpayPaymentService.php) (Xây dựng đường dẫn URL thanh toán bảo mật bằng chữ ký SHA512, đối chiếu chữ ký phản hồi của VNPay để chống gian lận)
*   **Cấu hình VNPay (.env):**
    *   Chứa mã TMN, Hash Secret môi trường Sandbox và địa chỉ API kiểm thử VNPay.

---

## 2. Tiêu Chí Nghiệm Thu Khác
*   **Blade Component:** Sử dụng Alert component dùng chung qua việc đăng ký [Alert.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/View/Components/Alert.php) trong [AppServiceProvider.php](file:///d:/HSU/2533Semester%203(2025-2026)/Phát%20triển%20Web%20sd%20Framework/Project/app/Providers/AppServiceProvider.php#L32).
*   **Giao diện & Tiếng Việt:** Tất cả màn hình từ trang chủ Shop, Đăng nhập, Hồ sơ cá nhân đến Trang quản trị Đơn hàng đã được tối ưu hóa giao diện trực quan, đồng bộ màu sắc và Việt hóa chuẩn mực 100% có dấu.
*   **Độ ổn định:** Vượt qua **61/61** ca kiểm thử tự động của dự án.