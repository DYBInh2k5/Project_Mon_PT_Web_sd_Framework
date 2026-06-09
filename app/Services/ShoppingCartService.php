<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Arr;

class ShoppingCartService
{
    private const SESSION_KEY = 'shop.cart';

    public function all(): array
    {
        // Toàn bộ giỏ hàng được lưu trong session để không cần bảng riêng cho demo.
        return session()->get(self::SESSION_KEY, []);
    }

    public function countItems(): int
    {
        // Đếm tổng số lượng sản phẩm, không phải số dòng item.
        return array_sum(array_map(
            fn (array $item) => (int) ($item['quantity'] ?? 0),
            $this->all()
        ));
    }

    public function add(Product $product, int $quantity = 1): void
    {
        // Khi cùng sản phẩm được thêm nhiều lần thì cộng dồn số lượng.
        $items = $this->all();
        $id = (string) $product->id;
        $current = (int) Arr::get($items, "{$id}.quantity", 0);

        $items[$id] = [
            'quantity' => max(1, $current + max(1, $quantity)),
        ];

        session()->put(self::SESSION_KEY, $items);
    }

    public function update(Product $product, int $quantity): void
    {
        // quantity <= 0 thì coi như xoá item khỏi giỏ.
        $items = $this->all();
        $id = (string) $product->id;

        if ($quantity <= 0) {
            unset($items[$id]);
        } else {
            $items[$id] = [
                'quantity' => $quantity,
            ];
        }

        session()->put(self::SESSION_KEY, $items);
    }

    public function remove(Product $product): void
    {
        // Xoá đúng một sản phẩm khỏi session cart.
        $items = $this->all();
        unset($items[(string) $product->id]);

        session()->put(self::SESSION_KEY, $items);
    }

    public function clear(): void
    {
        // Làm rỗng toàn bộ giỏ hàng sau khi checkout xong.
        session()->forget(self::SESSION_KEY);
    }

    public function productIds(): array
    {
        // Lấy danh sách ID để query lại database trước khi checkout.
        return array_map('intval', array_keys($this->all()));
    }

    public function quantityFor(Product $product): int
    {
        // Dùng cho trang chi tiết sản phẩm để hiển thị số lượng đang có trong giỏ.
        return (int) Arr::get($this->all(), $product->id.'.quantity', 0);
    }
}
