# 10. Laravel Optimization

## 1. Muc tieu

File nay ghi lai cac phan toi uu Laravel da bat dau ap dung vao project.

Muc tieu:

- controller gon hon
- logic nghiệp vụ tach rieng
- truy van de bao tri hon
- co lich su thay doi đơn hàng
- gửi mail theo luồng Event/Listener cua Laravel

## 2. List toi uu da lam

### OrderService

File:

- [OrderService.php](../app/Services/OrderService.php)

Vai tro:

- xu ly nghiệp vụ cập nhật trạng thái đơn hàng
- ghi lich su đổi trạng thái
- phat event `OrderStatusUpdated`

Luong:

`OrderController -> OrderService -> OrderStatusUpdated event`

### Event va Listener

File:

- [OrderStatusUpdated.php](../app/Events/OrderStatusUpdated.php)
- [SendOrderStatusUpdatedMail.php](../app/Listeners/SendOrderStatusUpdatedMail.php)

Vai tro:

- khi trạng thái đơn hàng thay doi, Laravel phat event
- listener nhan event va gửi mail thong bao cho khách hàng
- listener implement `ShouldQueue`
- hiện tai `.env` dang `QUEUE_CONNECTION=sync`, nen queue chay ngay de demo duoc trên lop

### Order status history

File:

- [OrderStatusHistory.php](../app/Models/OrderStatusHistory.php)
- [2026_05_26_090000_create_order_status_histories_table.php](../database/migrations/2026_05_26_090000_create_order_status_histories_table.php)

Bang `order_status_histories` luu:

- `order_id`
- `changed_by`
- `previous_status`
- `new_status`
- `note`
- `created_at`

Y nghia:

- xem lai lich su đổi trạng thái
- biet ai da doi
- biet doi tu trạng thái nao sang trạng thái nao

### Local scope trong Order model

File:

- [Order.php](../app/Models/Order.php)

Da them:

- `scopeSearch()`
- `scopeStatus()`
- `scopePlacedFrom()`
- `scopePlacedUntil()`

Y nghia:

- controller không can viet query dai
- query tim kiem/loc nam trong model
- de tai su dung neu sau nay co API hoac dashboard

### Database index cho orders

File:

- [2026_05_26_090100_add_search_indexes_to_orders_table.php](../database/migrations/2026_05_26_090100_add_search_indexes_to_orders_table.php)

Da index:

- `status`
- `placed_at`
- `customer_phone`
- `customer_email`

Y nghia:

- toi uu loc theo trạng thái
- toi uu loc theo ngay
- hỗ trợ tim kiem theo phone/email tot hon

### Eager loading

Da ap dung:

- `OrderController@show`: load `items.product` va `statusHistories.changer`
- `ArticleController@index`: load `user` va `tags`

Y nghia:

- tranh loi N+1 query
- khi view goi quan he thi dữ liệu da duoc load san

### Windows cache workaround

Tren Windows, Laravel co the loi `Access is denied` khi ghi Blade compiled file
hoac manifest bang `rename()`.

De on dinh project nay, minh da:

- dung `base_path('cache/views')` cho compiled view path
- bind `SafeFilesystem` trong `AppServiceProvider`
- ghi file cache truc tiep thay vi dua vao `rename()` atomically

Đây là workaround cho môi trường Windows cua project, không phai thay doi nghiệp vụ.

## 3. Luong cập nhật trạng thái đơn hàng sau toi uu

1. Editor/Admin vao chi tiết đơn hàng
2. Submit form doi status
3. `OrderController@updateStatus` nhan request
4. Controller goi `OrderService@updateStatus`
5. Service cập nhật bang `orders`
6. Service ghi them 1 dong vao `order_status_histories`
7. Service phat event `OrderStatusUpdated`
8. Listener `SendOrderStatusUpdatedMail` gửi mail cho khach
9. View `orders.show` hiện lich su status

## 4. Cach giai thich khi vấn đáp

“Luc dau em co the cập nhật status va gửi mail truc tiep trong controller. Sau do em toi uu theo Laravel bang cach tach logic sang `OrderService`, dung event `OrderStatusUpdated` va listener `SendOrderStatusUpdatedMail` de gửi mail. Em cùng tao bang `order_status_histories` de lưu lịch sử đổi trạng thái, va them local scope trong model `Order` de controller gon hon khi tim kiem va loc đơn hàng.”

## 5. Cac toi uu co the lam tiep

- Policy cho tung model
- Soft delete cho product/order
- Notification noi bo cho admin/editor
- dashboard chart doanh thu
- test rieng cho event/listener va order status history
