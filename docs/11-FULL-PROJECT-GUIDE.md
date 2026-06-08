# 11. Full Project Guide

## 1. Ten de tai

Xay dung ung dung phan quan tri cua website ban hang bang Laravel.

Project nay tap trung vao cac chuc nang:

- dang nhap va dang ky
- middleware auth
- phan quyen theo role
- quan ly user
- xem va cap nhat profile user
- quan ly danh muc san pham
- quan ly san pham co hinh anh
- quan ly don hang
- mat tien shop cong khai de khach xem san pham
- gio hang va checkout MoMo sandbox
- cap nhat trang thai don hang
- gui mail khi doi trang thai don hang
- chatbot ho tro khach hang
- thanh toan online dang demo
- demo Article - Tag bang Eloquent many-to-many
- toi uu Laravel bang Service, Event, Listener, Queue, Local Scope
- cai dat `laravel/boost` de bo tro workflow AI trong project

## 2. Cong nghe dung trong project

- Laravel 12
- PHP 8.2+
- Blade
- Tailwind CSS
- Alpine.js
- SQLite
- Laravel Mail
- Laravel Queue voi `QUEUE_CONNECTION=sync`
- Eloquent ORM
- Query Builder
- Form Request Validation
- Blade Component

## 3. Cach chay project

### Buoc 1: Mo terminal tai thu muc project

Thu muc hien tai:

```powershell
D:\HSU\2533Semester 3(2025-2026)\Phat trien Web sd Framework\Project
```

Luu y:

- `DB_DATABASE` dang tro sang ban SQLite trong thu muc temp cua Windows
- day la cach de tranh loi `disk I/O error` khi update profile/avatar tren may nay

### Buoc 2: Cai dependency PHP neu may chua co vendor

```powershell
composer install
```

### Buoc 3: Cai dependency frontend neu chua co node_modules

```powershell
npm install
```

### Buoc 4: Tao file `.env` neu chua co

```powershell
copy .env.example .env
```

### Buoc 5: Tao app key

```powershell
php artisan key:generate
```

### Buoc 6: Chay migrate

```powershell
php artisan migrate
```

### Buoc 7: Do du lieu mau

```powershell
php artisan db:seed
```

### Buoc 8: Chay project

```powershell
php artisan serve
```

Mo trinh duyet:

```text
http://127.0.0.1:8000
```

Neu can build frontend:

```powershell
npm run dev
```

Hoac chay full stack theo script:

```powershell
composer run dev
```

## 4. Tai khoan demo

Seeder tao san:

```text
admin@example.com
password
```

```text
support@example.com
password
```

Y nghia:

- `admin@example.com`: role `admin`, dung de quan ly user
- `support@example.com`: role `editor`, dung de quan ly category, product, order

## 5. Role trong project

Project co 3 role:

- `admin`
- `editor`
- `user`

Trong `users` table co cot:

- `role`
- `is_active`

Phan quyen dung middleware:

```php
->middleware('role:admin')
->middleware('role:editor,admin')
->middleware('role:user,editor,admin')
```

File middleware:

- [EnsureUserHasRole.php](../app/Http/Middleware/EnsureUserHasRole.php)

Luong middleware:

1. Lay user dang dang nhap
2. Lay role route yeu cau
3. Neu user co role phu hop thi cho vao controller
4. Neu sai role thi `abort(403)`

## 6. Route quan trong

File route:

- [routes/web.php](../routes/web.php)
- [routes/auth.php](../routes/auth.php)

### Auth

- `/login`
- `/register`
- `/forgot-password`
- `/reset-password`
- `/confirm-password`
- `/logout`

### Dashboard

- `/dashboard`

### Public shop

- `/`
- `/shop`
- `/shop/products/{product}`
- `/cart`
- `/checkout`
- `/checkout/momo/return`
- `/checkout/momo/ipn`

Day la mat tien cong khai cua website:

- khach co the xem danh muc
- tim san pham
- loc san pham theo danh muc
- xem chi tiet san pham
- them san pham vao gio
- cap nhat so luong / xoa san pham trong gio
- tao don hang va chuyen sang MoMo sandbox
- xem gia va trang thai ton kho

### User management

- `/users`
- `/users/create`
- `/users/{user}`
- `/users/{user}/edit`
- `PATCH /users/{user}/status`

Role:

- `admin`

### Product category

- `/product-categories`
- `/product-categories/create`
- `/product-categories/{productCategory}`
- `/product-categories/{productCategory}/edit`

Role:

- `editor`, `admin`

### Product

- `/products`
- `/products/create`
- `/products/{product}`
- `/products/{product}/edit`

Role:

- `editor`, `admin`

### Orders

- `/orders`
- `/orders/{order}`
- `PATCH /orders/{order}/status`
- `/orders/{order}/payment`
- `POST /orders/{order}/payment`

Role:

- `editor`, `admin`

### Chatbot

- `/support-chat`
- `POST /support-chat`
- `POST /chat/send`
- `POST /support-chat/clear`
- bot dung Gemini de tra loi broad question ve project, khong chi keyword
- neu co ma don, bot se tra cuu order that truoc khi tong hop tra loi
- prompt co them context tu Boost guideline va guide cua project
- neu Gemini bi 429/quota, bot co local fallback de van tra loi duoc cac cau hoi co ban va theo module

Role:

- `user`, `editor`, `admin`

### Articles demo Eloquent

- `/articles`

Dung de demo:

- Article thuoc ve User
- Article co nhieu Tag
- Tag co nhieu Article

## 7. Cau truc thu muc nen nho

```text
app/
  Http/
    Controllers/
    Middleware/
    Requests/
  Models/
  Services/
  Events/
  Listeners/
  Mail/
  Support/
database/
  migrations/
  seeders/
  factories/
resources/
  views/
routes/
  web.php
  auth.php
docs/
```

## 8. Database chinh

Database dang dung:

```text
database/database.sqlite
```

Bang quan trong:

- `users`
- `profiles`
- `product_categories`
- `products`
- `orders`
- `order_items`
- `order_status_histories`
- `articles`
- `tags`
- `article_tag`

## 9. Quan he Eloquent

### User - Profile

```text
User hasOne Profile
Profile belongsTo User
```

Dung cho:

- profile cua user dang dang nhap
- admin xem/cap nhat profile user trong user management

### ProductCategory - Product

```text
ProductCategory hasMany Product
Product belongsTo ProductCategory
```

Dung cho:

- loc product theo category
- xem chi tiet category kem danh sach product

### Product - OrderItem

```text
Product hasMany OrderItem
OrderItem belongsTo Product
```

Dung cho:

- xem san pham trong chi tiet don hang

### Order - OrderItem

```text
Order hasMany OrderItem
OrderItem belongsTo Order
```

Dung cho:

- chi tiet don hang
- tinh so luong item trong don

### Order - OrderStatusHistory

```text
Order hasMany OrderStatusHistory
OrderStatusHistory belongsTo Order
```

Dung cho:

- xem lich su doi trang thai don hang

### Article - Tag

```text
Article belongsTo User
User hasMany Article
Article belongsToMany Tag
Tag belongsToMany Article
```

Bang trung gian:

```text
article_tag
```

## 10. User management

Controller:

- [UserController.php](../app/Http/Controllers/UserController.php)

Request validation:

- [StoreUserRequest.php](../app/Http/Requests/StoreUserRequest.php)
- [UpdateUserRequest.php](../app/Http/Requests/UpdateUserRequest.php)

View:

- [users/index.blade.php](../resources/views/users/index.blade.php)
- [users/create.blade.php](../resources/views/users/create.blade.php)
- [users/show.blade.php](../resources/views/users/show.blade.php)
- [users/edit.blade.php](../resources/views/users/edit.blade.php)

Chuc nang:

- danh sach user
- tim theo ten/email
- loc theo role
- loc theo status
- tao user
- xem chi tiet user
- xem thong tin profile
- cap nhat account va profile
- neu email thay doi thi `email_verified_at` se reset ve `null`
- doi `is_active`
- xoa user

Luong update user:

```text
Route /users/{user}
-> UserController@update
-> UpdateUserRequest validate
-> update users table
-> update profiles table
-> redirect users.index
```

## 11. Product category

Controller:

- [ProductCategoryController.php](../app/Http/Controllers/ProductCategoryController.php)

Request:

- [ProductCategoryRequest.php](../app/Http/Requests/ProductCategoryRequest.php)

Chuc nang:

- danh sach category
- tim theo name/slug
- loc status
- tao category
- sua category
- xoa category
- xem chi tiet category
- dem so product trong category bang `withCount`

## 12. Product

Controller:

- [ProductController.php](../app/Http/Controllers/ProductController.php)

Request:

- [ProductRequest.php](../app/Http/Requests/ProductRequest.php)

Chuc nang:

- danh sach product
- tim theo name/sku/slug
- loc theo category
- loc theo status
- tao product
- upload image
- sua product
- xoa product
- xoa image cu khi upload image moi

Upload file:

```php
$request->file('image')->store('products', 'public');
```

## 13. Orders

Controller:

- [OrderController.php](../app/Http/Controllers/OrderController.php)

Service:

- [OrderService.php](../app/Services/OrderService.php)

Models:

- [Order.php](../app/Models/Order.php)
- [OrderItem.php](../app/Models/OrderItem.php)
- [OrderStatusHistory.php](../app/Models/OrderStatusHistory.php)

Views:

- [orders/index.blade.php](../resources/views/orders/index.blade.php)
- [orders/show.blade.php](../resources/views/orders/show.blade.php)

Chuc nang:

- xem danh sach don hang
- tim theo order number, customer name, email, phone
- loc theo status
- loc theo date from/date to
- sap xep moi den cu
- xem chi tiet don hang
- xem thong tin khach hang
- xem san pham trong don
- cap nhat status
- luu history doi status
- gui mail khi doi status

## 14. Toi uu Order bang Laravel

Luc dau co the viet tat ca trong `OrderController`.

Sau toi uu:

```text
OrderController
-> OrderService
-> OrderStatusUpdated event
-> SendOrderStatusUpdatedMail listener
-> OrderStatusUpdatedMail
```

Y nghia:

- controller gon hon
- service chua logic nghiep vu
- event/listener tach gui mail khoi controller
- history giup xem lai qua trinh doi status
- local scope giup query gon va tai su dung duoc

## 15. Luong cap nhat status don hang

1. Editor/Admin vao `/orders/{order}`
2. Chon status moi
3. Submit form `PATCH /orders/{order}/status`
4. `UpdateOrderStatusRequest` validate status
5. `OrderController@updateStatus` goi `OrderService@updateStatus`
6. `OrderService` update `orders.status`
7. `OrderService` tao dong moi trong `order_status_histories`
8. `OrderService` phat event `OrderStatusUpdated`
9. Listener `SendOrderStatusUpdatedMail` gui mail
10. Redirect ve trang chi tiet don hang

## 16. Payment demo

Controller:

- [OrderPaymentController.php](../app/Http/Controllers/OrderPaymentController.php)

Request:

- [ProcessOrderPaymentRequest.php](../app/Http/Requests/ProcessOrderPaymentRequest.php)

View:

- [orders/payment.blade.php](../resources/views/orders/payment.blade.php)

Luong:

1. Mo chi tiet don hang
2. Bam `Open checkout`
3. Nhap thong tin payment demo
4. Submit form
5. Cap nhat:
   - `payment_status = paid`
   - `payment_method`
   - `transaction_code`
   - `paid_at`
6. Neu order dang `pending`, goi `OrderService` doi status sang `processing`

## 17. Mail

Mail class:

- [OrderStatusUpdatedMail.php](../app/Mail/OrderStatusUpdatedMail.php)

View:

- [status-updated.blade.php](../resources/views/emails/orders/status-updated.blade.php)

Config hien tai:

```env
MAIL_MAILER=log
QUEUE_CONNECTION=sync
```

Y nghia:

- mail khong gui ra Gmail that
- mail ghi vao log de demo
- listener queue chay ngay vi queue la `sync`

## 18. Chatbot

Controller:

- [SupportChatController.php](../app/Http/Controllers/SupportChatController.php)

Logic:

- [CustomerSupportChatbot.php](../app/Support/CustomerSupportChatbot.php)

View:

- [support/chat.blade.php](../resources/views/support/chat.blade.php)

Chuc nang:

- tra loi theo keyword
- neu khong co rule phu hop thi goi Gemini API
- tra ve JSON de UI render on dinh
- luu lich su chat trong session
- doc ma don that nhu `ORD-00023`
- neu gap ma don, bot query bang `orders`

## 19. Articles va Tags

Controller:

- [ArticleController.php](../app/Http/Controllers/ArticleController.php)

Models:

- [Article.php](../app/Models/Article.php)
- [Tag.php](../app/Models/Tag.php)
- [User.php](../app/Models/User.php)

View:

- [article/list.blade.php](../resources/views/article/list.blade.php)

Route:

```php
Route::resource('articles', ArticleController::class);
```

Code chinh:

```php
$articles = Article::with(['user', 'tags'])->get();
```

Y nghia:

- lay danh sach article
- eager load user va tags
- tranh N+1 query
- view hien `$article->user->name`
- view lap `$article->tags`

## 20. Form Request Validation

Project dung Form Request de controller gon hon.

Vi du:

- `StoreUserRequest`
- `UpdateUserRequest`
- `ProductRequest`
- `ProductCategoryRequest`
- `UpdateOrderStatusRequest`
- `ProcessOrderPaymentRequest`

Luong:

```text
Form submit
-> FormRequest validate
-> neu sai quay ve view kem errors
-> neu dung vao controller
```

## 21. Blade Component Alert

Class:

- [Alert.php](../app/View/Components/Alert.php)

View:

- [alert.blade.php](../resources/views/components/alert.blade.php)

Alias:

```php
Blade::component('package-alert', Alert::class);
```

Dung:

```blade
<x-package-alert type="danger" message="..." :messages="$errors->all()" />
```

## 22. Cac lenh hay dung

### Route

```powershell
php artisan route:list
php artisan route:list --path=orders
php artisan route:list --path=articles
```

### Database

```powershell
php artisan migrate
php artisan migrate:status
php artisan db:seed
```

### Cache

```powershell
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

Tren may nay, Laravel con dung them thu muc `cache/` o root de luu compiled view va package cache tam thoi.
Thu muc nay la runtime-only va da duoc them vao `.gitignore`.

### Test

```powershell
php artisan test
```

### Serve

```powershell
php artisan serve
```

## 23. Thu tu doc code de hieu nhanh

Nen doc theo thu tu:

1. `routes/web.php`
2. middleware neu route co middleware
3. controller tuong ung
4. request validation
5. service neu co
6. model va relationship
7. migration de biet bang/cot
8. view blade

Vi du voi order:

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

## 24. Demo nhanh tren lop

### Demo role

1. Dang nhap admin
2. Vao `/users`
3. Tao/sua user
4. Doi status user
5. Dang nhap editor
6. Thu vao `/users` de thay bi chan

### Demo product/category

1. Dang nhap editor
2. Vao `/product-categories`
3. Tao category
4. Vao `/products`
5. Tao product co upload image
6. Loc product theo category/status

### Demo order

1. Vao `/orders`
2. Tim theo phone/email/order number
3. Loc theo status
4. Loc theo date from/date to
5. Mo chi tiet order
6. Doi status
7. Xem status history
8. Kiem tra mail log

### Demo payment

1. Mo order detail
2. Bam `Open checkout`
3. Nhap payment demo
4. Submit
5. Xem payment status thanh `paid`
6. Neu order pending thi status thanh `processing`

### Demo chatbot

1. Vao `/support-chat`
2. Nhap `Kiem tra don ORD-00023`
3. Bot doc du lieu that tu SQLite
4. Nhap cau hoi khac nhu `Laravel middleware role nay dung nhu the nao?`
5. Bot se tra loi bang Gemini dua tren context cua project va Boost

### Demo articles

1. Vao `/articles`
2. Xem title, user, body, created_at, tags
3. Giai thich Eloquent relationship

## 25. Cau tra loi van dap ngan

“Project cua em la phan quan tri website ban hang bang Laravel. Em co auth, middleware role, user management, profile, product/category CRUD, order management, chatbot, mail va payment demo. Em dung Eloquent relationship cho cac model, Form Request de validate, Blade Component de hien alert. Rieng module don hang em toi uu bang `OrderService`, event `OrderStatusUpdated`, listener gui mail va bang `order_status_histories` de luu lich su doi trang thai.”

## 26. Cau tra loi khi bi hoi vi sao tach Service

“Neu de controller vua validate, vua update database, vua gui mail thi controller se bi dai va kho bao tri. Em tach nghiep vu doi trang thai don hang sang `OrderService`, controller chi nhan request va goi service. Cach nay dung voi Laravel hon va de mo rong sau nay.”

## 27. Cau tra loi khi bi hoi Event/Listener de lam gi

“Event/Listener giup tach viec gui mail khoi logic cap nhat don hang. Khi status thay doi, service phat event `OrderStatusUpdated`. Listener `SendOrderStatusUpdatedMail` nhan event va gui mail. Sau nay neu muon them notification hay log khac, em chi can them listener moi.”

## 28. Cau tra loi khi bi hoi local scope la gi

“Local scope la ham trong model de tai su dung query. Trong `Order`, em tao `scopeSearch`, `scopeStatus`, `scopePlacedFrom`, `scopePlacedUntil`. Nhờ vậy controller khong can viet nhieu `when`, query gon hon va co the dung lai o noi khac.”

## 29. Cau tra loi khi bi hoi eager loading la gi

“Eager loading la lay san du lieu quan he de tranh N+1 query. Vi du trang articles can user va tags, em dung `Article::with(['user', 'tags'])->get()`. Trang order detail can san pham va lich su status, em dung `$order->load(['items.product', 'statusHistories.changer'])`.”

## 30. Loi thuong gap

### Loi mail khong vao Gmail

Do `.env` dang:

```env
MAIL_MAILER=log
```

Mail se ghi vao:

```text
storage/logs/laravel.log
```

### Loi SQLite journal / disk I/O

Neu migration bi ngat giua chung, co the con file:

```text
database/database.sqlite-journal
```

Cach xu ly:

1. Tat server Laravel dang chay
2. Chay lai:

```powershell
php artisan migrate
```

Neu van loi, backup database truoc khi xoa file journal.

### Loi route khong thay

Chay:

```powershell
php artisan route:list
```

### Loi view cu

Chay:

```powershell
php artisan view:clear
```

## 31. Ghi nho cuoi

Project nen duoc giai thich theo luong:

```text
Route -> Middleware -> Request Validation -> Controller -> Service -> Model -> Event/Listener -> View
```

Khong phai module nao cung co service/event. Module don hang co service/event vi no la module quan trong nhat va co nhieu nghiep vu nhat.
