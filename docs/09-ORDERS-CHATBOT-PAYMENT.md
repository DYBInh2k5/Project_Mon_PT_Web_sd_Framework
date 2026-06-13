# 09. Orders, Chatbot, Payment

## 1. Order management

Model:

- [Order.php](../app/Models/Order.php)
- [OrderItem.php](../app/Models/OrderItem.php)
- [OrderStatusHistory.php](../app/Models/OrderStatusHistory.php)

Controller:

- [OrderController.php](../app/Http/Controllers/OrderController.php)

Service/Event/Listener:

- [OrderService.php](../app/Services/OrderService.php)
- [OrderStatusUpdated.php](../app/Events/OrderStatusUpdated.php)
- [SendOrderStatusUpdatedMail.php](../app/Listeners/SendOrderStatusUpdatedMail.php)

View:

- [orders/index.blade.php](../resources/views/orders/index.blade.php)
- [orders/show.blade.php](../resources/views/orders/show.blade.php)

Chuc nang:

- xem danh sach đơn hàng
- loc theo trạng thái
- tim theo ma don, ten khach, email, so dien thoai
- tim theo ngay `from - to`
- sap xep moi den cu
- xem chi tiết đơn hàng
- xem thông tin khách hàng
- cập nhật trạng thái đơn hàng
- lưu lịch sử đổi trạng thái đơn hàng

## 2. Mail khi đổi trạng thái

File:

- [OrderStatusUpdatedMail.php](../app/Mail/OrderStatusUpdatedMail.php)
- [status-updated.blade.php](../resources/views/emails/orders/status-updated.blade.php)

Luong:

1. nhan vien doi status trong `orders.show`
2. `OrderController@updateStatus` goi `OrderService`
3. `OrderService` cập nhật dữ liệu va ghi `order_status_histories`
4. `OrderService` phat event `OrderStatusUpdated`
5. listener `SendOrderStatusUpdatedMail` gửi mail thong bao cho `customer_email`

Luu y:

- trong môi trường hiện tai, `MAIL_MAILER=log`
- `QUEUE_CONNECTION=sync` nen listener queue chay ngay trong luc demo
- nghia la mail duoc ghi vao log de demo, không gui ra hop thu that

## 3. Customer support chatbot

File:

- [SupportChatController.php](../app/Http/Controllers/SupportChatController.php)
- [CustomerSupportChatbot.php](../app/Support/CustomerSupportChatbot.php)
- [GeminiChatService.php](../app/Services/GeminiChatService.php)
- [chat.blade.php](../resources/views/support/chat.blade.php)

Chuc nang:

- tra loi moi cau hoi bang Gemini API với context cua project
- neu cau hoi co ma don thi tra cuu đơn hàng that trong SQLite truoc
- tra ve cau tra loi duoi dang JSON de UI render on dinh
- lưu lịch sử chat trong session
- doc ma don that nhu `ORD-00023`

Neu gap ma don:

- bot se truy van bang `orders`
- tra ve trạng thái, tong tien, thoi gian dat hang

Ghi chu:

- `laravel/boost` da duoc cai lam dev dependency de hỗ trợ workflow AI cua du an
- chatbot runtime van goi Gemini API bang `GEMINI_API_KEY` trong `.env`
- prompt duoc bo sung ngung canh tu `.ai/guidelines/project-chatbot.md` va `docs/11-FULL-PROJECT-GUIDE.md`
- neu Gemini het quota hoac bi loi, chatbot van tra loi bang local knowledge fallback cua project
- nut truy cap chatbot duoc ghep thanh widget nho co dinh o goc duoi ben phai

## 4. Payment demo

File:

- [OrderPaymentController.php](../app/Http/Controllers/OrderPaymentController.php)
- [ProcessOrderPaymentRequest.php](../app/Http/Requests/ProcessOrderPaymentRequest.php)
- [orders/payment.blade.php](../resources/views/orders/payment.blade.php)

Migration:

- [2026_05_18_103000_add_payment_fields_to_orders_table.php](../database/migrations/2026_05_18_103000_add_payment_fields_to_orders_table.php)

Field moi trong `orders`:

- `payment_status`
- `payment_method`
- `transaction_code`
- `paid_at`

Luong demo:

1. mở chi tiết đơn hàng
2. bam `Open checkout`
3. nhap thông tin thanh toán
4. submit
5. he thong cập nhật `payment_status = paid`
6. sinh `transaction_code`
7. neu status cu la `pending` thi goi `OrderService` doi sang `processing`

## 5. Public shop + cart + VNPay checkout

File:

- [ShopController.php](../app/Http/Controllers/ShopController.php)
- [ShopCartController.php](../app/Http/Controllers/ShopCartController.php)
- [ShopCheckoutController.php](../app/Http/Controllers/ShopCheckoutController.php)
- [ShoppingCartService.php](../app/Services/ShoppingCartService.php)
- [VnpayPaymentService.php](../app/Services/VnpayPaymentService.php)
- [shop/index.blade.php](../resources/views/shop/index.blade.php)
- [shop/cart.blade.php](../resources/views/shop/cart.blade.php)
- [shop/checkout.blade.php](../resources/views/shop/checkout.blade.php)
- [shop/payment-result.blade.php](../resources/views/shop/payment-result.blade.php)

Route:

- `/`
- `/shop`
- `/cart`
- `/checkout`
- `/checkout/vnpay/return`
- `/checkout/vnpay/ipn`

Chuc nang:

- hiện mặt tiền shop công khai cho khach xem sản phẩm
- tim sản phẩm va loc theo danh mục
- them sản phẩm vao giỏ hàng
- cập nhật so luồng va xoa sản phẩm trong gio
- tao đơn hàng tu giỏ hàng
- tao URL thanh toán VNPay theo đơn hàng
- VNPay hiện thi QR/checkout page cho khach
- returnUrl va IPN se cập nhật `payment_status`, `payment_method`, `transaction_code`, `paid_at`

Luu y:

- vi day la luồng VNPay checkout, shop public van hiện thi gia theo VND khi checkout
- neu chưa cấu hình du `VNPAY_TMN_CODE` va `VNPAY_HASH_SECRET` thi thanh toán se bao loi ro rang

## 6. Seeder lien quan

File:

- [DatabaseSeeder.php](../database/seeders/DatabaseSeeder.php)

Hien tai da tao:

- `25` orders
- `56` order_items

Vi du ma don co the demo:

- `ORD-00023`
- `ORD-00025`

## 7. Cach demo nhanh trên lop

1. vao `/orders`
2. loc theo `pending` hoac `processing`
3. mở chi tiết 1 đơn hàng
4. doi status đơn hàng de demo lich su trạng thái va gửi mail
5. bam `Open checkout` de demo payment
6. vao `/support-chat` va nhap `Kiem tra don ORD-00023`

## 8. Cau tra loi ngan de vấn đáp

“Em da bo sung module đơn hàng gom danh sach, chi tiết, loc theo ngay va trạng thái. Khi đổi trạng thái đơn hàng, controller goi `OrderService`, service ghi lich su vao `order_status_histories`, phat event va listener gửi mail thong bao cho khach. Em cùng tao chatbot hỗ trợ khách hàng co the doc ma don that trong SQLite. Ngoai ra em lam man thanh toán online dang demo cho tung đơn hàng, cập nhật `payment_status`, `payment_method`, `transaction_code` va `paid_at`.”
