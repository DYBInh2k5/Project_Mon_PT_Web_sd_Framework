# 08. Bản Đồ Controller (Controller By Controller)

## 1. Mục đích của tài liệu này

Tài liệu này ánh xạ chi tiết từng Controller quan trọng trong dự án để phục vụ thi vấn đáp:
- Vị trí tệp tin Controller trong mã nguồn.
- Vai trò, nhiệm vụ chính của Controller.
- Danh sách các phương thức xử lý và kết quả trả về tương ứng (view/redirect).

Nếu giáo viên hỏi *"File nào chịu trách nhiệm xử lý chức năng X"*, bạn có thể sử dụng tài liệu này để định vị và trả lời chính xác.

## 2. Thư mục chứa các Controller

- `app/Http/Controllers/` - Chứa các Controller chính của hệ thống.
- `app/Http/Controllers/Auth/` - Chứa các Controller xử lý chức năng xác thực tài khoản.
- `app/Http/Controllers/Settings/` - Chứa các Controller xử lý cài đặt tài khoản của người dùng.

---

## 3. Chi tiết các Controller Quản trị (Admin/Editor Area)

### 3.1. DashboardController
- **Đường dẫn:** [DashboardController.php](../app/Http/Controllers/DashboardController.php)
- **Nhiệm vụ:** Xử lý hiển thị trang chủ quản trị (`/dashboard`).
- **Các phương thức:**
  - `index(Request $request)`: Tổng hợp các số liệu thống kê nhanh trong cơ sở dữ liệu (tổng số người dùng, tổng số sản phẩm, tổng doanh thu, số đơn hàng gần đây) và truyền dữ liệu sang view `pages.dashboard`.

### 3.2. UserController
- **Đường dẫn:** [UserController.php](../app/Http/Controllers/UserController.php)
- **Nhiệm vụ:** Quản lý toàn bộ chức năng CRUD tài khoản người dùng và hồ sơ (Profile) đi kèm (chỉ dành cho vai trò `admin`).
- **Các phương thức:**
  - `index(Request $request)`: Tải danh sách người dùng phân trang, hỗ trợ tìm kiếm theo tên/email, lọc theo vai trò và trạng thái hoạt động. Trả về view `users.index`.
  - `create()`: Trả về view `users.create` chứa biểu mẫu tạo mới tài khoản.
  - `store(StoreUserRequest $request)`: Thực hiện lưu tài khoản mới sau khi validate dữ liệu và tự động khởi tạo bản ghi hồ sơ trống liên kết (quan hệ 1-1). Redirect về `users.index`.
  - `show(User $user)`: Nạp thông tin hồ sơ thông qua `load('profile')` để hiển thị trang chi tiết người dùng tại view `users.show`.
  - `edit(User $user)`: Trả về view `users.edit` để hiển thị biểu mẫu chỉnh sửa tài khoản và hồ sơ.
  - `update(UpdateUserRequest $request, User $user)`: Cập nhật song song thông tin bảng `users` và bảng `profiles` (bao gồm xử lý tải lên ảnh đại diện mới và xóa ảnh cũ). Redirect về `users.index`.
  - `toggleStatus(User $user, Request $request)`: Bật/Tắt nhanh trạng thái hoạt động của tài khoản người dùng (ngăn chặn tự tắt chính mình).
  - `destroy(User $user, Request $request)`: Xóa tài khoản người dùng khỏi hệ thống (ngăn chặn tự xóa tài khoản đang đăng nhập).

### 3.3. ProductController
- **Đường dẫn:** [ProductController.php](../app/Http/Controllers/ProductController.php)
- **Nhiệm vụ:** Quản lý CRUD sản phẩm trong hệ thống (dành cho vai trò `editor` hoặc `admin`).
- **Các phương thức:**
  - `index(Request $request)`: Hiển thị bảng sản phẩm có kèm tên danh mục liên kết, hỗ trợ tìm kiếm từ khóa, lọc theo danh mục, lọc theo trạng thái và phân trang. Trả về view `products.index`.
  - `create()`: Trả về view `products.create` kèm theo danh sách danh mục để người dùng lựa chọn.
  - `store(ProductRequest $request)`: Thực hiện lưu sản phẩm mới, xử lý lưu trữ file hình ảnh sản phẩm tải lên vào thư mục public. Redirect về `products.index`.
  - `show(Product $product)`: Xem chi tiết sản phẩm và ảnh tại view `products.show`.
  - `edit(Product $product)`: Hiển thị form sửa sản phẩm và danh sách danh mục để chọn lại tại view `products.edit`.
  - `update(ProductRequest $request, Product $product)`: Cập nhật thông tin sản phẩm, xử lý xóa ảnh cũ khỏi ổ đĩa nếu khách hàng upload ảnh sản phẩm mới. Redirect về `products.index`.
  - `destroy(Product $product)`: Xóa sản phẩm khỏi database và xóa file ảnh liên kết trên ổ đĩa. Redirect về `products.index`.

### 3.4. ProductCategoryController
- **Đường dẫn:** [ProductCategoryController.php](../app/Http/Controllers/ProductCategoryController.php)
- **Nhiệm vụ:** Quản lý CRUD danh mục sản phẩm (dành cho vai trò `editor` hoặc `admin`).
- **Các phương thức:**
  - `index(Request $request)`: Liệt kê danh sách danh mục sản phẩm, tính toán số lượng sản phẩm thuộc mỗi danh mục bằng phương thức `withCount('products')`. Trả về view `product-categories.index`.
  - `create()`: Trả về form tạo danh mục mới tại view `product-categories.create`.
  - `store(ProductCategoryRequest $request)`: Lưu danh mục mới và gán khóa ngoại `created_by` của người dùng hiện tại.
  - `show(ProductCategory $productCategory)`: Hiển thị chi tiết danh mục và danh sách sản phẩm liên kết tại view `product-categories.show`.
  - `edit(ProductCategory $productCategory)`: Trả về form sửa danh mục tại view `product-categories.edit`.
  - `update(ProductCategoryRequest $request, ProductCategory $productCategory)`: Cập nhật thông tin danh mục.
  - `destroy(ProductCategory $productCategory)`: Xóa danh mục khỏi cơ sở dữ liệu.

### 3.5. OrderController
- **Đường dẫn:** [OrderController.php](../app/Http/Controllers/OrderController.php)
- **Nhiệm vụ:** Quản lý danh sách đơn hàng và cập nhật trạng thái đơn hàng (dành cho vai trò `editor` hoặc `admin`).
- **Các phương thức:**
  - `index(Request $request)`: Liệt kê danh sách đơn hàng sử dụng các local scope từ Model Order để tìm kiếm từ khóa, lọc theo trạng thái đơn hàng, lọc theo thời gian đặt hàng (từ ngày, đến ngày) và sắp xếp đơn mới lên đầu. Trả về view `orders.index`.
  - `show(Order $order)`: Hiển thị chi tiết hóa đơn đặt hàng, danh sách sản phẩm đã mua và lịch sử thay đổi trạng thái tại view `orders.show`.
  - `updateStatus(UpdateOrderStatusRequest $request, Order $order, OrderService $orders)`: Tiếp nhận yêu cầu đổi trạng thái đơn, gọi xử lý nghiệp vụ tại `OrderService` (đổi trạng thái đơn, ghi nhật ký đổi trạng thái và phát event gửi mail tự động). Redirect về `orders.show`.

---

## 4. Chi tiết các Controller dành cho Khách hàng (Public Shop Area)

### 4.1. ShopController
- **Đường dẫn:** [ShopController.php](../app/Http/Controllers/ShopController.php)
- **Nhiệm vụ:** Hiển thị sản phẩm ra mặt tiền cửa hàng công khai cho khách xem.
- **Các phương thức:**
  - `index(Request $request)`: Hiển thị trang chủ shop công khai, hỗ trợ tìm kiếm sản phẩm theo tên, lọc sản phẩm theo danh mục và hiển thị ảnh. Trả về view `shop.index`.
  - `show(Product $product)`: Trang xem thông tin chi tiết của một sản phẩm dành cho khách hàng. Trả về view `shop.show`.

### 4.2. ShopCartController
- **Đường dẫn:** [ShopCartController.php](../app/Http/Controllers/ShopCartController.php)
- **Nhiệm vụ:** Quản lý các thao tác thêm, bớt, sửa, xóa sản phẩm trong giỏ hàng (lưu trữ thông tin trong Session).
- **Các phương thức:**
  - `index()`: Hiển thị trang giỏ hàng cá nhân tại view `shop.cart`.
  - `store(Product $product, Request $request)`: Thêm sản phẩm vào giỏ hàng.
  - `update(Product $product, Request $request)`: Thay đổi số lượng mua của một mặt hàng trong giỏ.
  - `destroy(Product $product)`: Xóa bỏ một sản phẩm ra khỏi giỏ hàng.
  - `clear()`: Xóa sạch toàn bộ sản phẩm trong giỏ hàng.

### 4.3. ShopCheckoutController
- **Đường dẫn:** [ShopCheckoutController.php](../app/Http/Controllers/ShopCheckoutController.php)
- **Nhiệm vụ:** Xử lý biểu mẫu thanh toán đơn hàng và liên kết với cổng VNPay Sandbox.
- **Các phương thức:**
  - `create(ShoppingCartService $cart)`: Hiển thị form điền thông tin đặt hàng tại view `shop.checkout`.
  - `store(Request $request, ShoppingCartService $cart, VnpayPaymentService $vnpay)`: Xác nhận đơn hàng, khởi tạo giao dịch trong database với trạng thái thanh toán là `unpaid`, sau đó tạo URL và chuyển hướng người dùng sang cổng thanh toán VNPay Sandbox.
  - `vnpayReturn(Request $request, VnpayPaymentService $vnpay, OrderService $orders)`: Tiếp nhận phản hồi từ trình duyệt sau khi thanh toán xong trên VNPay, thực hiện kiểm tra chữ ký checksum và số tiền thanh toán. Nếu thành công sẽ cập nhật đơn hàng thành đã thanh toán (`paid`, `processing`) và hiển thị kết quả tại view `shop.payment-result`.
  - `ipn(Request $request, VnpayPaymentService $vnpay, OrderService $orders)`: Tiếp nhận truy vấn ngầm bất đồng bộ (IPN) từ server VNPay gửi tới nhằm đối soát giao dịch độc lập, bảo mật và cập nhật trạng thái đơn hàng an toàn nhất. Trả về chuỗi JSON phản hồi cho VNPay.

---

## 5. Chi tiết các Controller Thành viên & Tính năng khác

### 5.1. Settings/ProfileController
- **Đường dẫn:** [Settings/ProfileController.php](../app/Http/Controllers/Settings/ProfileController.php)
- **Nhiệm vụ:** Cho phép người dùng tự xem và cập nhật thông tin cá nhân hoặc xóa tài khoản của mình.
- **Các phương thức:**
  - `show(Request $request)`: Hiển thị thông tin hồ sơ của người dùng đang đăng nhập tại view `pages.profile`.
  - `edit(Request $request)`: Hiển thị biểu mẫu chỉnh sửa thông tin hồ sơ tại view `pages.auth.settings.profile`.
  - `update(Request $request)`: Validate thông tin cá nhân và cập nhật vào cơ sở dữ liệu.
  - `destroy(Request $request)`: Xóa vĩnh viễn tài khoản của chính người dùng sau khi xác nhận mật khẩu hiện tại.

### 5.2. SupportChatController
- **Đường dẫn:** [SupportChatController.php](../app/Http/Controllers/SupportChatController.php)
- **Nhiệm vụ:** Quản lý giao diện cửa sổ chat hỗ trợ trực tuyến của người dùng.
- **Các phương thức:**
  - `index(Request $request)`: Lấy ra hoặc khởi tạo phiên hội thoại (`AgentConversation`) của người dùng hiện tại, tải danh sách tin nhắn cũ từ cơ sở dữ liệu để Alpine.js render và hiển thị cửa sổ chat tại view `support.chat`.
  - `clear(Request $request)`: Xóa toàn bộ lịch sử trò chuyện của người dùng trong database để bắt đầu một phiên hội thoại mới.

### 5.3. ChatController
- **Đường dẫn:** [ChatController.php](../app/Http/Controllers/ChatController.php)
- **Nhiệm vụ:** Đây là Single Action Controller (chỉ có duy nhất phương thức `__invoke`) nhận nhiệm vụ tiếp nhận câu hỏi của người dùng, lưu trữ vào cơ sở dữ liệu, gọi Agent AI sinh câu trả lời, lưu câu trả lời vào database và trả phản hồi dạng JSON về giao diện.

### 5.4. ArticleController
- **Đường dẫn:** [ArticleController.php](../app/Http/Controllers/ArticleController.php)
- **Nhiệm vụ:** Demo mối quan hệ Nhiều - Nhiều (Many-to-Many).
- **Các phương thức:**
  - `index()`: Truy vấn danh sách bài viết sử dụng Eager Loading `Article::with(['user', 'tags'])->get()` để tránh lỗi N+1 query và hiển thị tại view `article.list`.
