# 08. Controller By Controller

## 1. Muc dich file nay

File nay dung de map tung controller quan trong trong project:

- controller nam o dau
- controller xu ly chuc nang gi
- method nao dung de lam gi
- method do tra ve view nao hoac xu ly du lieu nao

Neu bi hoi “file nao xu ly chuc nang nay”, ban co the tra loi dua tren file nay.

## 2. Controller tong quan

Thu muc:

- [app/Http/Controllers](../app/Http/Controllers)
- [app/Http/Controllers/Auth](../app/Http/Controllers/Auth)
- [app/Http/Controllers/Settings](../app/Http/Controllers/Settings)

## 3. DashboardController

File:

- [DashboardController.php](../app/Http/Controllers/DashboardController.php)

Vai tro:

- xu ly route `/dashboard`
- tong hop du lieu de dua len dashboard

Thuong dung de:

- dem so luong
- hien thong ke nhanh
- hien danh sach gan day

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

- hien chi tiet 1 user
- tra ve view:
  - `users.show`

#### `edit(User $user)`

- mo form sua user
- tra ve view:
  - `users.edit`

#### `update(UpdateUserRequest $request, User $user)`

- cap nhat `name`, `email`, `role`, `is_active`
- redirect ve danh sach user

#### `toggleStatus(User $user, Request $request)`

- dao nguoc `is_active`
- co chan khong cho user tu tat chinh tai khoan dang dang nhap

#### `destroy(User $user, Request $request)`

- xoa user
- co chan khong cho user tu xoa chinh tai khoan dang dang nhap

## 5. ProductController

File:

- [ProductController.php](../app/Http/Controllers/ProductController.php)

Vai tro:

- CRUD san pham
- upload anh
- loc san pham
- xem chi tiet san pham

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

- mo form tao san pham
- lay danh sach category
- tra ve view:
  - `products.create`

#### `store(ProductRequest $request)`

- validate qua `ProductRequest`
- neu co anh thi upload vao `storage/public/products`
- tao product moi

#### `show(Product $product)`

- load `category`, `creator`
- hien chi tiet san pham
- tra ve view:
  - `products.show`

#### `edit(Product $product)`

- mo form sua san pham
- lay danh sach category
- tra ve view:
  - `products.edit`

#### `update(ProductRequest $request, Product $product)`

- cap nhat san pham
- neu upload anh moi thi xoa anh cu

#### `destroy(Product $product)`

- xoa san pham
- neu co anh thi xoa anh trong storage

## 6. ProductCategoryController

File:

- [ProductCategoryController.php](../app/Http/Controllers/ProductCategoryController.php)

Vai tro:

- CRUD danh muc san pham
- loc danh muc
- xem chi tiet danh muc

### Cac method chinh

#### `index(Request $request)`

- tim theo `name`, `slug`
- loc theo `status`
- dem so product lien quan bang `withCount('products')`
- tra ve view:
  - `product-categories.index`

#### `create()`

- mo form tao danh muc
- tra ve view:
  - `product-categories.create`

#### `store(ProductCategoryRequest $request)`

- tao danh muc moi
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

- cap nhat danh muc

#### `destroy(ProductCategory $productCategory)`

- xoa danh muc

## 7. Settings\\ProfileController

File:

- [Settings/ProfileController.php](../app/Http/Controllers/Settings/ProfileController.php)

Vai tro:

- xu ly profile cua user dang dang nhap
- phan nay dung `Query Builder`

### Cac method chinh

#### `show(Request $request)`

- lay user hien tai
- dam bao profile ton tai
- lay du lieu profile tu bang `profiles`
- tra ve view:
  - `pages.profile`

#### `edit(Request $request)`

- mo form sua profile
- tra ve view:
  - `pages.auth.settings.profile`

#### `update(Request $request)`

- validate du lieu
- upload avatar neu co
- cap nhat bang `profiles`
- neu doi `name` hoac `email` thi cap nhat them bang `users`

#### `destroy(Request $request)`

- yeu cau nhap `current_password`
- logout
- xoa tai khoan

#### `ensureProfileExists(...)`

- ham private
- neu user chua co profile thi tao profile mac dinh

## 8. Settings\\PasswordController

File:

- [Settings/PasswordController.php](../app/Http/Controllers/Settings/PasswordController.php)

Vai tro:

- xu ly doi mat khau trong khu `settings/password`

Thuong co:

- `edit()`
- `update()`

## 9. RoleDemoController

File:

- [RoleDemoController.php](../app/Http/Controllers/RoleDemoController.php)

Vai tro:

- demo middleware role
- de giao vien test nhanh quyen truy cap theo vai tro

### Cac method chinh

#### `index(Request $request)`

- hien trang tong hop cac route demo role

#### `admin(Request $request)`

- trang chi cho `admin`

#### `editor(Request $request)`

- trang cho `editor` va `admin`

#### `user(Request $request)`

- trang cho `user`, `editor`, `admin`

#### `renderAccessPage(...)`

- ham private dung chung
- gom logic tra ve view demo

## 10. Auth Controllers quan trong

### LoginController

File:

- [Auth/LoginController.php](../app/Http/Controllers/Auth/LoginController.php)

Chuc nang:

- `create()`: mo trang dang nhap `pages.auth.signin`
- `store()`: xu ly dang nhap
- `destroy()`: dang xuat

### RegistrationController

File:

- [Auth/RegistrationController.php](../app/Http/Controllers/Auth/RegistrationController.php)

Chuc nang:

- mo trang dang ky
- tao tai khoan moi

### PasswordResetLinkController

File:

- [Auth/PasswordResetLinkController.php](../app/Http/Controllers/Auth/PasswordResetLinkController.php)

Chuc nang:

- mo trang quen mat khau
- gui mail reset password

### NewPasswordController

File:

- [Auth/NewPasswordController.php](../app/Http/Controllers/Auth/NewPasswordController.php)

Chuc nang:

- mo form reset password
- cap nhat mat khau moi

### ConfirmationController

File:

- [Auth/ConfirmationController.php](../app/Http/Controllers/Auth/ConfirmationController.php)

Chuc nang:

- mo form confirm password
- xac nhan lai mat khau de vao khu vuc bao mat

### VerificationController

File:

- [Auth/VerificationController.php](../app/Http/Controllers/Auth/VerificationController.php)

Chuc nang:

- thong bao verify email
- gui lai mail verify
- xac nhan email

## 11. OrderController

File:

- [OrderController.php](../app/Http/Controllers/OrderController.php)

Vai tro:

- quan ly danh sach don hang
- loc theo ngay, trang thai, tu khoa
- xem chi tiet don hang
- doi trang thai don hang va gui mail

### Cac method chinh

#### `index(Request $request)`

- lay danh sach order
- tim theo ma don, ten khach, email
- loc theo `status`
- loc theo `date_from`, `date_to`
- sap xep moi den cu
- tra ve view:
  - `orders.index`

#### `show(Order $order)`

- load `items.product`
- hien chi tiet don hang
- tra ve view:
  - `orders.show`

#### `updateStatus(UpdateOrderStatusRequest $request, Order $order)`

- validate status
- cap nhat trang thai don
- gui mail qua `OrderStatusUpdatedMail`
- redirect lai trang chi tiet

## 12. OrderPaymentController

File:

- [OrderPaymentController.php](../app/Http/Controllers/OrderPaymentController.php)

Vai tro:

- mo man checkout demo
- xu ly online payment mo phong

### Cac method chinh

#### `create(Order $order)`

- mo trang payment cho mot don hang
- tra ve view:
  - `orders.payment`

#### `store(ProcessOrderPaymentRequest $request, Order $order)`

- validate thong tin thanh toan
- cap nhat `payment_status`, `payment_method`, `transaction_code`, `paid_at`
- neu don dang `pending` thi doi sang `processing`

## 13. SupportChatController

File:

- [SupportChatController.php](../app/Http/Controllers/SupportChatController.php)

Vai tro:

- hien man chatbot
- nhan cau hoi
- luu lich su chat trong session

### Cac method chinh

#### `index(Request $request)`

- lay lich su chat tu session
- tra ve view:
  - `support.chat`

#### `store(Request $request, CustomerSupportChatbot $chatbot)`

- validate cau hoi
- them tin nhan user vao session
- goi `CustomerSupportChatbot` de lay cau tra loi
- them tra loi cua bot vao session

#### `clear(Request $request)`

- xoa lich su hoi thoai trong session

## 14. Controller nao nen nho ky nhat

Neu van dap, nen nho ky:

- [UserController.php](../app/Http/Controllers/UserController.php)
- [ProductController.php](../app/Http/Controllers/ProductController.php)
- [ProductCategoryController.php](../app/Http/Controllers/ProductCategoryController.php)
- [OrderController.php](../app/Http/Controllers/OrderController.php)
- [OrderPaymentController.php](../app/Http/Controllers/OrderPaymentController.php)
- [SupportChatController.php](../app/Http/Controllers/SupportChatController.php)
- [Settings/ProfileController.php](../app/Http/Controllers/Settings/ProfileController.php)
- [RoleDemoController.php](../app/Http/Controllers/RoleDemoController.php)
- [Auth/LoginController.php](../app/Http/Controllers/Auth/LoginController.php)

## 15. Cach tra loi khi bi hoi “luong chay di dau”

Ban co the noi:

1. route duoc khai bao trong `routes/web.php` hoac `routes/auth.php`
2. request di qua middleware neu route co gan middleware
3. controller nhan request va xu ly logic
4. controller truy van model hoac Query Builder
5. controller tra ve view hoac redirect

Vi du:

- `/users/create` -> `UserController@create` -> `users.create`
- submit form `/users` -> `UserController@store` -> validate -> tao user -> redirect
