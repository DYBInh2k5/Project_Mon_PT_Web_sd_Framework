# 01. Overview

## 1. Muc tieu project

Đây là project Laravel dung de hoc va demo cac noi dung:

- đăng nhập, đăng ký, quen mật khẩu
- quan ly user
- phan quyền theo role
- quan ly danh mục sản phẩm
- quan ly sản phẩm
- quan ly đơn hàng
- mặt tiền shop công khai cho khach xem sản phẩm
- giỏ hàng va checkout VNPay
- chatbot hỗ trợ khách hàng
- thanh toán online dang demo
- ho so người dùng (profile)
- Blade Component
- migration va model cho demo quan he nhieu-nhieu
- trang hiện thi danh sach articles va tags tuong ung bang Eloquent

## 2. Cong nghe đang dùng

- Laravel 12
- Blade
- Tailwind CSS
- Alpine.js
- SQLite

## 3. Thu muc quan trong

- `app/Http/Controllers`
  - xu ly request
- `app/Models`
  - model dữ liệu
- `app/Http/Middleware`
  - middleware nhu kiem tra role, check age
- `resources/views`
  - giao diện Blade
- `routes`
  - dinh nghia route
- `database/migrations`
  - tao bang
- `database/seeders`
  - dữ liệu mau
- `storage`
  - log, view cache, file tam

## 4. Model hiện co

- `User`
- `Profile`
- `ProductCategory`
- `Product`
- `Article`
- `Tag`
- `Order`
- `OrderItem`
- `OrderStatusHistory`
- `ShopController`, `ShopCartController`, `ShopCheckoutController`

## 5. Chuc nang chinh hiện co

- auth day du
- user management
- role demo
- product/category CRUD
- order management
- update order status bang `OrderService`
- lưu lịch sử đổi trạng thái đơn hàng
- gửi mail bang Event/Listener
- customer support chatbot
- payment demo cho tung order
- profile dung Eloquent qua quan he `User hasOne Profile`
- admin xem va cập nhật profile cua user trong phan user management
- trang chu `/` la mặt tiền shop công khai, co tim kiem va loc theo danh mục
- shop public co giỏ hàng, checkout va VNPay return page
- Alert component
- migration cho `articles`, `tags`, `article_tag`
- factory va seeding cho `Article`, `Tag`, `article_tag`
- trang `/articles` hiện thi danh sach article, user va tag bang Eloquent relationship

## 6. Cac file can nho

- [routes/web.php](../routes/web.php)
- [routes/auth.php](../routes/auth.php)
- [app/Providers/AppServiceProvider.php](../app/Providers/AppServiceProvider.php)
- [app/Http/Middleware/EnsureUserHasRole.php](../app/Http/Middleware/EnsureUserHasRole.php)
- [app/View/Components/Alert.php](../app/View/Components/Alert.php)
- [app/Http/Controllers/ArticleController.php](../app/Http/Controllers/ArticleController.php)
- [resources/views/article/list.blade.php](../resources/views/article/list.blade.php)
- [app/Services/OrderService.php](../app/Services/OrderService.php)
- [app/Events/OrderStatusUpdated.php](../app/Events/OrderStatusUpdated.php)
- [app/Listeners/SendOrderStatusUpdatedMail.php](../app/Listeners/SendOrderStatusUpdatedMail.php)
