# Project Docs

Bo tài liệu nay duoc viet de giup doc va hieu toan bo project Laravel hiện tai.

Thu tu nen doc:

1. [01-OVERVIEW.md](./01-OVERVIEW.md)
2. [02-ROUTES.md](./02-ROUTES.md)
3. [03-DATABASE.md](./03-DATABASE.md)
4. [04-AUTH-ROLE-PROFILE.md](./04-AUTH-ROLE-PROFILE.md)
5. [05-PRODUCTS-USERS-UI.md](./05-PRODUCTS-USERS-UI.md)
6. [06-ORAL-EXAM-NOTES.md](./06-ORAL-EXAM-NOTES.md)
7. [07-ARTICLE-TAG-FACTORY-SEEDING.md](./07-ARTICLE-TAG-FACTORY-SEEDING.md)
8. [08-CONTROLLER-BY-CONTROLLER.md](./08-CONTROLLER-BY-CONTROLLER.md)
9. [09-ORDERS-CHATBOT-PAYMENT.md](./09-ORDERS-CHATBOT-PAYMENT.md)
10. [10-LARAVEL-OPTIMIZATION.md](./10-LARAVEL-OPTIMIZATION.md)
11. [11-FULL-PROJECT-GUIDE.md](./11-FULL-PROJECT-GUIDE.md)
12. [12-LECTURE-MATERIALS/README.md](./12-LECTURE-MATERIALS/README.md)
13. [13-CHATBOT-AGENT-GUIDE.md](./13-CHATBOT-AGENT-GUIDE.md)
14. [14-TAI-LIEU-VAN-DAP-CHI-TIET.md](./14-TAI-LIEU-VAN-DAP-CHI-TIET.md)
15. [15-ARCHITECTURE-OVERVIEW.md](./15-ARCHITECTURE-OVERVIEW.md)

Neu muon hieu nhanh project:

- bat dau tu `routes/web.php`
- doc controller tuong ung
- doc model
- sau do doc view

Luong chung:

`Route -> Middleware -> Controller -> Model/Query -> View`

Luong da toi uu cho đơn hàng:

`Route -> Controller -> OrderService -> Event -> Listener -> Mail`
