# 09. Orders, Chatbot, Payment

## 1. Order management

Model:

- [Order.php](../app/Models/Order.php)
- [OrderItem.php](../app/Models/OrderItem.php)

Controller:

- [OrderController.php](../app/Http/Controllers/OrderController.php)

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

## 2. Mail khi doi trang thai

File:

- [OrderStatusUpdatedMail.php](../app/Mail/OrderStatusUpdatedMail.php)
- [status-updated.blade.php](../resources/views/emails/orders/status-updated.blade.php)

Luong:

1. nhan vien doi status trong `orders.show`
2. `OrderController@updateStatus` cap nhat du lieu
3. Laravel gui mail thong bao cho `customer_email`

Luu y:

- trong moi truong hien tai, `MAIL_MAILER=log`
- nghia la mail duoc ghi vao log de demo, khong gui ra hop thu that

## 3. Customer support chatbot

File:

- [SupportChatController.php](../app/Http/Controllers/SupportChatController.php)
- [CustomerSupportChatbot.php](../app/Support/CustomerSupportChatbot.php)
- [chat.blade.php](../resources/views/support/chat.blade.php)

Chuc nang:

- tra loi cau hoi theo tu khoa
- goi y prompt mau
- luu lich su chat trong session
- doc ma don that nhu `ORD-00023`

Neu gap ma don:

- bot se truy van bang `orders`
- tra ve trang thai, tong tien, thoi gian dat hang

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
7. neu status cu la `pending` thi doi sang `processing`

## 5. Seeder lien quan

File:

- [DatabaseSeeder.php](../database/seeders/DatabaseSeeder.php)

Hien tai da tao:

- `25` orders
- `56` order_items

Vi du ma don co the demo:

- `ORD-00023`
- `ORD-00025`

## 6. Cach demo nhanh tren lop

1. vao `/orders`
2. loc theo `pending` hoac `processing`
3. mo chi tiet 1 don hang
4. doi status don hang de demo gui mail
5. bam `Open checkout` de demo payment
6. vao `/support-chat` va nhap `Kiem tra don ORD-00023`

## 7. Cau tra loi ngan de van dap

“Em da bo sung module don hang gom danh sach, chi tiet, loc theo ngay va trang thai. Khi doi trang thai don hang, he thong gui mail thong bao cho khach. Em cung tao chatbot ho tro khach hang co the doc ma don that trong SQLite. Ngoai ra em lam man thanh toan online dang demo cho tung don hang, cap nhat `payment_status`, `payment_method`, `transaction_code` va `paid_at`.”
