# 07. Article, Tag, Factory, Seeding

## 1. Muc tieu cua phan nay

Phan nay duoc lam de chuan bi cho buoi demo quan he nhieu-nhieu trong Laravel.

Ba thanh phan chinh:

- model `Article`
- model `Tag`
- bang trung gian `article_tag`

## 2. Cac file lien quan

### Model

- [app/Models/Article.php](../app/Models/Article.php)
- [app/Models/Tag.php](../app/Models/Tag.php)
- [app/Models/User.php](../app/Models/User.php)

### Controller va route

- [app/Http/Controllers/ArticleController.php](../app/Http/Controllers/ArticleController.php)
- [routes/web.php](../routes/web.php)

### Migration

- [2026_05_11_090000_create_articles_table.php](../database/migrations/2026_05_11_090000_create_articles_table.php)
- [2026_05_11_090100_create_tags_table.php](../database/migrations/2026_05_11_090100_create_tags_table.php)
- [2026_05_11_090200_create_article_tag_table.php](../database/migrations/2026_05_11_090200_create_article_tag_table.php)

### Factory

- [database/factories/ArticleFactory.php](../database/factories/ArticleFactory.php)
- [database/factories/TagFactory.php](../database/factories/TagFactory.php)
- [database/factories/UserFactory.php](../database/factories/UserFactory.php)

### Seeder

- [database/seeders/DatabaseSeeder.php](../database/seeders/DatabaseSeeder.php)

### View

- [resources/views/article/list.blade.php](../resources/views/article/list.blade.php)
- [resources/views/layouts/app1.blade.php](../resources/views/layouts/app1.blade.php)

## 3. Quan he giua cac model

### User - Article

- 1 `User` co nhieu `Article`
- 1 `Article` thuoc ve 1 `User`

Trong code:

- `User::articles()` la `hasMany`
- `Article::user()` la `belongsTo`

### Article - Tag

- 1 `Article` co nhieu `Tag`
- 1 `Tag` co nhieu `Article`

Trong code:

- `Article::tags()` la `belongsToMany`
- `Tag::articles()` la `belongsToMany`

Bang trung gian de noi 2 bang nay la:

- `article_tag`

## 4. Fillable

### Article

```php
protected $fillable = [
    'user_id',
    'title',
    'body',
];
```

### Tag

```php
protected $fillable = [
    'tag',
];
```

## 5. Migration

### Bang `articles`

Cot chinh:

- `id`
- `user_id`
- `title`
- `body`
- `created_at`
- `updated_at`

### Bang `tags`

Cot chinh:

- `id`
- `tag`
- `created_at`
- `updated_at`

### Bang `article_tag`

Cot chinh:

- `id`
- `article_id`
- `tag_id`
- `created_at`
- `updated_at`

## 6. Factory hoat dong nhu the nao

### UserFactory

Sinh:

- `name`
- `email`
- `role`
- `is_active`
- `password`

### ArticleFactory

Sinh:

- `user_id`
  - lay ngau nhien 1 `id` tu bang `users`
- `title`
- `body`

### TagFactory

Sinh:

- `tag`

## 7. Seeder hoat dong nhu the nao

Trong [DatabaseSeeder.php](../database/seeders/DatabaseSeeder.php), project dang lam:

1. tao user mau
   - `admin@example.com`
   - `support@example.com`
2. neu user chua du thi tao them bang `UserFactory`
3. tao `Profile` cho tung user
4. tao du lieu cho category va product
5. tao them `Tag`
6. tao them `Article`
7. gan nhieu `Tag` cho tung `Article`

Doan logic gan tag:

- lay danh sach `tag id`
- tron ngau nhien
- cat ra toi da 10 tag
- gan vao article qua `article_tag`

## 8. So luong du lieu hien tai

Sau khi seed, so luong du lieu da kiem tra:

- `users = 16`
- `articles = 50`
- `tags = 20`
- `article_tag = 500`

## 9. Trang hien thi danh sach Articles

De khop bai tren lop, project co them:

```php
Route::resource('articles', ArticleController::class);
```

Trong `ArticleController@index`:

```php
$articles = Article::with(['user', 'tags'])->get();

return view('article.list', ['articles' => $articles]);
```

View `article.list` hien thi:

- title cua article
- user tao article qua `$article->user->name`
- body
- created_at
- danh sach tag qua `$article->tags`

## 10. Cach giai thich khi van dap

Ban co the noi:

“Em tao `Article` va `Tag` de demo quan he nhieu-nhieu. `Article` thuoc ve `User` va co nhieu `Tag`. `Tag` cung co the thuoc nhieu `Article`, nen em dung `belongsToMany` o ca hai model. Bang trung gian `article_tag` dung de luu cap `article_id` va `tag_id`. Sau do em tao factory de sinh du lieu gia va dung `DatabaseSeeder` de do du lieu vao SQLite. Em tao them `ArticleController@index` dung `Article::with(['user', 'tags'])->get()` va view `article.list` de hien danh sach article kem user va tags bang Eloquent, dong thoi tranh N+1 query.”

## 11. Lenh hay dung

```powershell
php artisan migrate
php artisan migrate:status
php artisan db:seed
```
