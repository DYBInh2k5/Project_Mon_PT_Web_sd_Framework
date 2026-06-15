# 03. Cơ Sở Dữ Liệu (Database)

## 1. Hệ quản trị cơ sở dữ liệu sử dụng

Dự án hiện tại sử dụng **SQLite** làm cơ sở dữ liệu chính:

- Đường dẫn file mặc định: [database/database.sqlite](../database/database.sqlite)

> [!NOTE]
> **Lưu ý quan trọng đối với môi trường Windows:**
> - File SQLite nằm trực tiếp trong thư mục dự án có thể xảy ra xung đột khóa ghi file của hệ điều hành, dẫn đến lỗi `disk I/O error` khi thực hiện ghi dữ liệu.
> - Để đảm bảo quá trình demo chạy mượt mà và ổn định, file cấu hình `.env` được chuyển hướng sử dụng cơ sở dữ liệu SQLite đặt tạm trong thư mục `Temp` của hệ thống Windows.
> - Đây là giải pháp xử lý môi trường (workaround) và hoàn toàn không làm ảnh hưởng tới logic xử lý nghiệp vụ của Laravel.

## 2. Danh sách các file Migration đã tạo

Các file migration nằm trong thư mục `database/migrations/` chịu trách nhiệm định nghĩa cấu trúc bảng:

1. `create_users_table` - Tạo bảng người dùng hệ thống (`users`).
2. `create_cache_table` - Tạo bảng lưu trữ bộ nhớ đệm (`cache`).
3. `create_jobs_table` - Tạo bảng quản lý hàng đợi tác vụ (`jobs`).
4. `add_role_to_users_table` - Bổ sung cột phân quyền `role` vào bảng `users`.
5. `create_product_categories_table` - Tạo bảng danh mục sản phẩm (`product_categories`).
6. `create_products_table` - Tạo bảng sản phẩm (`products`).
7. `add_image_path_to_products_table` - Bổ sung cột lưu đường dẫn hình ảnh sản phẩm `image_path` vào bảng `products`.
8. `add_is_active_to_users_table` - Bổ sung cột trạng thái kích hoạt tài khoản `is_active` cho bảng `users`.
9. `create_profiles_table` - Tạo bảng hồ sơ người dùng cá nhân (`profiles`).
10. `create_articles_table` - Tạo bảng bài viết (`articles`).
11. `create_tags_table` - Tạo bảng nhãn bài viết (`tags`).
12. `create_article_tag_table` - Tạo bảng trung gian (`article_tag`) kết nối mối quan hệ Many-to-Many giữa bài viết và nhãn.
13. `create_orders_table` - Tạo bảng đơn đặt hàng (`orders`).
14. `create_order_items_table` - Tạo bảng chi tiết mặt hàng trong đơn hàng (`order_items`).
15. `add_payment_fields_to_orders_table` - Bổ sung các cột thông tin thanh toán VNPay (payment_status, payment_method, transaction_code, paid_at).
16. `create_order_status_histories_table` - Tạo bảng lịch sử thay đổi trạng thái đơn hàng (`order_status_histories`).
17. `add_search_indexes_to_orders_table` - Thêm các chỉ mục (index) tìm kiếm để tối ưu hóa truy vấn cho bảng đơn hàng.

---

## 3. Cấu trúc các bảng cốt lõi

### Bảng người dùng (`users`)
Lưu trữ thông tin tài khoản đăng nhập và phân quyền chính:
- `id` (Primary Key)
- `name` (Tên hiển thị tài khoản)
- `email` (Địa chỉ email đăng nhập, duy nhất)
- `email_verified_at` (Thời gian xác minh email)
- `password` (Mật khẩu tài khoản đã mã hóa)
- `role` (Vai trò phân quyền: `admin`, `editor`, `user`)
- `is_active` (Trạng thái tài khoản: kích hoạt `true`, bị khóa `false`)

### Bảng hồ sơ người dùng (`profiles`)
Mối quan hệ 1-1 đối với bảng `users` (`User hasOne Profile`):
- `id` (Primary Key)
- `user_id` (Khóa ngoại liên kết bảng `users`)
- `full_name` (Họ và tên đầy đủ)
- `address` (Địa chỉ giao hàng/cư trú)
- `avatar` (Đường dẫn lưu file ảnh đại diện)
- `birthday` (Ngày sinh nhật)
- `gender` (Giới tính)
- `phone` (Số điện thoại liên hệ)

### Bảng danh mục sản phẩm (`product_categories`)
- `id` (Primary Key)
- `name` (Tên danh mục)
- `slug` (Chuỗi tối ưu hóa đường dẫn tĩnh URL)
- `description` (Mô tả danh mục)
- `is_active` (Trạng thái hiển thị danh mục)
- `created_by` (Khóa ngoại liên kết bảng `users` chỉ ra người tạo)

### Bảng sản phẩm (`products`)
Mối quan hệ N-1 đối với bảng `product_categories` (`Product belongsTo ProductCategory`):
- `id` (Primary Key)
- `product_category_id` (Khóa ngoại liên kết danh mục)
- `name` (Tên sản phẩm)
- `slug` (Slug URL sản phẩm)
- `sku` (Mã định danh kho hàng duy nhất)
- `price` (Giá bán sản phẩm)
- `stock` (Số lượng tồn kho)
- `description` (Mô tả sản phẩm)
- `image_path` (Đường dẫn ảnh sản phẩm tải lên)
- `is_active` (Trạng thái hiển thị sản phẩm)
- `created_by` (Người tạo sản phẩm)

### Bảng bài viết & Nhãn bài viết (Many-to-Many Demo)
Mối quan hệ nhiều-nhiều được kết nối thông qua bảng trung gian:
- Bảng `articles`: `id`, `user_id`, `title`, `body`.
- Bảng `tags`: `id`, `tag` (tên nhãn).
- Bảng trung gian `article_tag`: `id`, `article_id` (khóa ngoại bài viết), `tag_id` (khóa ngoại nhãn bài viết).

---

## 4. Cơ chế Đơn Hàng (Order Schema)

### Bảng đơn đặt hàng (`orders`)
Lưu trữ thông tin giao dịch tổng thể của đơn hàng:
- `id` (Primary Key)
- `order_number` (Mã đơn hàng định dạng duy nhất, ví dụ: `WEB-20260615-ABCD`)
- `customer_name` (Tên người nhận)
- `customer_email` (Email người nhận)
- `customer_phone` (Số điện thoại người nhận)
- `customer_address` (Địa chỉ giao nhận hàng)
- `notes` (Ghi chú mua hàng từ khách)
- `status` (Trạng thái xử lý đơn hàng: `pending`, `processing`, `completed`, `cancelled`)
- `payment_status` (Trạng thái thanh toán: `unpaid` - chưa trả, `paid` - đã trả)
- `payment_method` (Phương thức thanh toán: `vnpay`, `cod`)
- `transaction_code` (Mã giao dịch trả về từ cổng thanh toán VNPay)
- `paid_at` (Thời điểm hoàn tất thanh toán)
- `total_amount` (Tổng giá trị hóa đơn thanh toán)
- `placed_at` (Thời điểm đặt đơn hàng)

### Bảng chi tiết đơn hàng (`order_items`)
Mối quan hệ 1-N đối với bảng `orders` (`Order hasMany OrderItem`):
- `id` (Primary Key)
- `order_id` (Khóa ngoại đơn hàng)
- `product_id` (Khóa ngoại liên kết bảng sản phẩm)
- `product_name` (Lưu tên sản phẩm tại thời điểm mua để tránh bị thay đổi lịch sử khi sửa sản phẩm)
- `quantity` (Số lượng mua)
- `unit_price` (Giá bán tại thời điểm mua)
- `line_total` (Thành tiền của dòng sản phẩm: `unit_price * quantity`)

### Bảng lịch sử trạng thái đơn hàng (`order_status_histories`)
Lưu vết nhật ký thay đổi trạng thái đơn hàng để phục vụ quản trị và theo dõi:
- `id` (Primary Key)
- `order_id` (Khóa ngoại đơn hàng)
- `changed_by` (Khóa ngoại liên kết người dùng thực hiện thay đổi trạng thái đơn)
- `previous_status` (Trạng thái trước khi đổi)
- `new_status` (Trạng thái mới cập nhật)
- `note` (Ghi chú lý do thay đổi trạng thái đơn hàng)

---

## 5. Tối ưu hóa chỉ mục (Indexes) cho bảng Đơn hàng

Hệ thống bổ sung các chỉ mục (index) tại database để tối ưu hóa hiệu năng truy vấn dữ liệu lớn khi lọc và tìm kiếm đơn hàng:
- **`status`**: Tối ưu hóa tốc độ tải trang khi quản trị viên lọc danh sách đơn hàng theo trạng thái (`pending`, `processing`, `completed`).
- **`placed_at`**: Đẩy nhanh tốc độ sắp xếp và tìm kiếm đơn hàng trong khoảng thời gian từ ngày này đến ngày khác.
- **`customer_phone`** & **`customer_email`**: Gia tăng tốc độ tìm kiếm khi nhập số điện thoại hoặc email khách hàng trên thanh tìm kiếm của Admin.

## 6. Dữ liệu thử nghiệm có sẵn sau khi chạy Seeder (Database Seeded)
Sau khi chạy câu lệnh `php artisan db:seed`, SQLite sẽ tự động được làm đầy bằng các dữ liệu giả lập chất lượng:
- **16 Người dùng:** Có đầy đủ 3 vai trò `admin`, `editor`, `user` phân chia rõ ràng.
- **50 Bài viết (`articles`) & 20 Nhãn (`tags`):** Liên kết thành công 500 bản ghi quan hệ trong bảng trung gian `article_tag`.
- **25 Đơn hàng (`orders`):** Trải dài từ các trạng thái chưa thanh toán, đã thanh toán VNPay cho tới hoàn thành.
- **56 Mặt hàng chi tiết (`order_items`):** Thể hiện chi tiết mua sắm nhiều mặt hàng trên mỗi đơn hàng mẫu.

## 7. Các lệnh Artisan quản lý cơ sở dữ liệu thường dùng
```powershell
# Chạy tất cả các migrations để dựng cấu trúc bảng mới
php artisan migrate

# Kiểm tra trạng thái chạy của các migrations
php artisan migrate:status

# Dọn sạch và tạo lại toàn bộ bảng cơ sở dữ liệu từ đầu
php artisan migrate:fresh

# Nạp dữ liệu mẫu thử nghiệm vào database
php artisan db:seed
```
