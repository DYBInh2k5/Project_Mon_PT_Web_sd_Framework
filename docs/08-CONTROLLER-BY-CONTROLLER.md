# 08. Controller By Controller

## 1. Muc dich file nay

File nay dung de map tung controller quan trong trong project:

- controller nam o dau
- controller xu ly chuc nang gi
- method nao dung de lam gi
- method do tra ve view nao hoac xu ly dữ liệu nao

Neu bi hoi “file nao xu ly chuc nang nay”, ban co the tra loi dua trên file nay.

## 2. Controller tổng quan

Thu muc:

- [app/Http/Controllers](../app/Http/Controllers)
- [app/Http/Controllers/Auth](../app/Http/Controllers/Auth)
- [app/Http/Controllers/Settings](../app/Http/Controllers/Settings)

## 3. DashboardController

File:

- [DashboardController.php](../app/Http/Controllers/DashboardController.php)

Vai tro:

- xu ly route `/dashboard`
- tong hop dữ liệu de dua len dashboard

Thuong dung de:

- dem so luồng
- hiện thong ke nhanh
- hiện danh sach gan day

## 4. UserController

File:

- [UserController.php](../app/Http/Controllers/UserController.php)

Vai tro:

- xu ly toan bo CRUD user
- loc user
- doi status user

### Cac method chinh

#### `index(Request $request)`

- lay danh sach user
- tim kiem theo ten/email
- loc theo `role`
- loc theo `status`
- phan trang
- tra ve view:
  - `users.index`

#### `create()`

- mo form tao user
- tra ve view:
  - `users.create`

#### `store(StoreUserRequest $request)`

- validate qua `StoreUserRequest`
- tao user moi
- redirect ve danh sach user

#### `show(User $user)`

- hiện chi tiết 1 user
- load va hiện thông tin profile cua user
- tra ve view:
  - `users.show`

#### `edit(User $user)`

- mo form sua user
- tra ve view:
  - `users.edit`

#### `update(UpdateUserRequest $request, User $user)`

- cập nhật `name`, `email`, `role`, `is_active`
- cập nhật them `full_name`, `address`, `avatar`, `birthday`, `gender`, `phone` trong `profiles`
- redirect ve danh sach user

#### `toggleStatus(User $user, Request $request)`

- dao nguoc `is_active`
- co chan không cho user tu tat chinh tai khoan dang đăng nhập

#### `destroy(User $user, Request $request)`

- xoa user
- co chan không cho user tu xoa chinh tai khoan dang đăng nhập

## 5. ProductController

File:

- [ProductController.php](../app/Http/Controllers/ProductController.php)

Vai tro:

- CRUD sản phẩm
- upload anh
- loc sản phẩm
- xem chi tiết sản phẩm

### Cac method chinh

#### `index(Request $request)`

- tim theo `name`, `sku`, `slug`
- loc theo `category`
- loc theo `status`
- phan trang
- load quan he `category`
- tra ve view:
  - `products.index`

#### `create()`

- mo form tao sản phẩm
- lay danh sach category
- tra ve view:
  - `products.create`

#### `store(ProductRequest $request)`

- validate qua `ProductRequest`
- neu co anh thi upload vao `storage/public/products`
- tao product moi

#### `show(Product $product)`

- load `category`, `creator`
- hiện chi tiết sản phẩm
- tra ve view:
  - `products.show`

#### `edit(Product $product)`

- mo form sua sản phẩm
- lay danh sach category
- tra ve view:
  - `products.edit`

#### `update(ProductRequest $request, Product $product)`

- cập nhật sản phẩm
- neu upload anh moi thi xoa anh cu

#### `destroy(Product $product)`

- xoa sản phẩm
- neu co anh thi xoa anh trong storage

## 6. ProductCategoryController

File:

- [ProductCategoryController.php](../app/Http/Controllers/ProductCategoryController.php)

Vai tro:

- CRUD danh mục sản phẩm
- loc danh mục
- xem chi tiết danh mục

### Cac method chinh

#### `index(Request $request)`

- tim theo `name`, `slug`
- loc theo `status`
- dem so product lien quan bang `withCount('products')`
- tra ve view:
  - `product-categories.index`

#### `create()`

- mo form tao danh mục
- tra ve view:
  - `product-categories.create`

#### `store(ProductCategoryRequest $request)`

- tao danh mục moi
- gan `created_by`

#### `show(ProductCategory $productCategory)`

- load `creator` va `products.creator`
- tra ve view:
  - `product-categories.show`

#### `edit(ProductCategory $productCategory)`

- mo form sua
- tra ve view:
  - `product-categories.edit`

#### `update(ProductCategoryRequest $request, ProductCategory $productCategory)`

- cập nhật danh mục

#### `destroy(ProductCategory $productCategory)`

- xoa danh mục

## 7. Settings\\ProfileController

File:

- [Settings/ProfileController.php](../app/Http/Controllers/Settings/ProfileController.php)

Vai tro:

- xu ly profile cua user dang đăng nhập
- phan nay dung Eloquent va quan he `User hasOne Profile`

### Cac method chinh

#### `show(Request $request)`

- lay user hiện tai
- dam bao profile ton tai
- lay dữ liệu profile bang Eloquent tu model `Profile`
- tra ve view:
  - `pages.profile`

#### `edit(Request $request)`

- mo form sua profile
- tra ve view:
  - `pages.auth.settings.profile`

#### `update(Request $request)`

- validate dữ liệu
- upload avatar neu co
- cập nhật profile bang Eloquent
- neu doi `name` hoac `email` thi cập nhật them bang `users` bang Eloquent

#### `destroy(Request $request)`

- yeu cau nhap `current_password`
- logout
- xoa tai khoan

#### `ensureProfileExists(...)`

- ham private
- neu user chưa có profile thi tao profile mac dinh bang `firstOrCreate()` qua quan he `profile()`

## 8. ArticleController

File:

- [ArticleController.php](../app/Http/Controllers/ArticleController.php)

Vai tro:

- xu ly route resource `/articles`
- demo Eloquent relationship giua `Article`, `User`, `Tag`
- hiện danh sach articles va tags tuong ung

### Cac method chinh

#### `index()`

- lay danh sach article bang `Article::with(['user', 'tags'])->get()`
- view se goi quan he `$article->user` de lay user
- view se goi quan he `$article->tags` de lay danh sach tag
- tra ve view:
  - `article.list`

## 9. Settings\\PasswordController

File:

- [Settings/PasswordController.php](../app/Http/Controllers/Settings/PasswordController.php)

Vai tro:

- xu ly doi mật khẩu trong khu `settings/password`

Thuong co:

- `edit()`
- `update()`

## 10. RoleDemoController

File:

- [RoleDemoController.php](../app/Http/Controllers/RoleDemoController.php)

Vai tro:

- demo middleware role
- de giao vien test nhanh quyền truy cap theo vai tro

### Cac method chinh

#### `index(Request $request)`

- hiện trang tong hop cac route demo role

#### `admin(Request $request)`

- trang chi cho `admin`

#### `editor(Request $request)`

- trang cho `editor` va `admin`

#### `user(Request $request)`

- trang cho `user`, `editor`, `admin`

#### `renderAccessPage(...)`

- ham private dung chung
- gom logic tra ve view demo

## 11. Auth Controllers quan trong

### LoginController

File:

- [Auth/LoginController.php](../app/Http/Controllers/Auth/LoginController.php)

Chuc nang:

- `create()`: mo trang đăng nhập `pages.auth.signin`
- `store()`: xu ly đăng nhập
- `destroy()`: đăng xuất

### RegistrationController

File:

- [Auth/RegistrationController.php](../app/Http/Controllers/Auth/RegistrationController.php)

Chuc nang:

- mo trang đăng ký
- tao tai khoan moi

### PasswordResetLinkController

File:

- [Auth/PasswordResetLinkController.php](../app/Http/Controllers/Auth/PasswordResetLinkController.php)

Chuc nang:

- mo trang quen mật khẩu
- gửi mail reset password

### NewPasswordController

File:

- [Auth/NewPasswordController.php](../app/Http/Controllers/Auth/NewPasswordController.php)

Chuc nang:

- mo form reset password
- cập nhật mật khẩu moi

### ConfirmationController

File:

- [Auth/ConfirmationController.php](../app/Http/Controllers/Auth/ConfirmationController.php)

Chuc nang:

- mo form confirm password
- xac nhan lai mật khẩu de vao khu vuc bao mat

### VerificationController

File:

- [Auth/VerificationController.php](../app/Http/Controllers/Auth/VerificationController.php)

Chuc nang:

- thong bao verify email
- gui lai mail verify
- xac nhan email

## 12. OrderController

File:

- [OrderController.php](../app/Http/Controllers/OrderController.php)

Vai tro:

- quan ly danh sach đơn hàng
- loc theo ngay, trạng thái, tu khoa
- xem chi tiết đơn hàng
- đổi trạng thái đơn hàng thong qua `OrderService`

### Cac method chinh

#### `index(Request $request)`

- lay danh sach order
- tim theo ma don, ten khach, email, so dien thoai bang `scopeSearch`
- loc theo `status` bang `scopeStatus`
- loc theo `date_from`, `date_to` bang `scopePlacedFrom`, `scopePlacedUntil`
- sap xep moi den cu
- tra ve view:
  - `orders.index`

#### `show(Order $order)`

- load `items.product`
- load `statusHistories.changer`
- hiện chi tiết đơn hàng
- hiện lich su đổi trạng thái đơn hàng
- tra ve view:
  - `orders.show`

#### `updateStatus(UpdateOrderStatusRequest $request, Order $order)`

- validate status
- goi `OrderService@updateStatus`
- service cập nhật trạng thái, ghi lich su, phat event
- listener gửi mail qua `OrderStatusUpdatedMail`
- redirect lai trang chi tiết

## 13. OrderService, Event, Listener

File:

- [OrderService.php](../app/Services/OrderService.php)
- [OrderStatusUpdated.php](../app/Events/OrderStatusUpdated.php)
- [SendOrderStatusUpdatedMail.php](../app/Listeners/SendOrderStatusUpdatedMail.php)

Vai tro:

- tach logic nghiệp vụ khoi controller
- ghi lich su status vao `order_status_histories`
- gửi mail bang Event/Listener cua Laravel

Luong:

`OrderController -> OrderService -> OrderStatusUpdated -> SendOrderStatusUpdatedMail`

## 14. OrderPaymentController

File:

- [OrderPaymentController.php](../app/Http/Controllers/OrderPaymentController.php)

Vai tro:

- mo man checkout demo
- xu ly online payment mo phong

### Cac method chinh

#### `create(Order $order)`

- mo trang payment cho mot đơn hàng
- tra ve view:
  - `orders.payment`

#### `store(ProcessOrderPaymentRequest $request, Order $order)`

- validate thông tin thanh toán
- cập nhật `payment_status`, `payment_method`, `transaction_code`, `paid_at`
- neu don dang `pending` thi goi `OrderService` doi sang `processing`

## 15. SupportChatController

File:

- [SupportChatController.php](../app/Http/Controllers/SupportChatController.php)

Vai tro:

- hiện man chatbot
- nhan cau hoi
- lưu lịch sử chat trong session

### Cac method chinh

#### `index(Request $request)`

- lay lich su chat tu session
- tra ve view:
  - `support.chat`

#### `store(Request $request, CustomerSupportChatbot $chatbot)`

- validate cau hoi
- them tin nhan user vao session
- goi `CustomerSupportChatbot` de lay cau tra loi
- neu không co rule phù hợp, `CustomerSupportChatbot` se goi `GeminiChatService`
- them tra loi cua bot vao session

#### `clear(Request $request)`

- xoa lich su hoi thoai trong session

## 16. Controller nao nen nho ky nhat

Neu vấn đáp, nen nho ky:

- [UserController.php](../app/Http/Controllers/UserController.php)
- [ProductController.php](../app/Http/Controllers/ProductController.php)
- [ProductCategoryController.php](../app/Http/Controllers/ProductCategoryController.php)
- [OrderController.php](../app/Http/Controllers/OrderController.php)
- [OrderPaymentController.php](../app/Http/Controllers/OrderPaymentController.php)
- [OrderService.php](../app/Services/OrderService.php)
- [SupportChatController.php](../app/Http/Controllers/SupportChatController.php)
- [Settings/ProfileController.php](../app/Http/Controllers/Settings/ProfileController.php)
- [ArticleController.php](../app/Http/Controllers/ArticleController.php)
- [RoleDemoController.php](../app/Http/Controllers/RoleDemoController.php)
- [Auth/LoginController.php](../app/Http/Controllers/Auth/LoginController.php)

## 17. Cach tra loi khi bi hoi “luồng chay di dau”

Ban co the noi:

1. route duoc khai bao trong `routes/web.php` hoac `routes/auth.php`
2. request di qua middleware neu route co gan middleware
3. controller nhan request va xu ly logic
4. controller truy van model/Eloquent
5. controller tra ve view hoac redirect

Vi du:

- `/users/create` -> `UserController@create` -> `users.create`
- submit form `/users` -> `UserController@store` -> validate -> tao user -> redirect
- `/articles` -> `ArticleController@index` -> `Article::with(['user', 'tags'])->get()` -> `article.list`
