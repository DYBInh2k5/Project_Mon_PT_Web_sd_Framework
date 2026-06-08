# 02. Routes

## 1. Hai file route chinh

- [routes/web.php](../routes/web.php)
- [routes/auth.php](../routes/auth.php)

## 2. Route trong `web.php`

### Public

- `/`
- `/shop`
- `/cart`
- `/checkout`
- `/checkout/momo/return`
- `/checkout/momo/ipn`
- `/check_fail`
- `/check_age/{age?}`
- `/articles`
- `/articles/{article}`

### Can dang nhap

- `/dashboard`
- `/profile`
- `/settings/profile`
- `/settings/password`

### User management

- `/users`
- `/users/create`
- `/users/{user}`
- `/users/{user}/edit`
- `PATCH /users/{user}/status`

### Product category

- `/product-categories`
- `/product-categories/create`
- `/product-categories/{id}`
- `/product-categories/{id}/edit`

### Product

- `/products`
- `/products/create`
- `/products/{id}`
- `/products/{id}/edit`

### Articles demo Eloquent

- `/articles`
- `/articles/create`
- `/articles/{article}`
- `/articles/{article}/edit`

### Public shop + cart + checkout

- `/`
- `/shop`
- `/shop/products/{product}`
- `/cart`
- `/checkout`
- `/checkout/momo/return`
- `/checkout/momo/ipn`

### Orders

- `/orders`
- `/orders/{order}`
- `PATCH /orders/{order}/status`
- `/orders/{order}/payment`
- `POST /orders/{order}/payment`

### Customer support

- `/support-chat`
- `POST /support-chat`
- `POST /chat/send`
- `POST /support-chat/clear`

### Role demo

- `/role-demo`
- `/role-demo/admin`
- `/role-demo/editor`
- `/role-demo/user`

## 3. Route trong `auth.php`

- `login`
- `register`
- `forgot-password`
- `reset-password`
- `verify-email`
- `confirm-password`
- `logout`

## 4. Cach doc route

Vi du:

```php
Route::resource('users', UserController::class)->middleware('role:admin');
```

Nghia la:

- route `users` dung `UserController`
- chi `admin` moi duoc vao
- request se di qua middleware `role`

Vi du bai articles:

```php
Route::resource('articles', ArticleController::class);
```

Nghia la:

- route `articles` dung `ArticleController`
- `/articles` se chay `ArticleController@index`
- controller lay article bang Eloquent va tra ve view `article.list`

Vi du mat tien shop:

```php
Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
```

Nghia la:

- route `/` va `/shop` cung mo trang shop cong khai
- nguoi dung co the xem danh muc, tim san pham va loc san pham ma khong can dang nhap

## 5. Luong xu ly route

1. User truy cap URL
2. Laravel tim route phu hop
3. Middleware chay truoc
4. Neu hop le thi vao controller
5. Controller tra ve view hoac redirect
