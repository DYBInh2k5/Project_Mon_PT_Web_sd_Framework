# 01. Overview

## 1. Muc tieu project

Day la project Laravel dung de hoc va demo cac noi dung:

- dang nhap, dang ky, quen mat khau
- quan ly user
- phan quyen theo role
- quan ly danh muc san pham
- quan ly san pham
- ho so nguoi dung (profile)
- Blade Component
- migration va model cho demo quan he nhieu-nhieu

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

## 5. Chuc nang chinh hien co

- auth day du
- user management
- role demo
- product/category CRUD
- profile dung Query Builder
- Alert component
- migration cho `articles`, `tags`, `article_tag`
- factory va seeding cho `Article`, `Tag`, `article_tag`

## 6. Cac file can nho

- [routes/web.php](../routes/web.php)
- [routes/auth.php](../routes/auth.php)
- [app/Providers/AppServiceProvider.php](../app/Providers/AppServiceProvider.php)
- [app/Http/Middleware/EnsureUserHasRole.php](../app/Http/Middleware/EnsureUserHasRole.php)
- [app/View/Components/Alert.php](../app/View/Components/Alert.php)
