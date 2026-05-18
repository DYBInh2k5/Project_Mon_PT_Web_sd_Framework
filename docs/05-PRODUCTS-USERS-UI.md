# 05. Products, Users, UI

## 1. User Management

Controller:

- [UserController.php](../app/Http/Controllers/UserController.php)

Chuc nang:

- xem danh sach user
- tao user
- xem chi tiet user
- sua user
- xoa user
- doi `is_active`
- loc theo role va status

View:

- [users/index.blade.php](../resources/views/users/index.blade.php)
- [users/create.blade.php](../resources/views/users/create.blade.php)
- [users/edit.blade.php](../resources/views/users/edit.blade.php)
- [users/show.blade.php](../resources/views/users/show.blade.php)

## 2. Product Category

Controller:

- [ProductCategoryController.php](../app/Http/Controllers/ProductCategoryController.php)

View:

- [product-categories/index.blade.php](../resources/views/product-categories/index.blade.php)
- [product-categories/create.blade.php](../resources/views/product-categories/create.blade.php)
- [product-categories/edit.blade.php](../resources/views/product-categories/edit.blade.php)
- [product-categories/show.blade.php](../resources/views/product-categories/show.blade.php)
- [product-categories/_form.blade.php](../resources/views/product-categories/_form.blade.php)

## 3. Product

Controller:

- [ProductController.php](../app/Http/Controllers/ProductController.php)

Chuc nang:

- CRUD san pham
- upload anh
- loc theo category
- loc theo status
- xem chi tiet

View:

- [products/index.blade.php](../resources/views/products/index.blade.php)
- [products/create.blade.php](../resources/views/products/create.blade.php)
- [products/edit.blade.php](../resources/views/products/edit.blade.php)
- [products/show.blade.php](../resources/views/products/show.blade.php)
- [products/_form.blade.php](../resources/views/products/_form.blade.php)

## 4. Orders, chatbot, payment

View:

- [orders/index.blade.php](../resources/views/orders/index.blade.php)
- [orders/show.blade.php](../resources/views/orders/show.blade.php)
- [orders/payment.blade.php](../resources/views/orders/payment.blade.php)
- [support/chat.blade.php](../resources/views/support/chat.blade.php)

Chuc nang:

- danh sach don hang
- loc theo ngay va trang thai
- xem chi tiet don hang va khach hang
- doi trang thai don hang
- gui mail khi doi trang thai
- chatbot goi y cau hoi va doc ma don that
- checkout demo cho online payment

## 5. Blade Component Alert

File class:

- [app/View/Components/Alert.php](../app/View/Components/Alert.php)

File view:

- [resources/views/components/alert.blade.php](../resources/views/components/alert.blade.php)

Alias:

- `x-package-alert`

Dung de hien:

- thong bao thanh cong
- thong bao loi
- danh sach nhieu loi

## 6. Tai sao co `novalidate`

Neu form co `required`, trinh duyet se chan submit truoc khi Laravel xu ly.

De hien `x-package-alert` cua project, cac form chinh duoc them:

```html
novalidate
```

De:

1. form submit len server
2. Laravel validate
3. tra loi ve view
4. alert tong hien ra

## 7. Cach test nhanh alert

Vi du:

1. vao `/users/create`
2. de trong form
3. bam `Create User`
4. alert do hien o dau form
