# 03. Database

## 1. Database dang dung

Project hien dang dung SQLite:

- [database/database.sqlite](../database/database.sqlite)

## 2. Cac migration hien co

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

## 3. Bang users

Thong tin chinh:

- `name`
- `email`
- `password`
- `role`
- `is_active`

`role` dung de phan quyen:

- `admin`
- `editor`
- `user`

## 4. Bang profiles

Quan he 1-1 voi `users`.

Thong tin:

- `user_id`
- `full_name`
- `address`
- `avatar`
- `birthday`
- `gender`
- `phone`

## 5. Bang product_categories

Thong tin chinh:

- `name`
- `slug`
- `description`
- `is_active`
- `created_by`

## 6. Bang products

Thong tin chinh:

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

Thong tin chinh:

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

Thong tin chinh:

- `order_id`
- `product_id`
- `product_name`
- `quantity`
- `unit_price`
- `line_total`

## 10. Du lieu gia dang co trong SQLite

Sau khi seed, du lieu hien tai da co:

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
- bang `article_tag` da co du lieu lien ket that
- co don hang de demo danh sach, chi tiet, doi trang thai
- co order item de demo san pham trong tung don
- co field thanh toan de demo online payment

## 11. AppServiceProvider va bug MySQL

Trong [app/Providers/AppServiceProvider.php](../app/Providers/AppServiceProvider.php) co:

```php
Schema::defaultStringLength(191);
```

Dong nay de tranh loi:

- `Specified key was too long`

## 12. Lenh hay dung

```powershell
php artisan migrate
php artisan migrate:status
php artisan db:seed
```
