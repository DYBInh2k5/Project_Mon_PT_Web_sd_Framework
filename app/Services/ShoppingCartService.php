<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Arr;

class ShoppingCartService
{
    private const SESSION_KEY = 'shop.cart';

    public function all(): array
    {
        // Toàn bộ giỏ hàng được lưu trong session để không cần bảng riêng trong database cho việc demo.
        return session()->get(self::SESSION_KEY, []);
    }

    public function countItems(): int
    {
        // Đếm tổng số lượng sản phẩm trong giỏ hàng (cộng dồn số lượng của từng loại sản phẩm).
        return array_sum(array_map(
            fn (array $item) => (int) ($item['quantity'] ?? 0),
            $this->all()
        ));
    }

    public function add(Product $product, int $quantity = 1): void
    {
        // Khi cùng một sản phẩm được thêm nhiều lần thì tiến hành cộng dồn số lượng.
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
        // Nếu số lượng truyền vào nhỏ hơn hoặc bằng 0 thì coi như xóa sản phẩm khỏi giỏ hàng.
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
        // Xóa một sản phẩm cụ thể khỏi giỏ hàng.
        $items = $this->all();
        unset($items[(string) $product->id]);

        session()->put(self::SESSION_KEY, $items);
    }

    public function clear(): void
    {
        // Xóa sạch giỏ hàng (thường dùng sau khi thanh toán thành công).
        session()->forget(self::SESSION_KEY);
    }

    public function productIds(): array
    {
        // Lấy danh sách ID của các sản phẩm trong giỏ hàng để truy vấn lại database lấy giá và số lượng tồn kho thực tế.
        return array_map('intval', array_keys($this->all()));
    }

    public function quantityFor(Product $product): int
    {
        // Lấy số lượng hiện tại của một sản phẩm trong giỏ hàng để hiển thị trên giao diện chi tiết sản phẩm.
        return (int) Arr::get($this->all(), $product->id.'.quantity', 0);
    }
}
