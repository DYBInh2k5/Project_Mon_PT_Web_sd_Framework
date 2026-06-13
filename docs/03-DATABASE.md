# 03. Database

## 1. Database đang dùng

Project hiện đang dùng SQLite:

- [database/database.sqlite](../database/database.sqlite)

Luu y trên may nay:

- file SQLite trong thư mục `database/` co the gap `disk I/O error` khi ghi
- de demo on dinh, `.env` dang tro sang ban SQLite trong thư mục temp cua Windows
- day la workaround cho môi trường hiện tai, không doi logic nghiệp vụ

## 2. Cac migration hiện co

- `create_users_table`
- `create_cache_table`
- `create_jobs_table`
- `add_role_to_users_table`
- `create_product_categories_table`
- `create_products_table`
- `add_image_path_to_products_table`
- `add_is_active_to_users_table`
- `create_profiles_table`
- `create_articles_table`
- `create_tags_table`
- `create_article_tag_table`
- `create_orders_table`
- `create_order_items_table`
- `add_payment_fields_to_orders_table`
- `create_order_status_histories_table`
- `add_search_indexes_to_orders_table`

## 3. Bang users

Thông tin chinh:

- `name`
- `email`
- `password`
- `role`
- `is_active`

`role` dung de phan quyền:

- `admin`
- `editor`
- `user`

## 4. Bang profiles

Quan he 1-1 với `users`.

Thông tin:

- `user_id`
- `full_name`
- `address`
- `avatar`
- `birthday`
- `gender`
- `phone`

## 5. Bang product_categories

Thông tin chinh:

- `name`
- `slug`
- `description`
- `is_active`
- `created_by`

## 6. Bang products

Thông tin chinh:

- `product_category_id`
- `name`
- `slug`
- `sku`
- `price`
- `stock`
- `description`
- `is_active`
- `image_path`
- `created_by`

## 7. Bang cho demo sau

### articles

- `id`
- `user_id`
- `title`
- `body`
- `timestamps`

### tags

- `id`
- `tag`
- `timestamps`

### article_tag

- `id`
- `article_id`
- `tag_id`
- `timestamps`

## 8. Bang orders

Thông tin chinh:

- `order_number`
- `customer_name`
- `customer_email`
- `customer_phone`
- `customer_address`
- `notes`
- `status`
- `payment_status`
- `payment_method`
- `transaction_code`
- `paid_at`
- `total_amount`
- `placed_at`

## 9. Bang order_items

Thông tin chinh:

- `order_id`
- `product_id`
- `product_name`
- `quantity`
- `unit_price`
- `line_total`

## 10. Bang order_status_histories

Thông tin chinh:

- `order_id`
- `changed_by`
- `previous_status`
- `new_status`
- `note`
- `created_at`

Y nghia:

- lưu lịch sử moi lan đổi trạng thái đơn hàng
- biet ai đổi trạng thái
- hỗ trợ demo toi uu Laravel bang Service, Event, Listener

## 11. Index toi uu cho orders

Project da them index cho:

- `status`
- `placed_at`
- `customer_phone`
- `customer_email`

Y nghia:

- loc theo status nhanh hon
- loc theo ngay nhanh hon
- tim theo phone/email tot hon

## 12. Du lieu gia đang có trong SQLite

Sau khi seed, dữ liệu hiện tai da co:

- `users = 16`
- `articles = 50`
- `tags = 20`
- `article_tag = 500`
- `orders = 25`
- `order_items = 56`

Y nghia:

- co user de gan cho article
- co article de demo quan he
- co tag de demo nhieu-nhieu
- bang `article_tag` da co dữ liệu lien ket that
- co đơn hàng de demo danh sach, chi tiết, đổi trạng thái
- co order item de demo sản phẩm trong tung don
- co field thanh toán de demo online payment

## 13. AppServiceProvider va bug MySQL

Trong [app/Providers/AppServiceProvider.php](../app/Providers/AppServiceProvider.php) co:

```php
Schema::defaultStringLength(191);
```

Dong nay de tranh loi:

- `Specified key was too long`

## 14. Lenh hay dung

```powershell
php artisan migrate
php artisan migrate:status
php artisan db:seed
```
