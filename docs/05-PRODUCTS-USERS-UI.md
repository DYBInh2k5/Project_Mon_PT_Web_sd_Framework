# 05. Products, Users, UI

## 1. User Management

Controller:

- [UserController.php](../app/Http/Controllers/UserController.php)

Chuc nang:

- xem danh sach user
- xem thông tin profile cua user
- tao user
- xem chi tiết user
- sua user va cập nhật profile cua user
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

- CRUD sản phẩm
- upload anh
- loc theo category
- loc theo status
- xem chi tiết

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

- mặt tiền shop công khai cho khach xem sản phẩm
- tim kiem sản phẩm
- loc theo danh mục
- xem chi tiết sản phẩm
- them sản phẩm vao giỏ hàng
- tang/giam/xoa sản phẩm trong gio
- checkout sang cong VNPay
- hiện kết quả thanh toán quay ve trang shop

## 5. Orders, chatbot, payment

View:

- [orders/index.blade.php](../resources/views/orders/index.blade.php)
- [orders/show.blade.php](../resources/views/orders/show.blade.php)
- [orders/payment.blade.php](../resources/views/orders/payment.blade.php)
- [support/chat.blade.php](../resources/views/support/chat.blade.php)

Chuc nang:

- danh sach đơn hàng
- loc theo ngay va trạng thái
- tim kiem theo ma don, ten khach, email, so dien thoai
- xem chi tiết đơn hàng va khách hàng
- đổi trạng thái đơn hàng
- lưu lịch sử đổi trạng thái
- gửi mail khi đổi trạng thái bang Event/Listener
- chatbot gợi ý cau hoi va doc ma don that
- checkout demo cho online payment

## 6. Blade Component Alert

File class:

- [app/View/Components/Alert.php](../app/View/Components/Alert.php)

File view:

- [resources/views/components/alert.blade.php](../resources/views/components/alert.blade.php)

Alias:

- `x-package-alert`

Dung de hiện:

- thong bao thành công
- thong bao loi
- danh sach nhieu loi

## 7. Tai sao co `novalidate`

Neu form co `required`, trinh duyet se chan submit truoc khi Laravel xu ly.

De hiện `x-package-alert` cua project, cac form chinh duoc them:

```html
novalidate
```

De:

1. form submit len server
2. Laravel validate
3. tra loi ve view
4. alert tong hiện ra

## 8. Cach test nhanh alert

Vi du:

1. vao `/users/create`
2. de trong form
3. bam `Create User`
4. alert do hiện o dau form

## 9. Ghi chu ve contrast UI

Project da duoc chinh lai contrast o cac component dung chung:

- body co mau chu mac dinh ro hon
- breadcrumb, settings nav, card description va user dropdown co do tuong phan cao hon
- muc dich la tranh chu bi chim trên nen sang hoac nen toi
