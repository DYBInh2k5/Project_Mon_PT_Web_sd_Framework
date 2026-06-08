<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Arr;

class ShoppingCartService
{
    private const SESSION_KEY = 'shop.cart';

    public function all(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    public function countItems(): int
    {
        return array_sum(array_map(
            fn (array $item) => (int) ($item['quantity'] ?? 0),
            $this->all()
        ));
    }

    public function add(Product $product, int $quantity = 1): void
    {
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
        $items = $this->all();
        unset($items[(string) $product->id]);

        session()->put(self::SESSION_KEY, $items);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function productIds(): array
    {
        return array_map('intval', array_keys($this->all()));
    }

    public function quantityFor(Product $product): int
    {
        return (int) Arr::get($this->all(), $product->id.'.quantity', 0);
    }
}
