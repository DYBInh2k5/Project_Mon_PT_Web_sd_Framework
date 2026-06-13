# 06. Oral Exam Notes

## 1. Gioi thieu ngan ve project

Đây là project Laravel dung Blade, Tailwind va Alpine de demo:

- auth
- role
- user management
- product/category CRUD
- order management
- chatbot hỗ trợ khách hàng
- thanh toán online dang demo
- profile
- Blade component
- migration cho demo quan he n-n
- trang hiện thi articles va tags bang Eloquent

## 2. Neu co hoi ve route

Tra loi:

- route duoc dinh nghia trong `routes/web.php` va `routes/auth.php`
- route se di qua middleware truoc, sau do moi vao controller

## 3. Neu co hoi ve role

Tra loi:

- role luu trong bang `users`
- middleware `EnsureUserHasRole` doc role hiện tai
- neu dung role thi `return $next($request)`
- neu sai role thi `abort(403)`

## 4. Neu co hoi ve profile

Tra loi:

- profile tach rieng thanh bang `profiles`
- quan he `1-1` với `users`
- `ProfileController` dung Eloquent qua quan he `User hasOne Profile`
- dữ liệu profile không chi lay tu `users` nua

## 5. Neu co hoi ve Alert Component

Tra loi:

- em tao `Alert` class component
- đăng ký alias la `x-package-alert`
- component nhan `type`, `message`, `messages`
- dung de hiện 1 thong bao hoac nhieu loi
- em da gan vao cac form chinh cua project

## 6. Neu co hoi vi sao alert luc dau không hiện

Tra loi:

- vi trinh duyet co HTML5 validation mac dinh
- no chan submit va hiện popup rieng
- em them `novalidate` de form submit len Laravel
- sau do Laravel validate va tra ve `x-package-alert`

## 7. Neu co hoi ve migration Article - Tag

Tra loi:

- em tao model `Article` va `Tag`
- em tao bang trung gian `article_tag`
- day la de chuan bi cho buoi demo quan he nhieu-nhieu

## 8. Neu co hoi ve quan he Article - Tag

Tra loi:

- `Article` co `belongsTo(User::class)` vi moi article thuoc ve 1 user
- `Article` co `belongsToMany(Tag::class)` vi 1 article co nhieu tag
- `Tag` co `belongsToMany(Article::class)` vi 1 tag co the gan cho nhieu article
- `User` co `hasMany(Article::class)` de truy cap danh sach article cua user

## 9. Neu co hoi ve factory va seeding

Tra loi:

- em tao `ArticleFactory` de sinh `user_id`, `title`, `body`
- em tao `TagFactory` de sinh dữ liệu cho cot `tag`
- trong `DatabaseSeeder` em tao user, tao tag, tao article
- sau do em gan nhieu tag cho tung article qua bang `article_tag`

## 10. Neu co hoi ve dữ liệu da sinh chua

Tra loi:

- da co dữ liệu that trong SQLite
- hiện tai co `16 users`, `50 articles`, `20 tags`, `500 dong article_tag`

## 11. Neu co hoi ve trang danh sach Articles

Tra loi:

- em tao `ArticleController` bang resource controller
- em đăng ký `Route::resource('articles', ArticleController::class)` trong `routes/web.php`
- method `index()` dung `Article::with(['user', 'tags'])->get()` de lay article kem user va tag bang Eloquent
- view `resources/views/article/list.blade.php` hiện thi title, user, body, created_at va tags
- trong view em dung quan he `$article->user->name` va `$article->tags`

## 12. Neu co hoi ve bug MySQL

Tra loi:

- em them `Schema::defaultStringLength(191)` trong `AppServiceProvider`
- muc dich la tranh loi `Specified key was too long`

## 13. Neu co hoi ve đơn hàng

Tra loi:

- em co model `Order` va `OrderItem`
- co danh sach đơn hàng, chi tiết đơn hàng, tim theo ma don/ten/email/so dien thoai, loc theo ngay, loc theo trạng thái
- chi tiết đơn hàng cho xem thông tin khách hàng va sản phẩm trong don
- khi cập nhật trạng thái, he thong gửi mail thong bao cho khach

## 14. Neu co hoi ve chatbot

Tra loi:

- em tao chatbot hỗ trợ khách hàng ngay trong admin app
- bot tra loi theo tu khoa ve đơn hàng, giao hang, huy don, mail thong bao
- neu nhap ma don nhu `ORD-00023` thi bot doc dữ liệu that trong SQLite

## 15. Neu co hoi ve thanh toán online

Tra loi:

- em lam man checkout demo cho tung đơn hàng
- khi thanh toán thành công, he thong cập nhật `payment_status`, `payment_method`, `transaction_code`, `paid_at`
- dong thoi neu don dang `pending` thi doi sang `processing`

## 16. Cau tra loi tong ket ngan

“Project cua em gom auth, role, user management, product/category CRUD, order management, chatbot hỗ trợ khách hàng, payment demo va profile. Em dung middleware de phan quyền, dung Eloquent qua quan he `User hasOne Profile` cho profile, tao Blade component `Alert` de hiện thong bao loi/thành công. Ngoai ra em da tao model `Article`, `Tag`, bang trung gian `article_tag`, dinh nghia quan he, tao factory va seed dữ liệu gia. Phan đơn hàng cua em co đổi trạng thái, gửi mail, chatbot va checkout demo de phuc vu bai project.” 
