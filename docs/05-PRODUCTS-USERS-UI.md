# 05. Products, Users, UI

## 1. User Management

Controller:

- [UserController.php](../app/Http/Controllers/UserController.php)

Chuc nang:

- xem danh sach user
- xem thong tin profile cua user
- tao user
- xem chi tiet user
- sua user va cap nhat profile cua user
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

## 4. Public shop

Layout / view:

- [layouts/shop.blade.php](../resources/views/layouts/shop.blade.php)
- [shop/index.blade.php](../resources/views/shop/index.blade.php)
- [shop/cart.blade.php](../resources/views/shop/cart.blade.php)
- [shop/checkout.blade.php](../resources/views/shop/checkout.blade.php)
- [shop/payment-result.blade.php](../resources/views/shop/payment-result.blade.php)

Controller:

- [ShopController.php](../app/Http/Controllers/ShopController.php)
- [ShopCartController.php](../app/Http/Controllers/ShopCartController.php)
- [ShopCheckoutController.php](../app/Http/Controllers/ShopCheckoutController.php)
- [shop/show.blade.php](../resources/views/shop/show.blade.php)

Chuc nang:

- mat tien shop cong khai cho khach xem san pham
- tim kiem san pham
- loc theo danh muc
- xem chi tiet san pham
- them san pham vao gio hang
- tang/giam/xoa san pham trong gio
- checkout sang MoMo sandbox
- hien ket qua thanh toan quay ve trang shop

## 5. Orders, chatbot, payment

View:

- [orders/index.blade.php](../resources/views/orders/index.blade.php)
- [orders/show.blade.php](../resources/views/orders/show.blade.php)
- [orders/payment.blade.php](../resources/views/orders/payment.blade.php)
- [support/chat.blade.php](../resources/views/support/chat.blade.php)

Chuc nang:

- danh sach don hang
- loc theo ngay va trang thai
- tim kiem theo ma don, ten khach, email, so dien thoai
- xem chi tiet don hang va khach hang
- doi trang thai don hang
- luu lich su doi trang thai
- gui mail khi doi trang thai bang Event/Listener
- chatbot goi y cau hoi va doc ma don that
- checkout demo cho online payment

## 6. Blade Component Alert

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

## 7. Tai sao co `novalidate`

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

## 8. Cach test nhanh alert

Vi du:

1. vao `/users/create`
2. de trong form
3. bam `Create User`
4. alert do hien o dau form

## 9. Ghi chu ve contrast UI

Project da duoc chinh lai contrast o cac component dung chung:

- body co mau chu mac dinh ro hon
- breadcrumb, settings nav, card description va user dropdown co do tuong phan cao hon
- muc dich la tranh chu bi chim tren nen sang hoac nen toi
