# 07. Bài Viết, Nhãn Bài Viết Và Dữ Liệu Mẫu (Article, Tag, Factory, Seeding)

## 1. Mục tiêu của phần này

Phần này được xây dựng nhằm chuẩn bị cho nội dung demo mối quan hệ Nhiều-Nhiều (Many-to-Many) trong Laravel môn học:
- Model **`Article`** (Bài viết)
- Model **`Tag`** (Nhãn bài viết)
- Bảng trung gian **`article_tag`** (Bảng kết nối bài viết và nhãn)

---

## 2. Các file mã nguồn liên quan

### Models:
- [app/Models/Article.php](../app/Models/Article.php) - Định nghĩa thực thể Article và quan hệ.
- [app/Models/Tag.php](../app/Models/Tag.php) - Định nghĩa thực thể Tag và quan hệ.
- [app/Models/User.php](../app/Models/User.php) - Định nghĩa quan hệ User sở hữu nhiều bài viết.

### Controllers & Routes:
- [app/Http/Controllers/ArticleController.php](../app/Http/Controllers/ArticleController.php) - Lấy danh sách bài viết.
- [routes/web.php](../routes/web.php) - Định nghĩa tuyến đường `/articles`.

### Database Migrations:
- [2026_05_11_090000_create_articles_table.php](../database/migrations/2026_05_11_090000_create_articles_table.php) - Tạo bảng `articles`.
- [2026_05_11_090100_create_tags_table.php](../database/migrations/2026_05_11_090100_create_tags_table.php) - Tạo bảng `tags`.
- [2026_05_11_090200_create_article_tag_table.php](../database/migrations/2026_05_11_090200_create_article_tag_table.php) - Tạo bảng trung gian `article_tag`.

### Factories (Sinh dữ liệu ảo):
- [database/factories/ArticleFactory.php](../database/factories/ArticleFactory.php) - Định nghĩa cấu trúc sinh bài viết ảo.
- [database/factories/TagFactory.php](../database/factories/TagFactory.php) - Định nghĩa cấu trúc sinh nhãn ảo.
- [database/factories/UserFactory.php](../database/factories/UserFactory.php) - Định nghĩa cấu trúc sinh người dùng ảo.

### Seeders (Bơm dữ liệu):
- [database/seeders/DatabaseSeeder.php](../database/seeders/DatabaseSeeder.php) - File seeder trung tâm quản lý luồng đổ dữ liệu.

### Views (Giao diện hiển thị):
- [resources/views/article/list.blade.php](../resources/views/article/list.blade.php) - Trang hiển thị danh sách bài viết kèm nhãn.

---

## 3. Định nghĩa quan hệ giữa các Model (Eloquent Relationships)

### Mối quan hệ 1-N: User ↔ Article
- Một người dùng (`User`) có thể viết nhiều bài viết (`Article`).
- Một bài viết (`Article`) chỉ thuộc sở hữu của duy nhất một người dùng (`User`).
- **Trong Model User:**
  ```php
  public function articles() {
      return $this->hasMany(Article::class);
  }
  ```
- **Trong Model Article:**
  ```php
  public function user() {
      return $this->belongsTo(User::class);
  }
  ```

### Mối quan hệ N-N: Article ↔ Tag
- Một bài viết (`Article`) có thể được gắn nhiều nhãn khác nhau (`Tag`).
- Một nhãn (`Tag`) có thể xuất hiện trong nhiều bài viết khác nhau (`Article`).
- Cần sử dụng bảng trung gian tên là `article_tag`.
- **Trong Model Article:**
  ```php
  public function tags() {
      return $this->belongsToMany(Tag::class);
  }
  ```
- **Trong Model Tag:**
  ```php
  public function articles() {
      return $this->belongsToMany(Article::class);
  }
  ```

---

## 4. Danh sách các cột chính trong Migration

### Bảng bài viết (`articles`)
- `id` (Primary Key)
- `user_id` (Khóa ngoại liên kết tới bảng `users`)
- `title` (Tiêu đề bài viết)
- `body` (Nội dung chi tiết bài viết)

### Bảng nhãn (`tags`)
- `id` (Primary Key)
- `tag` (Tên nhãn, ví dụ: Công nghệ, Thể thao, Thời trang)

### Bảng trung gian (`article_tag`)
- `id` (Primary Key)
- `article_id` (Khóa ngoại liên kết tới bảng `articles`)
- `tag_id` (Khóa ngoại liên kết tới bảng `tags`)

---

## 5. Cơ chế sinh dữ liệu mẫu bằng Factory & Seeder

### Cách thức hoạt động của các Factory:
- **`UserFactory`:** Tự động sinh ngẫu nhiên tên người dùng, địa chỉ email, đặt vai trò mặc định là `user` và kích hoạt tài khoản.
- **`ArticleFactory`:** Sinh ngẫu nhiên tiêu đề và nội dung bài viết bằng thư viện Faker. Khóa ngoại `user_id` được gán ngẫu nhiên bằng cách truy vấn một ID bất kỳ hiện có từ bảng `users`.
- **`TagFactory`:** Sinh các từ khóa ngắn ngẫu nhiên để gán làm nhãn bài viết.

### Thứ tự thực hiện đổ dữ liệu mẫu trong `DatabaseSeeder.php`:
1. Tạo 2 tài khoản quản trị mẫu cố định (`admin@example.com` và `support@example.com`).
2. Sinh thêm các tài khoản người dùng ảo bằng `UserFactory` nếu số lượng chưa đủ 14.
3. Duyệt qua toàn bộ người dùng hiện có để khởi tạo bản ghi hồ sơ (`profiles`) tương ứng.
4. Tạo danh mục sản phẩm và sản phẩm ảo cho cửa hàng.
5. Tạo 20 nhãn ảo bằng `TagFactory`.
6. Tạo 50 bài viết ảo bằng `ArticleFactory`.
7. **Gắn liên kết Nhiều-Nhiều:** Duyệt qua 50 bài viết ảo vừa tạo, lấy ngẫu nhiên một mảng chứa từ 1 đến 10 ID nhãn trong danh sách 20 nhãn hiện có, sử dụng phương thức `sync()` hoặc `attach()` để tự động điền bản ghi vào bảng trung gian `article_tag`.

---

## 6. Trang danh sách bài viết công khai

Tuyến đường hiển thị danh sách bài viết:
```php
Route::resource('articles', ArticleController::class);
```

Trong phương thức `index` của `ArticleController.php`, hệ thống thực hiện truy vấn:
```php
// Sử dụng Eager Loading với phương thức with() để nạp trước User và các Tags đi kèm bài viết.
// Giúp giảm tối đa số câu lệnh SELECT gửi tới SQLite (Tránh lỗi N+1 Query).
$articles = Article::with(['user', 'tags'])->get();

return view('article.list', ['articles' => $articles]);
```

**Cách hiển thị trên giao diện Blade (`article/list.blade.php`):**
- Vòng lặp `@foreach` duyệt qua danh sách `$articles`.
- Hiển thị tên tác giả thông qua quan hệ 1-1: `$article->user->name`.
- Lặp tiếp danh sách nhãn đi kèm bài viết thông qua quan hệ nhiều-nhiều:
  ```blade
  @foreach($article->tags as $tag)
      <span class="badge">{{ $tag->tag }}</span>
  @endforeach
  ```

## 7. Gợi ý trình bày khi vấn đáp
> "Trong dự án, em đã cài đặt module Bài viết và Nhãn để minh họa mối quan hệ Nhiều - Nhiều. Em định nghĩa phương thức `belongsToMany` trong cả hai Model `Article` và `Tag`, sử dụng bảng trung gian là `article_tag`. Để chuẩn bị dữ liệu thử nghiệm, em viết các Factory để tự động sinh bài viết và nhãn ảo, sau đó dùng Seeder để tạo các liên kết thực tế. Khi tải trang danh sách bài viết, em áp dụng Eager Loading thông qua câu lệnh `Article::with(['user', 'tags'])->get()` giúp nạp trước các quan hệ liên kết và tối ưu hóa hiệu năng bằng cách loại bỏ lỗi N+1 query."
