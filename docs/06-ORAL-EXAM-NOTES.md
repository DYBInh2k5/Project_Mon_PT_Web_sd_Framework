# 06. Oral Exam Notes

## 1. Gioi thieu ngan ve project

Day la project Laravel dung Blade, Tailwind va Alpine de demo:

- auth
- role
- user management
- product/category CRUD
- profile
- Blade component
- migration cho demo quan he n-n

## 2. Neu co hoi ve route

Tra loi:

- route duoc dinh nghia trong `routes/web.php` va `routes/auth.php`
- route se di qua middleware truoc, sau do moi vao controller

## 3. Neu co hoi ve role

Tra loi:

- role luu trong bang `users`
- middleware `EnsureUserHasRole` doc role hien tai
- neu dung role thi `return $next($request)`
- neu sai role thi `abort(403)`

## 4. Neu co hoi ve profile

Tra loi:

- profile tach rieng thanh bang `profiles`
- quan he `1-1` voi `users`
- `ProfileController` dung `Query Builder`
- du lieu profile khong chi lay tu `users` nua

## 5. Neu co hoi ve Alert Component

Tra loi:

- em tao `Alert` class component
- dang ky alias la `x-package-alert`
- component nhan `type`, `message`, `messages`
- dung de hien 1 thong bao hoac nhieu loi
- em da gan vao cac form chinh cua project

## 6. Neu co hoi vi sao alert luc dau khong hien

Tra loi:

- vi trinh duyet co HTML5 validation mac dinh
- no chan submit va hien popup rieng
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
- em tao `TagFactory` de sinh du lieu cho cot `tag`
- trong `DatabaseSeeder` em tao user, tao tag, tao article
- sau do em gan nhieu tag cho tung article qua bang `article_tag`

## 10. Neu co hoi ve du lieu da sinh chua

Tra loi:

- da co du lieu that trong SQLite
- hien tai co `16 users`, `50 articles`, `20 tags`, `500 dong article_tag`

## 11. Neu co hoi ve bug MySQL

Tra loi:

- em them `Schema::defaultStringLength(191)` trong `AppServiceProvider`
- muc dich la tranh loi `Specified key was too long`

## 12. Cau tra loi tong ket ngan

“Project cua em gom auth, role, user management, product/category CRUD va profile. Em dung middleware de phan quyen, dung Query Builder cho profile, tao Blade component `Alert` de hien thong bao loi/thanh cong. Ngoai ra em da tao model `Article`, `Tag`, bang trung gian `article_tag`, dinh nghia quan he, tao factory va seed du lieu gia de chuan bi cho demo quan he nhieu-nhieu.” 
