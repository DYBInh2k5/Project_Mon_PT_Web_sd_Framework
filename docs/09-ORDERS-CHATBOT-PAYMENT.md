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

- xem danh sach don hang
- loc theo trang thai
- tim theo ma don, ten khach, email, so dien thoai
- tim theo ngay `from - to`
- sap xep moi den cu
- xem chi tiet don hang
- xem thong tin khach hang
- cap nhat trang thai don hang
- luu lich su doi trang thai don hang

## 2. Mail khi doi trang thai

File:

- [OrderStatusUpdatedMail.php](../app/Mail/OrderStatusUpdatedMail.php)
- [status-updated.blade.php](../resources/views/emails/orders/status-updated.blade.php)

Luong:

1. nhan vien doi status trong `orders.show`
2. `OrderController@updateStatus` goi `OrderService`
3. `OrderService` cap nhat du lieu va ghi `order_status_histories`
4. `OrderService` phat event `OrderStatusUpdated`
5. listener `SendOrderStatusUpdatedMail` gui mail thong bao cho `customer_email`

Luu y:

- trong moi truong hien tai, `MAIL_MAILER=log`
- `QUEUE_CONNECTION=sync` nen listener queue chay ngay trong luc demo
- nghia la mail duoc ghi vao log de demo, khong gui ra hop thu that

## 3. Customer support chatbot

File:

- [SupportChatController.php](../app/Http/Controllers/SupportChatController.php)
- [CustomerSupportChatbot.php](../app/Support/CustomerSupportChatbot.php)
- [GeminiChatService.php](../app/Services/GeminiChatService.php)
- [chat.blade.php](../resources/views/support/chat.blade.php)

Chuc nang:

- tra loi moi cau hoi bang Gemini API voi context cua project
- neu cau hoi co ma don thi tra cuu don hang that trong SQLite truoc
- tra ve cau tra loi duoi dang JSON de UI render on dinh
- luu lich su chat trong session
- doc ma don that nhu `ORD-00023`

Neu gap ma don:

- bot se truy van bang `orders`
- tra ve trang thai, tong tien, thoi gian dat hang

Ghi chu:

- `laravel/boost` da duoc cai lam dev dependency de ho tro workflow AI cua du an
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

1. mo chi tiet don hang
2. bam `Open checkout`
3. nhap thong tin thanh toan
4. submit
5. he thong cap nhat `payment_status = paid`
6. sinh `transaction_code`
7. neu status cu la `pending` thi goi `OrderService` doi sang `processing`

## 5. Public shop + cart + MoMo checkout

File:

- [ShopController.php](../app/Http/Controllers/ShopController.php)
- [ShopCartController.php](../app/Http/Controllers/ShopCartController.php)
- [ShopCheckoutController.php](../app/Http/Controllers/ShopCheckoutController.php)
- [ShoppingCartService.php](../app/Services/ShoppingCartService.php)
- [MomoPaymentService.php](../app/Services/MomoPaymentService.php)
- [shop/index.blade.php](../resources/views/shop/index.blade.php)
- [shop/cart.blade.php](../resources/views/shop/cart.blade.php)
- [shop/checkout.blade.php](../resources/views/shop/checkout.blade.php)
- [shop/payment-result.blade.php](../resources/views/shop/payment-result.blade.php)

Route:

- `/`
- `/shop`
- `/cart`
- `/checkout`
- `/checkout/momo/return`
- `/checkout/momo/ipn`

Chuc nang:

- hien mat tien shop cong khai cho khach xem san pham
- tim san pham va loc theo danh muc
- them san pham vao gio hang
- cap nhat so luong va xoa san pham trong gio
- tao don hang tu gio hang
- gui request sang MoMo sandbox de lay `payUrl`
- nhan ket qua quay ve qua `redirectUrl`
- nhan thong bao server-to-server qua `ipnUrl`
- cap nhat `payment_status`, `payment_method`, `transaction_code`, `paid_at` khi giao dich thanh cong

Luu y:

- vi MoMo thanh toan bang VND, shop public hien thi gia theo VND khi checkout
- neu chua cau hinh du `MOMO_PARTNER_CODE`, `MOMO_ACCESS_KEY`, `MOMO_SECRET_KEY` thi thanh toan se bao loi ro rang

## 6. Seeder lien quan

File:

- [DatabaseSeeder.php](../database/seeders/DatabaseSeeder.php)

Hien tai da tao:

- `25` orders
- `56` order_items

Vi du ma don co the demo:

- `ORD-00023`
- `ORD-00025`

## 7. Cach demo nhanh tren lop

1. vao `/orders`
2. loc theo `pending` hoac `processing`
3. mo chi tiet 1 don hang
4. doi status don hang de demo lich su trang thai va gui mail
5. bam `Open checkout` de demo payment
6. vao `/support-chat` va nhap `Kiem tra don ORD-00023`

## 8. Cau tra loi ngan de van dap

“Em da bo sung module don hang gom danh sach, chi tiet, loc theo ngay va trang thai. Khi doi trang thai don hang, controller goi `OrderService`, service ghi lich su vao `order_status_histories`, phat event va listener gui mail thong bao cho khach. Em cung tao chatbot ho tro khach hang co the doc ma don that trong SQLite. Ngoai ra em lam man thanh toan online dang demo cho tung don hang, cap nhat `payment_status`, `payment_method`, `transaction_code` va `paid_at`.”
