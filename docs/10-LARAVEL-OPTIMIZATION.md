# 10. Laravel Optimization

## 1. Muc tieu

File nay ghi lai cac phan toi uu Laravel da bat dau ap dung vao project.

Muc tieu:

- controller gon hon
- logic nghiep vu tach rieng
- truy van de bao tri hon
- co lich su thay doi don hang
- gui mail theo luong Event/Listener cua Laravel

## 2. List toi uu da lam

### OrderService

File:

- [OrderService.php](../app/Services/OrderService.php)

Vai tro:

- xu ly nghiep vu cap nhat trang thai don hang
- ghi lich su doi trang thai
- phat event `OrderStatusUpdated`

Luong:

`OrderController -> OrderService -> OrderStatusUpdated event`

### Event va Listener

File:

- [OrderStatusUpdated.php](../app/Events/OrderStatusUpdated.php)
- [SendOrderStatusUpdatedMail.php](../app/Listeners/SendOrderStatusUpdatedMail.php)

Vai tro:

- khi trang thai don hang thay doi, Laravel phat event
- listener nhan event va gui mail thong bao cho khach hang
- listener implement `ShouldQueue`
- hien tai `.env` dang `QUEUE_CONNECTION=sync`, nen queue chay ngay de demo duoc tren lop

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

- xem lai lich su doi trang thai
- biet ai da doi
- biet doi tu trang thai nao sang trang thai nao

### Local scope trong Order model

File:

- [Order.php](../app/Models/Order.php)

Da them:

- `scopeSearch()`
- `scopeStatus()`
- `scopePlacedFrom()`
- `scopePlacedUntil()`

Y nghia:

- controller khong can viet query dai
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

- toi uu loc theo trang thai
- toi uu loc theo ngay
- ho tro tim kiem theo phone/email tot hon

### Eager loading

Da ap dung:

- `OrderController@show`: load `items.product` va `statusHistories.changer`
- `ArticleController@index`: load `user` va `tags`

Y nghia:

- tranh loi N+1 query
- khi view goi quan he thi du lieu da duoc load san

### Windows cache workaround

Tren Windows, Laravel co the loi `Access is denied` khi ghi Blade compiled file
hoac manifest bang `rename()`.

De on dinh project nay, minh da:

- dung `base_path('cache/views')` cho compiled view path
- bind `SafeFilesystem` trong `AppServiceProvider`
- ghi file cache truc tiep thay vi dua vao `rename()` atomically

Day la workaround cho moi truong Windows cua project, khong phai thay doi nghiep vu.

## 3. Luong cap nhat trang thai don hang sau toi uu

1. Editor/Admin vao chi tiet don hang
2. Submit form doi status
3. `OrderController@updateStatus` nhan request
4. Controller goi `OrderService@updateStatus`
5. Service cap nhat bang `orders`
6. Service ghi them 1 dong vao `order_status_histories`
7. Service phat event `OrderStatusUpdated`
8. Listener `SendOrderStatusUpdatedMail` gui mail cho khach
9. View `orders.show` hien lich su status

## 4. Cach giai thich khi van dap

“Luc dau em co the cap nhat status va gui mail truc tiep trong controller. Sau do em toi uu theo Laravel bang cach tach logic sang `OrderService`, dung event `OrderStatusUpdated` va listener `SendOrderStatusUpdatedMail` de gui mail. Em cung tao bang `order_status_histories` de luu lich su doi trang thai, va them local scope trong model `Order` de controller gon hon khi tim kiem va loc don hang.”

## 5. Cac toi uu co the lam tiep

- Policy cho tung model
- Soft delete cho product/order
- Notification noi bo cho admin/editor
- dashboard chart doanh thu
- test rieng cho event/listener va order status history
