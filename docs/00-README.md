# Tài liệu Dự án (Project Docs)

Bộ tài liệu này được viết chi tiết để giúp người đọc hiểu toàn bộ dự án Laravel hiện tại, từ tổng quan hệ thống, luồng dữ liệu, cho đến cấu trúc mã nguồn chi tiết để phục vụ thi vấn đáp.

## Thứ tự nên đọc để nắm dự án:

1. [01-OVERVIEW.md](./01-OVERVIEW.md) - Tổng quan dự án, mục tiêu và công nghệ.
2. [02-ROUTES.md](./02-ROUTES.md) - Danh sách toàn bộ các tuyến đường (route) và phân quyền.
3. [03-DATABASE.md](./03-DATABASE.md) - Cấu trúc cơ sở dữ liệu SQLite, các bảng và migration.
4. [04-AUTH-ROLE-PROFILE.md](./04-AUTH-ROLE-PROFILE.md) - Đăng nhập, phân quyền middleware và thông tin profile.
5. [05-PRODUCTS-USERS-UI.md](./05-PRODUCTS-USERS-UI.md) - Chức năng CRUD sản phẩm, người dùng và thiết kế giao diện.
6. [06-ORAL-EXAM-NOTES.md](./06-ORAL-EXAM-NOTES.md) - Những ghi chú và điểm quan trọng cần lưu ý khi thi vấn đáp.
7. [07-ARTICLE-TAG-FACTORY-SEEDING.md](./07-ARTICLE-TAG-FACTORY-SEEDING.md) - Quản lý bài viết và nhãn bài viết (Many-to-Many).
8. [08-CONTROLLER-BY-CONTROLLER.md](./08-CONTROLLER-BY-CONTROLLER.md) - Chi tiết từng phương thức của các controller trong dự án.
9. [09-ORDERS-CHATBOT-PAYMENT.md](./09-ORDERS-CHATBOT-PAYMENT.md) - Luồng xử lý đơn hàng, chatbot AI và cổng thanh toán VNPay.
10. [10-LARAVEL-OPTIMIZATION.md](./10-LARAVEL-OPTIMIZATION.md) - Cách tối ưu mã nguồn (Service, Event, Listener, Queue, Eager Loading).
11. [11-FULL-PROJECT-GUIDE.md](./11-FULL-PROJECT-GUIDE.md) - Hướng dẫn cài đặt dự án, danh sách tài khoản demo và tổng quan nghiệp vụ.
12. [12-LECTURE-MATERIALS/README.md](./12-LECTURE-MATERIALS/README.md) - Liên kết với tài liệu bài giảng gốc của môn học.
13. [13-CHATBOT-AGENT-GUIDE.md](./13-CHATBOT-AGENT-GUIDE.md) - Hướng dẫn chi tiết luồng hoạt động của Chatbot AI và package `laravel/ai`.
14. [14-TAI-LIEU-VAN-DAP-CHI-TIET.md](./14-TAI-LIEU-VAN-DAP-CHI-TIET.md) - Tài liệu ôn tập câu hỏi vấn đáp chi tiết nhất.
15. [15-ARCHITECTURE-OVERVIEW.md](./15-ARCHITECTURE-OVERVIEW.md) - Tổng quan kiến trúc tổng thể (bản đồ thiết kế dự án).
16. [16-TONG-QUAN-VA-CAI-DAT.md](./16-TONG-QUAN-VA-CAI-DAT.md) - Bản đọc nhanh về luồng chạy và cấu hình các dịch vụ.

## Cách tiếp cận code nhanh nhất:

Để hiểu cách một chức năng hoạt động trong dự án này:
- Bắt đầu từ file định nghĩa tuyến đường: `routes/web.php`
- Chuyển tiếp đến Controller tương ứng xử lý request.
- Tìm hiểu các Model kết nối cơ sở dữ liệu.
- Cuối cùng là xem giao diện Blade hiển thị thông tin ra sao.

**Luồng dữ liệu chung của dự án:**
`Tuyến đường (Route) -> Middleware (Phân quyền/Tuổi) -> Controller -> Model/Query -> Giao diện (View)`

**Luồng nghiệp vụ xử lý đơn hàng (đã được tối ưu hóa):**
`Route -> Controller -> OrderService -> Event -> Listener -> Mail`

## Các cập nhật gần đây:
- **Chuẩn hóa chữ Việt:** Toàn bộ tài liệu hỗ trợ ôn tập vấn đáp trong thư mục `docs/` đã được sửa lỗi hiển thị chữ và chuyển sang Tiếng Việt có dấu chuẩn xác.
- **Đồng bộ hóa giao diện:** Giao diện đăng nhập, đăng ký và shop đã được thiết kế đồng bộ theo một phong cách thống nhất.
- **Tích hợp VNPay:** Thay thế phương thức thanh toán ví điện tử Momo cũ bằng VNPay Sandbox trực quan và hoạt động ổn định hơn.
- **Bổ sung chú thích mã nguồn:** Đã bổ sung ghi chú Tiếng Việt có dấu trực tiếp trong mã nguồn tại các Controllers, Models, Services, AI Tools và Events/Listeners để người xem dễ hiểu.
