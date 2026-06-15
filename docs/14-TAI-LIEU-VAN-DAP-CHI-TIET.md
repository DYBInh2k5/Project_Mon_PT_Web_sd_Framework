# 14. Tài Liệu Vấn Đáp Chi Tiết (Oral Exam Detailed Guide)

Tài liệu này được viết chi tiết nhằm hỗ trợ bạn ôn tập và trả lời vấn đáp một cách xuất sắc nhất. Mục tiêu là giúp bạn tự tin giải thích được:
- Đề tài dự án làm gì?
- Kiến trúc và luồng chạy của từng chức năng trong hệ thống.
- Tại sao sử dụng giải pháp/công nghệ đó của Laravel?
- Tệp tin mã nguồn nào đảm nhận nhiệm vụ gì trong dự án?

---

## 1. Bản đồ Kiến trúc Tổng thể của Dự án

Dự án là **Hệ thống quản lý bán hàng** viết bằng Laravel 12.

**Luồng dữ liệu tổng quát của hệ thống:**
```text
Tuyến đường (Route) -> Middleware -> Validate dữ liệu (Form Request) -> Controller -> Service -> Model (Eloquent) -> Event & Listener -> View (Blade)
```

> [!NOTE]
> Không phải chức năng nào cũng đi qua toàn bộ các lớp trên.
> **Module Đơn hàng** là module đầy đủ và chuẩn mực nhất của dự án, áp dụng đầy đủ mô hình Service, Event, Listener, Mail và lịch sử thay đổi trạng thái đơn hàng.

---

## 2. Các chức năng chính bắt buộc và tệp tin mã nguồn tương ứng

### 2.1. Quản lý danh mục sản phẩm (CRUD)
- **Quyền hạn truy cập (Role):** `editor` hoặc `admin`
- **Các tệp tin mã nguồn cốt lõi:**
  - Tuyến đường định nghĩa: `routes/web.php`
  - Lớp Controller xử lý: [ProductCategoryController.php](../app/Http/Controllers/ProductCategoryController.php)
  - Biểu mẫu Validate: `ProductCategoryRequest.php`
  - Các View giao diện: `resources/views/product-categories/`
- **Chức năng chính:** Xem danh sách, tạo mới, chỉnh sửa thông tin, xóa danh mục sản phẩm, hỗ trợ tìm kiếm theo tên/slug, lọc trạng thái hoạt động và đếm số lượng sản phẩm liên kết bằng `withCount('products')`.

### 2.2. Quản lý người dùng và hồ sơ tài khoản
- **Quyền hạn truy cập (Role):** `admin`
- **Các tệp tin mã nguồn cốt lõi:**
  - Lớp Controller xử lý: [UserController.php](../app/Http/Controllers/UserController.php)
  - Biểu mẫu Validate: `StoreUserRequest.php` và `UpdateUserRequest.php`
  - Các View giao diện: `resources/views/users/`
- **Chức năng chính:** Hiển thị danh sách tài khoản, tìm kiếm, lọc theo vai trò/trạng thái. Admin có quyền xem chi tiết hồ sơ, cập nhật tài khoản và thông tin cá nhân (địa chỉ, số điện thoại, ngày sinh, giới tính, tải lên ảnh đại diện) và thực hiện khóa/mở khóa nhanh trạng thái tài khoản.

### 2.3. Quản lý sản phẩm (CRUD)
- **Quyền hạn truy cập (Role):** `editor` hoặc `admin`
- **Các tệp tin mã nguồn cốt lõi:**
  - Lớp Controller xử lý: [ProductController.php](../app/Http/Controllers/ProductController.php)
  - Biểu mẫu Validate: `ProductRequest.php`
  - Các View giao diện: `resources/views/products/`
- **Chức năng chính:** Tạo sản phẩm, sửa sản phẩm, xóa sản phẩm. Hỗ trợ tải lên hình ảnh sản phẩm và tự động xóa bỏ file ảnh cũ trong storage khi thay đổi ảnh hoặc xóa sản phẩm để tránh lãng phí dung lượng máy chủ.

### 2.4. Quản lý đơn đặt hàng
- **Quyền hạn truy cập (Role):** `editor` hoặc `admin`
- **Các tệp tin mã nguồn cốt lõi:**
  - Lớp Controller xử lý: [OrderController.php](../app/Http/Controllers/OrderController.php)
  - Lớp nghiệp vụ: [OrderService.php](../app/Services/OrderService.php)
  - Sự kiện & Lắng nghe: [OrderStatusUpdated.php](../app/Events/OrderStatusUpdated.php) và [SendOrderStatusUpdatedMail.php](../app/Listeners/SendOrderStatusUpdatedMail.php)
  - Lớp Email: [OrderStatusUpdatedMail.php](../app/Mail/OrderStatusUpdatedMail.php)
- **Chức năng chính:** Quản trị viên xem danh sách, lọc theo trạng thái/thời gian, tìm kiếm thông tin giao nhận. Xem chi tiết đơn hàng, xem thông tin khách hàng, thực hiện đổi trạng thái đơn (hệ thống tự động ghi nhật ký lịch sử đổi và phát sự kiện gửi mail thông báo cho khách hàng).

### 2.5. Trợ lý AI Chatbot hỗ trợ khách hàng
- **Các tệp tin mã nguồn cốt lõi:**
  - Lớp Controller: [SupportChatController.php](../app/Http/Controllers/SupportChatController.php) và [ChatController.php](../app/Http/Controllers/ChatController.php)
  - Agent AI & Các Tool: [SupportBot.php](../app/Ai/Agents/SupportBot.php), `SearchProducts.php`, `GetProductDetails.php`, `ListCategories.php`
  - Các Model lưu trữ: `AgentConversation.php` và `AgentConversationMessage.php`
- **Chức năng chính:** Hiển thị giao diện chat, lưu trữ bền vững lịch sử trò chuyện trong database (không dùng session tạm). Khi tin nhắn được gửi lên, Chatbot Agent AI (sử dụng Gemini API thông qua package `laravel/ai`) sẽ phân tích và tự động kích hoạt các công cụ (Tools) để truy vấn thông tin sản phẩm hoặc danh mục thực tế của cửa hàng và phản hồi cho khách hàng.

### 2.6. Thanh toán trực tuyến VNPay
- **Các tệp tin mã nguồn cốt lõi:**
  - Lớp Controller: [ShopCheckoutController.php](../app/Http/Controllers/ShopCheckoutController.php)
  - Lớp Service: [VnpayPaymentService.php](../app/Services/VnpayPaymentService.php)
  - Giao diện thanh toán: `resources/views/shop/checkout.blade.php`
- **Chức năng chính:** Khách đặt hàng xong sẽ được tạo đơn hàng tạm ở trạng thái `pending`, hệ thống sinh mã liên kết thanh toán an toàn (HMAC SHA512) và chuyển hướng khách sang VNPay. Khi thanh toán hoàn tất, VNPay chuyển hướng khách về lại shop (`vnpayReturn`) và gửi request IPN (`ipn`) bất đồng bộ ngầm để đối soát số tiền, cập nhật trạng thái đơn hàng thành `processing` và trạng thái thanh toán thành đã trả tiền (`paid`).

---

## 3. Các từ khóa kỹ thuật cần nhấn mạnh khi vấn đáp

### 3.1. Eloquent Relationship (Mối quan hệ cơ sở dữ liệu)
- **Ví dụ trong dự án:**
  - `User hasOne Profile` (Quan hệ 1-1).
  - `ProductCategory hasMany Product` (Quan hệ 1-N).
  - `Order hasMany OrderItem` (Quan hệ 1-N).
  - `Article belongsToMany Tag` (Quan hệ Nhiều-Nhiều).
- **Ý nghĩa trả lời:** *"Em sử dụng Eloquent Relationship để khai báo các mối quan hệ thực thể trong Model, giúp việc lấy dữ liệu liên quan vô cùng đơn giản và tránh viết các câu truy vấn SQL thô phức tạp ở nhiều nơi."*

### 3.2. Form Request Validation (Xác thực dữ liệu tách biệt)
- **Ví dụ trong dự án:** `StoreUserRequest`, `UpdateUserRequest`, `ProductRequest`, `ProductCategoryRequest`.
- **Ý nghĩa trả lời:** *"Em tách logic kiểm tra tính hợp lệ của dữ liệu biểu mẫu (Validation) ra các file Form Request riêng biệt. Điều này giúp code trong Controller luôn sạch sẽ, dễ đọc và dễ tái sử dụng ở các API khác."*

### 3.3. Service Pattern (Tách biệt logic nghiệp vụ)
- **Ví dụ trong dự án:** `OrderService`, `VnpayPaymentService`, `GeminiChatService`.
- **Ý nghĩa trả lời:** *"Em sử dụng Service Pattern để đóng gói các logic xử lý nghiệp vụ phức tạp ra khỏi Controller. Controller chỉ làm nhiệm vụ nhận request và điều phối kết quả trả về, còn toàn bộ nghiệp vụ thực tế (như thanh toán, gửi mail, lưu lịch sử) được xử lý bên trong Service."*

### 3.4. Event / Listener (Tách rời các tác vụ phụ)
- **Ví dụ trong dự án:** Sự kiện `OrderStatusUpdated` và bộ lắng nghe `SendOrderStatusUpdatedMail`.
- **Ý nghĩa trả lời:** *"Khi trạng thái đơn hàng thay đổi, Service phát đi sự kiện. Bộ lắng nghe sẽ bắt lấy sự kiện này để thực hiện việc gửi email thông báo cho khách hàng. Việc tách rời này giúp mã nguồn lỏng (loose coupling), dễ bảo trì và không làm chậm tốc độ tải trang của người dùng."*

### 3.5. Eager Loading (Tránh lỗi N+1 Query)
- **Ví dụ trong dự án:** `Article::with(['user', 'tags'])->get()`.
- **Ý nghĩa trả lời:** *"Mặc định Laravel sử dụng cơ chế Lazy Loading, tức là khi nào cần mới truy vấn DB, dẫn đến lỗi N+1 query (gửi quá nhiều câu lệnh SELECT lặp lại khi hiển thị danh sách). Em sử dụng Eager Loading với phương thức `with()` để nạp trước toàn bộ dữ liệu quan hệ vào bộ nhớ chỉ trong 1-2 câu truy vấn chính."*

---

## 4. Gợi ý mẫu câu hỏi & câu trả lời xuất sắc phục vụ vấn đáp

### Hỏi: Tại sao em lại dùng database để lưu lịch sử Chatbot thay vì dùng Session như bình thường?
- **Trả lời:** *"Dạ thưa cô, việc lưu lịch sử trò chuyện trong Session có hạn chế là dữ liệu sẽ bị mất đi hoàn toàn khi người dùng đăng xuất, tắt trình duyệt hoặc Session hết hạn. Để mang lại trải nghiệm hỗ trợ khách hàng tốt nhất và theo dõi được lịch sử tư vấn, em đã thiết kế các bảng `agent_conversations` và `agent_conversations_messages` trong cơ sở dữ liệu để lưu trữ lịch sử bền vững cho mỗi tài khoản người dùng."*

### Hỏi: Giải thích quy trình xử lý chữ ký bảo mật khi kết nối VNPay?
- **Trả lời:** *"Dạ thưa cô, để đảm bảo thông tin số tiền và mã đơn hàng không bị thay đổi bất hợp pháp trong quá trình chuyển tiếp giữa shop và VNPay, hệ thống sử dụng thuật toán mã hóa chữ ký HMAC SHA512. Khi chuyển sang VNPay, em sắp xếp các tham số theo thứ tự alphabet và băm chuỗi kèm theo mã bảo mật (Hash Secret Key) được cấp. Khi nhận kết quả trả về (qua returnUrl hoặc IPN), em thực hiện băm lại dữ liệu nhận được để đối chiếu chữ ký an toàn, đảm bảo giao dịch thực sự thành công và số tiền khớp 100%."*

### Hỏi: Em đã cấu hình email thông báo đơn hàng như thế nào khi chạy demo?
- **Trả lời:** *"Dạ thưa cô, trong môi trường demo cục bộ, em đã cấu hình biến môi trường `MAIL_MAILER=log` trong file `.env`. Với thiết lập này, các email thông báo đổi trạng thái đơn hàng sẽ không được gửi tới hộp thư thật (tránh tốn thời gian và chi phí dịch vụ gửi mail), mà sẽ được ghi nhận chi tiết dưới dạng mã HTML trực tiếp vào file log của hệ thống tại `storage/logs/laravel.log` để phục vụ việc kiểm tra."*
