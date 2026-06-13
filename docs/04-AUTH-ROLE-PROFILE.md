# 04. Auth, Role, Profile

## 1. Auth

Controller auth nam trong:

- `app/Http/Controllers/Auth`

Controller đang dùng thuc te:

- [LoginController.php](../app/Http/Controllers/Auth/LoginController.php)
- [RegistrationController.php](../app/Http/Controllers/Auth/RegistrationController.php)
- [PasswordResetLinkController.php](../app/Http/Controllers/Auth/PasswordResetLinkController.php)
- [NewPasswordController.php](../app/Http/Controllers/Auth/NewPasswordController.php)
- [ConfirmationController.php](../app/Http/Controllers/Auth/ConfirmationController.php)

## 2. Role

Role duoc luu trong:

- [User.php](../app/Models/User.php)
- cot `role` trong bang `users`

Gia tri:

- `admin`
- `editor`
- `user`

## 3. Middleware role

Middleware:

- [EnsureUserHasRole.php](../app/Http/Middleware/EnsureUserHasRole.php)

Nhiem vu:

1. lay user hiện tai
2. doc role can co tu route
3. neu dung role thi cho qua
4. neu sai role thi `403`

Vi du:

```php
->middleware('role:admin')
```

## 4. CheckAge middleware

Middleware:

- [CheckAge.php](../app/Http/Middleware/CheckAge.php)

Demo route:

- `/check_age/{age?}`
- `/check_fail`

## 5. Profile

Controller chinh:

- [Settings/ProfileController.php](../app/Http/Controllers/Settings/ProfileController.php)
- [UserController.php](../app/Http/Controllers/UserController.php)

Controller nay dung Eloquent va quan he `User hasOne Profile` de lay va cập nhật `profiles`.
Khi user thay doi email, `email_verified_at` se duoc reset ve `null` de Laravel yeu cau xac minh lai.

Vi du:

```php
$profile = $user->profile()->firstOrCreate([]);
```

## 6. Quan he 1-1 User - Profile

Trong model:

- [User.php](../app/Models/User.php): `hasOne(Profile::class)`
- [Profile.php](../app/Models/Profile.php): `belongsTo(User::class)`

Trong user management:

- admin vao `/users/{user}` de xem thông tin account va profile cua user
- admin vao `/users/{user}/edit` de cập nhật `name`, `email`, `role`, `is_active`
- cùng form edit nay cập nhật them `full_name`, `address`, `avatar`, `birthday`, `gender`, `phone`

Avatar sau khi upload se hiện o:

- man `Profile Settings`
- man `User Profile`
- dropdown user o goc trên ben phai cua header

## 7. View auth/profile đang dùng

- [pages/auth/signin.blade.php](../resources/views/pages/auth/signin.blade.php)
- [pages/auth/signup.blade.php](../resources/views/pages/auth/signup.blade.php)
- [pages/auth/forgot-password.blade.php](../resources/views/pages/auth/forgot-password.blade.php)
- [pages/auth/reset-password.blade.php](../resources/views/pages/auth/reset-password.blade.php)
- [pages/auth/confirm-password.blade.php](../resources/views/pages/auth/confirm-password.blade.php)
- [pages/auth/settings/profile.blade.php](../resources/views/pages/auth/settings/profile.blade.php)
- [pages/auth/settings/password.blade.php](../resources/views/pages/auth/settings/password.blade.php)
- [pages/profile.blade.php](../resources/views/pages/profile.blade.php)
