# 10. Tối Ưu Hóa Hệ Thống Laravel (Laravel Optimization)

## 1. Mục tiêu tối ưu hóa

Tài liệu này ghi lại các kỹ thuật tối ưu hóa kiến trúc và hiệu năng hệ thống Laravel đã được áp dụng vào dự án. Mục tiêu hướng tới là:
- **Làm gọn Controller (Skinny Controller):** Đưa toàn bộ các câu truy vấn phức tạp hoặc logic nghiệp vụ ra khỏi Controller để dễ bảo trì.
- **Tách biệt nghiệp vụ (Separation of Concerns):** Sử dụng các Service Pattern để đóng gói logic xử lý đơn hàng và thanh toán.
- **Lập trình hướng sự kiện (Event-Driven):** Sử dụng hệ thống Event/Listener để tách rời các hành động phụ (như gửi email) khỏi luồng xử lý chính.
- **Tối ưu hóa truy vấn Database:** Thiết lập chỉ mục (Indexes) và áp dụng cơ chế nạp trước quan hệ (Eager Loading) để loại bỏ hoàn toàn lỗi N+1 query.

---

## 2. Các giải pháp tối ưu đã thực hiện

### 2.1. Đóng gói nghiệp vụ qua `OrderService`
- **Tệp tin xử lý:** [OrderService.php](../app/Services/OrderService.php)
- **Giải pháp:** Khi nhân viên cập nhật trạng thái đơn hàng, Controller không trực tiếp truy vấn DB. Thay vào đó, mọi hành động cập nhật trạng thái đơn, ghi nhật ký lịch sử trạng thái và phát sự kiện gửi mail đều được xử lý tập trung bên trong lớp `OrderService` dưới một Database Transaction để đảm bảo tính toàn vẹn dữ liệu.

### 2.2. Gửi email phi tập trung (Event & Listener)
- **Tệp tin xử lý:**
  - [OrderStatusUpdated.php](../app/Events/OrderStatusUpdated.php) (Lớp Event)
  - [SendOrderStatusUpdatedMail.php](../app/Listeners/SendOrderStatusUpdatedMail.php) (Lớp Listener)
- **Giải pháp:** Khi trạng thái đơn hàng thay đổi, hệ thống phát đi sự kiện `OrderStatusUpdated`. Bộ lắng nghe `SendOrderStatusUpdatedMail` (đã kích hoạt tính năng hàng đợi `ShouldQueue`) sẽ tự động đón bắt và thực hiện gửi email. Việc này giúp luồng phản hồi HTTP trả về cho người dùng nhanh hơn mà không phải chờ đợi máy chủ kết nối và gửi mail xong.

### 2.3. Nhật ký đổi trạng thái đơn hàng (`OrderStatusHistory`)
- **Tệp tin xử lý:** [OrderStatusHistory.php](../app/Models/OrderStatusHistory.php)
- **Giải pháp:** Tạo bảng cơ sở dữ liệu chuyên biệt để ghi nhận: ai đã đổi trạng thái đơn hàng, đổi từ trạng thái cũ nào sang trạng thái mới nào, lý do đổi và thời điểm thực hiện. Giúp người quản trị dễ dàng theo dõi và giám sát quy trình xử lý đơn hàng.

### 2.4. Rút gọn truy vấn bằng Local Query Scopes
- **Tệp tin xử lý:** [Order.php](../app/Models/Order.php)
- **Giải pháp:** Định nghĩa các Local Scope trong model `Order` như:
  - `scopeSearch()`: Tìm kiếm theo mã đơn hoặc thông tin liên hệ khách hàng.
  - `scopeStatus()`: Lọc nhanh theo trạng thái đơn hàng.
  - `scopePlacedFrom()` & `scopePlacedUntil()`: Lọc theo mốc thời gian.
- **Kết quả:** Code trong `OrderController` rất ngắn gọn và dễ hiểu, đồng thời các scope này có thể tái sử dụng dễ dàng ở nhiều vị trí khác trong tương lai (như trang Dashboard, thống kê báo cáo).

### 2.5. Chỉ mục cơ sở dữ liệu (Database Indexing)
- **Giải pháp:** Thiết lập chỉ mục (index) cho các cột thường xuyên được dùng để tìm kiếm và lọc trong bảng đơn hàng: `status`, `placed_at`, `customer_phone`, `customer_email`.
- **Kết quả:** Gia tăng tốc độ tìm kiếm và lọc dữ liệu trên trang danh sách đơn hàng của quản trị viên khi cơ sở dữ liệu phình to.

### 2.6. Giải quyết lỗi truy vấn N+1 (Eager Loading)
- **Giải pháp:**
  - Tại `OrderController@show`: Dùng `load(['items.product', 'statusHistories.changer'])` để nạp sẵn danh sách sản phẩm đã mua và người thực hiện thay đổi trạng thái.
  - Tại `ArticleController@index`: Sử dụng `Article::with(['user', 'tags'])->get()` để nạp trước tác giả và nhãn bài viết.
- **Kết quả:** Giảm số lượng câu lệnh SQL truy vấn gửi tới Database từ N+1 xuống chỉ còn 2-3 câu lệnh duy nhất.

### 2.7. Tối ưu hệ thống lưu File trên Windows (Windows File Cache Workaround)
- Do môi trường Windows có thể phát sinh lỗi cấp quyền hoặc khóa file tạm của Laravel khi biên dịch các file Blade Template hoặc manifest thông qua cơ chế `rename()` gốc.
- **Khắc phục:** Hệ thống chuyển hướng lưu trữ cache biên dịch Blade sang thư mục `cache/views` nằm tại thư mục gốc của dự án, đồng thời đăng ký một `SafeFilesystem` tùy chỉnh trong `AppServiceProvider` ghi file trực tiếp thay vì ghi tạm rồi đổi tên, đảm bảo dự án chạy ổn định 100% trên môi trường Windows.

---

## 3. Cách trả lời xuất sắc khi vấn đáp
> "Ban đầu, em có thể xử lý việc cập nhật trạng thái đơn hàng và gửi mail trực tiếp bên trong Controller. Tuy nhiên, cách làm đó sẽ khiến Controller bị phình to (Fat Controller) và khó bảo trì. Để tối ưu hóa, em đã tách toàn bộ logic nghiệp vụ đơn hàng ra lớp `OrderService`, sử dụng mô hình Event/Listener để tách rời tác vụ gửi email thông báo cho khách hàng ra khỏi luồng xử lý chính. Ngoài ra, em áp dụng Eager Loading với `with()` để triệt tiêu lỗi N+1 query trên trang bài viết và đơn hàng, đồng thời định nghĩa các Local Query Scopes trong Model `Order` để tái sử dụng mã nguồn và giúp Controller gọn gàng nhất."
