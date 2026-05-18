# 04. Auth, Role, Profile

## 1. Auth

Controller auth nam trong:

- `app/Http/Controllers/Auth`

Controller dang dung thuc te:

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

1. lay user hien tai
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

Controller nay dung `Query Builder` de lay va cap nhat `profiles`.

Vi du:

```php
DB::table('profiles')->where('user_id', $user->id)->first();
```

## 6. Quan he 1-1 User - Profile

Trong model:

- [User.php](../app/Models/User.php): `hasOne(Profile::class)`
- [Profile.php](../app/Models/Profile.php): `belongsTo(User::class)`

## 7. View auth/profile dang dung

- [pages/auth/signin.blade.php](../resources/views/pages/auth/signin.blade.php)
- [pages/auth/signup.blade.php](../resources/views/pages/auth/signup.blade.php)
- [pages/auth/forgot-password.blade.php](../resources/views/pages/auth/forgot-password.blade.php)
- [pages/auth/reset-password.blade.php](../resources/views/pages/auth/reset-password.blade.php)
- [pages/auth/confirm-password.blade.php](../resources/views/pages/auth/confirm-password.blade.php)
- [pages/auth/settings/profile.blade.php](../resources/views/pages/auth/settings/profile.blade.php)
- [pages/auth/settings/password.blade.php](../resources/views/pages/auth/settings/password.blade.php)
- [pages/profile.blade.php](../resources/views/pages/profile.blade.php)
