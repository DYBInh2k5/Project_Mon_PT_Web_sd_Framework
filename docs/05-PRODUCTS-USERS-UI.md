# 05. Quản Lý Sản Phẩm, Người Dùng Và Giao Diện (Products, Users, UI)

## 1. Quản lý Người dùng (User Management)

### Vai trò chịu trách nhiệm: `admin`
Admin hệ thống thực hiện các thao tác quản trị người dùng, phân quyền và kiểm soát hoạt động thông qua:
- **Controller chính:** [UserController.php](../app/Http/Controllers/UserController.php)
- **Form Requests validate:** `StoreUserRequest.php` và `UpdateUserRequest.php`

**Các chức năng cốt lõi:**
- **Danh sách người dùng:** Hiển thị danh sách, phân trang, tích hợp tìm kiếm theo tên/email, lọc theo vai trò (`role`) và trạng thái (`status`).
- **Thống kê nhanh:** Số liệu tổng hợp số lượng admin, editor, user thông thường, tài khoản đang hoạt động và tài khoản đã xác minh email.
- **Tạo người dùng mới:** Điền thông tin tài khoản, tự động khởi tạo bản ghi hồ sơ trống liên kết (quan hệ 1-1).
- **Xem thông tin chi tiết:** Xem tài khoản kết hợp hồ sơ cá nhân (`profile`).
- **Chỉnh sửa nâng cao:** Admin được quyền thay đổi vai trò (role), bật/tắt trạng thái hoạt động tài khoản và cập nhật trực tiếp hồ sơ cá nhân của người dùng.
- **Xóa tài khoản:** Xóa tài khoản khỏi database (ngăn chặn tự xóa tài khoản của chính mình).
- **Chuyển nhanh trạng thái (`is_active`):** Cho phép click nhanh để kích hoạt/khóa tài khoản trực tiếp từ danh sách.

**Các View Blade liên quan:**
- [users/index.blade.php](../resources/views/users/index.blade.php) - Bảng danh sách người dùng kèm bộ lọc.
- [users/create.blade.php](../resources/views/users/create.blade.php) - Form tạo người dùng mới.
- [users/edit.blade.php](../resources/views/users/edit.blade.php) - Form cập nhật tài khoản và hồ sơ.
- [users/show.blade.php](../resources/views/users/show.blade.php) - Trang chi tiết thông tin hồ sơ của người dùng.

---

## 2. Quản lý Danh mục Sản phẩm (Product Category)

### Vai trò chịu trách nhiệm: `editor` hoặc `admin`
Quản lý các nhóm danh mục để phân loại sản phẩm:
- **Controller chính:** [ProductCategoryController.php](../app/Http/Controllers/ProductCategoryController.php)
- **Form Request validate:** `ProductCategoryRequest.php`

**Các View Blade liên quan:**
- [product-categories/index.blade.php](../resources/views/product-categories/index.blade.php) - Danh sách danh mục sản phẩm có đếm số lượng sản phẩm liên kết (`withCount('products')`).
- [product-categories/create.blade.php](../resources/views/product-categories/create.blade.php) - Form tạo danh mục mới.
- [product-categories/edit.blade.php](../resources/views/product-categories/edit.blade.php) - Form chỉnh sửa danh mục.
- [product-categories/show.blade.php](../resources/views/product-categories/show.blade.php) - Chi tiết danh mục và danh sách sản phẩm thuộc về danh mục đó.
- [product-categories/_form.blade.php](../resources/views/product-categories/_form.blade.php) - Biểu mẫu khai báo trường nhập liệu dùng chung.

---

## 3. Quản lý Sản phẩm (Product)

### Vai trò chịu trách nhiệm: `editor` hoặc `admin`
- **Controller chính:** [ProductController.php](../app/Http/Controllers/ProductController.php)
- **Form Request validate:** `ProductRequest.php`

**Các chức năng cốt lõi:**
- **CRUD sản phẩm:** Tạo mới, chỉnh sửa và xóa sản phẩm.
- **Quản lý tồn kho & giá cả:** Cập nhật số lượng sản phẩm trong kho (`stock`) và giá bán (`price`).
- **Tải lên hình ảnh:** Hỗ trợ upload ảnh sản phẩm, tự động lưu vào đĩa lưu trữ `public` và xóa bỏ file ảnh cũ trong storage khi cập nhật ảnh mới hoặc xóa sản phẩm để tiết kiệm không gian máy chủ.
- **Thống kê kho hàng:** Hiển thị tổng số sản phẩm, số sản phẩm sắp hết hàng (tồn kho dưới hoặc bằng 10) và ước tính tổng giá trị kho hàng (`SUM(price * stock)`).

**Các View Blade liên quan:**
- [products/index.blade.php](../resources/views/products/index.blade.php) - Danh sách sản phẩm kèm bộ lọc.
- [products/create.blade.php](../resources/views/products/create.blade.php) - Form tạo sản phẩm mới.
- [products/edit.blade.php](../resources/views/products/edit.blade.php) - Form sửa sản phẩm.
- [products/show.blade.php](../resources/views/products/show.blade.php) - Xem chi tiết thông số sản phẩm và hình ảnh minh họa.

---

## 4. Mặt tiền Cửa hàng (Public Shop)

Giao diện bán hàng công khai phục vụ khách mua hàng không bắt buộc đăng nhập:
- **Layout giao diện:** [layouts/shop.blade.php](../resources/views/layouts/shop.blade.php)
- **ShopController:** Hiển thị sản phẩm, xem chi tiết sản phẩm công khai.
- **ShopCartController:** Quản lý giỏ hàng thông qua Session (thêm, cập nhật số lượng, xóa sản phẩm khỏi giỏ).
- **ShopCheckoutController:** Xử lý đặt hàng, tạo mã đơn hàng duy nhất và liên kết chuyển hướng sang cổng thanh toán VNPay Sandbox.

**Các View Blade liên quan:**
- [shop/index.blade.php](../resources/views/shop/index.blade.php) - Trang trưng bày sản phẩm có bộ tìm kiếm và lọc theo danh mục sản phẩm.
- [shop/show.blade.php](../resources/views/shop/show.blade.php) - Trang chi tiết sản phẩm dành cho khách hàng.
- [shop/cart.blade.php](../resources/views/shop/cart.blade.php) - Giao diện giỏ hàng của khách.
- [shop/checkout.blade.php](../resources/views/shop/checkout.blade.php) - Form khai báo thông tin đặt hàng (Tên, SĐT, Email, Địa chỉ nhận hàng).
- [shop/payment-result.blade.php](../resources/views/shop/payment-result.blade.php) - Giao diện hiển thị trạng thái kết quả thanh toán VNPay.

---

## 5. Blade Component Alert (`x-package-alert`)

Để tối ưu hóa mã nguồn giao diện và tái sử dụng, hệ thống xây dựng một Custom Blade Component để hiển thị thông báo:
- **Class xử lý:** [app/View/Components/Alert.php](../app/View/Components/Alert.php)
- **View component:** [resources/views/components/alert.blade.php](../resources/views/components/alert.blade.php)
- **Cách dùng trong Blade:**
  ```blade
  <x-package-alert type="success" message="Thao tác thành công!" />
  ```

### Tại sao sử dụng thuộc tính `novalidate` trên thẻ `<form>`?
- Mặc định, trình duyệt sẽ chặn hành động submit nếu một thẻ input có thuộc tính `required` bị bỏ trống, và hiển thị bong bóng thông báo lỗi riêng của trình duyệt.
- Để hệ thống Laravel thực hiện validate ở phía máy chủ (Backend) và hiển thị thông báo lỗi đồng bộ, đẹp mắt qua component `<x-package-alert>`, tất cả các form chính trong dự án đều được thêm thuộc tính `novalidate`.
- Việc này giúp form luôn được submit lên máy chủ, Laravel tiến hành kiểm tra dữ liệu và trả về lỗi biểu mẫu qua biến `$errors` để hiển thị danh sách lỗi chi tiết ở đầu trang.
- **Cách thử nghiệm nhanh:** Vào trang tạo người dùng mới, để trống tất cả các trường và ấn nút submit, khung thông báo lỗi màu đỏ của Alert component sẽ xuất hiện ngay ở đầu form.

## 6. Thiết kế giao diện và Độ tương phản (UI Contrast)
Dự án được tối ưu hóa độ tương phản giao diện (UI Contrast) trên các trang quản trị để cải thiện trải nghiệm người dùng:
- Màu văn bản mặc định được chuyển sang các tông màu đậm, rõ ràng hơn để tránh mỏi mắt.
- Các liên kết breadcrumb, menu điều hướng phụ (settings nav) và văn bản mô tả trong các thẻ card được tinh chỉnh độ tương phản cao, đảm bảo hiển thị sắc nét trên cả nền sáng lẫn nền tối.
