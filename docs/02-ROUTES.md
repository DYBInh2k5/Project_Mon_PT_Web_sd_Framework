# 02. Routes

## 1. Hai file route chinh

- [routes/web.php](../routes/web.php)
- [routes/auth.php](../routes/auth.php)

## 2. Route trong `web.php`

### Public

- `/`
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

### Orders

- `/orders`
- `/orders/{order}`
- `PATCH /orders/{order}/status`
- `/orders/{order}/payment`
- `POST /orders/{order}/payment`

### Customer support

- `/support-chat`
- `POST /support-chat`
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

## 5. Luong xu ly route

1. User truy cap URL
2. Laravel tim route phu hop
3. Middleware chay truoc
4. Neu hop le thi vao controller
5. Controller tra ve view hoac redirect
