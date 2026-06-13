# 14. Tài liệu vấn đáp chi tiết

Tài liệu này dùng để ôn bài và trả lời vấn đáp. Mục tiêu là nhìn vào đây có thể nói lại được:

- project làm gì
- từng module chạy như thế nào
- vì sao dùng đúng tính năng Laravel đó
- file code nào chịu trách nhiệm cho phần nào

## 1. Mô hình tổng thể

Project là **hệ thống quản trị website bán hàng** bằng Laravel.

Luồng chung của project:

```text
Route -> Middleware -> Request Validation -> Controller -> Service -> Model -> Event/Listener -> View
```

Không phải module nào cũng có đủ tất cả các lớp trên.  
Module đơn hàng là module đầy đủ nhất vì có:

- service
- event
- listener
- mail
- lịch sử trạng thái

## 2. Các phần bắt buộc của đề

### 2.1. Quản lý danh mục sản phẩm

- Role: `editor`
- File chính:
  - `app/Http/Controllers/ProductCategoryController.php`
  - `app/Http/Requests/ProductCategoryRequest.php`
  - `resources/views/product-categories/*`

Chức năng:

- xem danh sách
- tạo mới
- sửa
- xoá
- tìm kiếm theo tên/slug
- lọc trạng thái

### 2.2. Quản lý người dùng

- Role: `admin`
- File chính:
  - `app/Http/Controllers/UserController.php`
  - `app/Http/Requests/StoreUserRequest.php`
  - `app/Http/Requests/UpdateUserRequest.php`
  - `resources/views/users/*`

Chức năng:

- xem danh sách user
- xem profile user
- cập nhật account và profile
- đổi trạng thái `is_active`
- xoá user

### 2.3. Quản lý sản phẩm

- Role: `editor`
- File chính:
  - `app/Http/Controllers/ProductController.php`
  - `app/Http/Requests/ProductRequest.php`
  - `resources/views/products/*`

Chức năng:

- tạo sản phẩm
- sửa sản phẩm
- xoá sản phẩm
- upload ảnh
- lọc theo category/status

### 2.4. Quản lý đơn hàng

- Role: `editor`
- File chính:
  - `app/Http/Controllers/OrderController.php`
  - `app/Services/OrderService.php`
  - `app/Events/OrderStatusUpdated.php`
  - `app/Listeners/SendOrderStatusUpdatedMail.php`
  - `app/Mail/OrderStatusUpdatedMail.php`

Chức năng:

- xem danh sách đơn hàng
- xem chi tiết đơn hàng
- xem thông tin khách hàng
- tìm theo ngày
- lọc theo trạng thái
- sắp xếp mới đến cũ
- cập nhật trạng thái đơn hàng
- lưu lịch sử trạng thái
- gửi mail cho khách khi đổi trạng thái

### 2.5. Chatbot hỗ trợ khách hàng

- File chính:
  - `app/Http/Controllers/SupportChatController.php`
  - `app/Http/Controllers/ChatController.php`
  - `app/Ai/Agents/SupportBot.php`
  - `app/Models/AgentConversation.php`
  - `app/Models/AgentConversationMessage.php`

Chức năng:

- hiển thị giao diện chat
- gửi message bằng `POST /chat/send`
- lưu hội thoại vào database
- gọi Gemini qua `laravel/ai`
- có nút chat nhỏ cố định góc phải dưới

### 2.6. Thanh toán online

- File chính:
  - `app/Http/Controllers/ShopCheckoutController.php`
  - `app/Services/VnpayPaymentService.php`
  - `resources/views/shop/checkout.blade.php`

Chức năng:

- checkout đơn hàng
- chuyển sang cổng VNPay
- nhận callback `returnUrl`
- nhận callback `ipnUrl`
- cập nhật `payment_status`, `payment_method`, `transaction_code`, `paid_at`

## 3. Điểm Laravel nên nhấn mạnh khi vấn đáp

### Eloquent relationship

Ví dụ:

- `User hasOne Profile`
- `ProductCategory hasMany Product`
- `Order hasMany OrderItem`
- `Article belongsToMany Tag`

Nên nói:

> Em dùng Eloquent relationship để lấy dữ liệu liên quan thay vì viết query thủ công ở nhiều nơi.

### Form Request Validation

Ví dụ:

- `StoreUserRequest`
- `UpdateUserRequest`
- `ProductRequest`
- `UpdateOrderStatusRequest`

Nên nói:

> Em tách validate ra Form Request để controller gọn hơn và dễ bảo trì hơn.

### Service

`OrderService` là ví dụ rõ nhất.

Nên nói:

> Em tách logic đổi trạng thái đơn hàng sang service để controller chỉ nhận request và gọi nghiệp vụ.

### Event / Listener

- `OrderStatusUpdated`
- `SendOrderStatusUpdatedMail`

Nên nói:

> Khi đơn hàng đổi trạng thái, service phát event. Listener nhận event và gửi mail. Cách này tách biệt trách nhiệm tốt hơn.

### Eager loading

Ví dụ:

```php
Article::with(['user', 'tags'])->get();
```

Nên nói:

> Em dùng eager loading để tránh N+1 query khi hiển thị danh sách article và tag.

## 4. Cách trình bày khi cô hỏi “project của em có gì”

Có thể trả lời theo thứ tự:

1. Project của em là hệ thống quản trị website bán hàng bằng Laravel.
2. Em có auth, middleware, role.
3. Em có quản lý user, profile, category, product, order.
4. Em có shop công khai, giỏ hàng, checkout, VNPay.
5. Em có chatbot hỗ trợ khách hàng bằng `laravel/ai`.
6. Em tối ưu module đơn hàng bằng service, event, listener và mail.
7. Em dùng Eloquent relationship, Form Request và Blade Component để code gọn và đúng kiến trúc Laravel.

## 5. File cần mở nhanh khi ôn bài

- `routes/web.php`
- `app/Http/Controllers/OrderController.php`
- `app/Services/OrderService.php`
- `app/Http/Controllers/ChatController.php`
- `app/Ai/Agents/SupportBot.php`
- `app/Http/Controllers/ShopCheckoutController.php`
- `resources/views/support/chat.blade.php`
- `resources/views/shop/checkout.blade.php`

## 6. Gợi ý câu trả lời ngắn

### Hỏi: Vì sao tách service?

> Vì nếu để controller vừa validate, vừa update database, vừa gửi mail thì controller sẽ dài và khó bảo trì. Em tách nghiệp vụ ra service để dễ mở rộng và đúng tinh thần Laravel hơn.

### Hỏi: Vì sao dùng event/listener?

> Vì em muốn tách việc gửi mail ra khỏi logic cập nhật trạng thái. Khi status đổi, service phát event và listener sẽ xử lý mail.

### Hỏi: Vì sao dùng eager loading?

> Vì em muốn tránh N+1 query và giảm số lượng truy vấn khi hiển thị dữ liệu có quan hệ.

### Hỏi: Chatbot của em hoạt động thế nào?

> Người dùng nhập câu hỏi, `ChatController` lưu message vào database, `SupportBot` gọi Gemini qua `laravel/ai`, sau đó lưu lại câu trả lời của assistant và trả JSON cho giao diện.

## 7. Lưu ý khi demo

- Demo role bằng tài khoản `admin@example.com` và `support@example.com`
- Demo order nên mở chi tiết đơn để xem status history
- Demo chatbot nên hỏi câu về order thật như `Kiem tra don ORD-00023`
- Demo article nên chỉ vào quan hệ `Article - User - Tag`
