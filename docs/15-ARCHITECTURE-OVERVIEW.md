# 15. Tổng quan kiến trúc dự án

Tài liệu này là bản đồ tổng thể của project.  
Nếu chỉ được giữ lại một file để hiểu nhanh hệ thống, thì file này là file nên đọc đầu tiên sau `docs/00-README.md`.

## 1. Mục tiêu của hệ thống

Project là **hệ thống quản trị website bán hàng** bằng Laravel.

Hệ thống có 2 mặt chính:

1. **Mặt tiền shop công khai**
   - khách xem sản phẩm
   - xem chi tiết sản phẩm
   - thêm vào giỏ hàng
   - checkout
   - thanh toán MoMo sandbox

2. **Khu quản trị**
   - admin quản lý user, profile
   - editor quản lý category, product, order
   - chatbot hỗ trợ khách hàng
   - demo article/tag để minh hoạ Eloquent relationship

## 2. Kiến trúc tầng

### 2.1. Tầng route

File:

- `routes/web.php`
- `routes/auth.php`

Nhiệm vụ:

- khai báo đường dẫn
- gắn middleware
- nối route với controller

### 2.2. Tầng middleware

File:

- `app/Http/Middleware/EnsureUserHasRole.php`

Nhiệm vụ:

- chặn user không đúng role
- bảo vệ route quản trị

### 2.3. Tầng controller

Controller nhận request và điều phối logic:

- `UserController`
- `ProductCategoryController`
- `ProductController`
- `OrderController`
- `OrderPaymentController`
- `ShopController`
- `ShopCartController`
- `ShopCheckoutController`
- `SupportChatController`
- `ChatController`
- `ArticleController`

### 2.4. Tầng validation

Form Request:

- `StoreUserRequest`
- `UpdateUserRequest`
- `ProductRequest`
- `ProductCategoryRequest`
- `UpdateOrderStatusRequest`
- `ProcessOrderPaymentRequest`

Nhiệm vụ:

- validate dữ liệu đầu vào
- giữ controller gọn

### 2.5. Tầng service

Service quan trọng:

- `OrderService`
- `ShoppingCartService`
- `MomoPaymentService`

Nhiệm vụ:

- chứa nghiệp vụ riêng
- giảm độ phức tạp của controller

### 2.6. Tầng model

Các model chính:

- `User`
- `Profile`
- `ProductCategory`
- `Product`
- `Order`
- `OrderItem`
- `OrderStatusHistory`
- `Article`
- `Tag`
- `AgentConversation`
- `AgentConversationMessage`

Nhiệm vụ:

- đại diện cho bảng dữ liệu
- khai báo relationship
- khai báo scope/cast/fillable

### 2.7. Tầng event/listener/mail

- `OrderStatusUpdated`
- `SendOrderStatusUpdatedMail`
- `OrderStatusUpdatedMail`

Nhiệm vụ:

- tách việc gửi mail ra khỏi logic cập nhật đơn
- dễ mở rộng thêm notification hoặc logging sau này

### 2.8. Tầng view

Blade views:

- `resources/views/users/*`
- `resources/views/products/*`
- `resources/views/product-categories/*`
- `resources/views/orders/*`
- `resources/views/shop/*`
- `resources/views/support/chat.blade.php`
- `resources/views/article/list.blade.php`

Nhiệm vụ:

- hiển thị dữ liệu
- nhận input từ người dùng
- gọi AJAX/Fetch cho chatbot

## 3. Luồng chính của từng module

### 3.1. User management

```text
Route -> Middleware -> UserController -> User model/Profile model -> View
```

Tính năng:

- danh sách user
- xem profile
- cập nhật profile
- đổi role/status

### 3.2. Product category

```text
Route -> Middleware -> ProductCategoryController -> ProductCategory model -> View
```

Tính năng:

- CRUD danh mục
- lọc status
- đếm sản phẩm trong danh mục

### 3.3. Product

```text
Route -> Middleware -> ProductController -> Product model -> Storage/View
```

Tính năng:

- CRUD sản phẩm
- upload ảnh
- lọc theo category/status

### 3.4. Order

```text
Route -> Middleware -> OrderController -> OrderService -> Order model -> Event -> Listener -> Mail -> View
```

Tính năng:

- xem danh sách
- xem chi tiết
- cập nhật trạng thái
- lưu lịch sử
- gửi mail

### 3.5. Shop public

```text
Route -> ShopController -> Product/ProductCategory models -> View
```

Tính năng:

- homepage shop
- trang chi tiết sản phẩm
- sản phẩm liên quan

### 3.6. Cart và checkout

```text
Route -> ShopCartController -> ShoppingCartService -> Session
Route -> ShopCheckoutController -> Order/OrderItem -> MomoPaymentService -> MoMo sandbox
```

Tính năng:

- thêm vào giỏ
- cập nhật số lượng
- xoá item
- checkout
- redirect sang MoMo
- callback thanh toán

### 3.7. Chatbot

```text
Route -> SupportChatController -> ChatController -> SupportBot -> laravel/ai -> Gemini -> DB
```

Tính năng:

- mở giao diện chat
- gửi message qua `POST /chat/send`
- lưu message vào database
- gọi Gemini qua agent
- hiển thị lịch sử hội thoại

### 3.8. Articles/Tags demo

```text
Route -> ArticleController -> Article model -> user/tags relationship -> View
```

Tính năng:

- demo `belongsTo`
- demo `belongsToMany`
- demo eager loading

## 4. Các file cần nhớ nhất khi vấn đáp

### Route

- `routes/web.php`

### Auth và role

- `app/Http/Middleware/EnsureUserHasRole.php`
- `app/Http/Controllers/Auth/*`

### User

- `app/Http/Controllers/UserController.php`
- `app/Http/Requests/StoreUserRequest.php`
- `app/Http/Requests/UpdateUserRequest.php`

### Product/category

- `app/Http/Controllers/ProductController.php`
- `app/Http/Controllers/ProductCategoryController.php`

### Order

- `app/Http/Controllers/OrderController.php`
- `app/Services/OrderService.php`
- `app/Events/OrderStatusUpdated.php`
- `app/Listeners/SendOrderStatusUpdatedMail.php`

### Shop

- `app/Http/Controllers/ShopController.php`
- `app/Http/Controllers/ShopCartController.php`
- `app/Http/Controllers/ShopCheckoutController.php`
- `app/Services/ShoppingCartService.php`
- `app/Services/MomoPaymentService.php`

### Chatbot

- `app/Http/Controllers/SupportChatController.php`
- `app/Http/Controllers/ChatController.php`
- `app/Ai/Agents/SupportBot.php`

## 5. Cách giải thích project trong 30 giây

> Project của em là hệ thống quản trị website bán hàng bằng Laravel. Em chia thành phần quản trị và phần shop công khai. Khu quản trị có user, profile, category, product, order và chatbot. Phần shop có danh sách sản phẩm, chi tiết sản phẩm, giỏ hàng và checkout MoMo sandbox. Em dùng middleware để phân quyền, Form Request để validate, Eloquent relationship để lấy dữ liệu liên quan, và service/event/listener để tách nghiệp vụ đơn hàng cho dễ bảo trì.

## 6. Cách hiểu nhanh code theo kiến trúc

Nếu mở một chức năng bất kỳ, hãy đọc theo thứ tự:

1. Route
2. Middleware
3. Controller
4. Request validation
5. Service
6. Model
7. View

Ví dụ với đơn hàng:

```text
routes/web.php
-> OrderController
-> UpdateOrderStatusRequest
-> OrderService
-> Order model
-> OrderStatusHistory model
-> OrderStatusUpdated event
-> SendOrderStatusUpdatedMail listener
-> orders/show.blade.php
```

## 7. Điều cần nhớ khi demo

- Demo role bằng `admin@example.com` và `support@example.com`
- Demo order bằng chi tiết đơn và status history
- Demo chatbot bằng câu hỏi có mã đơn
- Demo shop bằng thêm vào giỏ và checkout
- Demo article bằng quan hệ `Article - User - Tag`

