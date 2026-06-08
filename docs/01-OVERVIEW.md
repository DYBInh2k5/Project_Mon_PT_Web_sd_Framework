# 01. Overview

## 1. Muc tieu project

Day la project Laravel dung de hoc va demo cac noi dung:

- dang nhap, dang ky, quen mat khau
- quan ly user
- phan quyen theo role
- quan ly danh muc san pham
- quan ly san pham
- quan ly don hang
- mat tien shop cong khai cho khach xem san pham
- gio hang va checkout MoMo sandbox
- chatbot ho tro khach hang
- thanh toan online dang demo
- ho so nguoi dung (profile)
- Blade Component
- migration va model cho demo quan he nhieu-nhieu
- trang hien thi danh sach articles va tags tuong ung bang Eloquent

## 2. Cong nghe dang dung

- Laravel 12
- Blade
- Tailwind CSS
- Alpine.js
- SQLite

## 3. Thu muc quan trong

- `app/Http/Controllers`
  - xu ly request
- `app/Models`
  - model du lieu
- `app/Http/Middleware`
  - middleware nhu kiem tra role, check age
- `resources/views`
  - giao dien Blade
- `routes`
  - dinh nghia route
- `database/migrations`
  - tao bang
- `database/seeders`
  - du lieu mau
- `storage`
  - log, view cache, file tam

## 4. Model hien co

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

## 5. Chuc nang chinh hien co

- auth day du
- user management
- role demo
- product/category CRUD
- order management
- update order status bang `OrderService`
- luu lich su doi trang thai don hang
- gui mail bang Event/Listener
- customer support chatbot
- payment demo cho tung order
- profile dung Eloquent qua quan he `User hasOne Profile`
- admin xem va cap nhat profile cua user trong phan user management
- trang chu `/` la mat tien shop cong khai, co tim kiem va loc theo danh muc
- shop public co gio hang, checkout va callback MoMo
- Alert component
- migration cho `articles`, `tags`, `article_tag`
- factory va seeding cho `Article`, `Tag`, `article_tag`
- trang `/articles` hien thi danh sach article, user va tag bang Eloquent relationship

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
