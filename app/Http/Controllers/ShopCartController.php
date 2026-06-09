<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ShoppingCartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopCartController extends Controller
{
    public function index(ShoppingCartService $cart): View
    {
        // View giỏ hàng luôn đọc lại dữ liệu đã được chuẩn hoá từ session.
        $items = $this->resolveItems($cart);

        return view('shop.cart', [
            'title' => 'Your Cart',
            'items' => $items,
            'subtotal' => collect($items)->sum(fn (array $item) => $item['line_total']),
            'count' => $cart->countItems(),
        ]);
    }

    public function store(Request $request, Product $product, ShoppingCartService $cart): RedirectResponse
    {
        if (! $product->is_active) {
            return back()->withErrors(['product' => 'Sản phẩm này đang tạm ngừng bán.']);
        }

        // Validate số lượng trước khi đưa vào session cart.
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.$product->stock],
        ]);

        $quantity = (int) ($validated['quantity'] ?? 1);
        $cart->add($product, $quantity);

        return back()->with('success', "{$product->name} đã được thêm vào giỏ hàng.");
    }

    public function update(Request $request, Product $product, ShoppingCartService $cart): RedirectResponse
    {
        // Cho phép cập nhật số lượng từng item, nếu quantity = 0 thì coi như xoá khỏi giỏ.
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:'.$product->stock],
        ]);

        $cart->update($product, (int) $validated['quantity']);

        return back()->with('success', 'Đã cập nhật số lượng sản phẩm trong giỏ hàng.');
    }

    public function destroy(Product $product, ShoppingCartService $cart): RedirectResponse
    {
        $cart->remove($product);

        return back()->with('success', 'Đã xoá sản phẩm khỏi giỏ hàng.');
    }

    public function clear(ShoppingCartService $cart): RedirectResponse
    {
        $cart->clear();

        return redirect()->route('shop.index')->with('success', 'Giỏ hàng đã được xoá.');
    }

    private function resolveItems(ShoppingCartService $cart): array
    {
        // Sản phẩm trong session có thể đã bị xoá hoặc tắt bán nên phải đọc lại từ database.
        $items = [];
        $products = Product::query()
            ->with('category')
            ->whereIn('id', $cart->productIds())
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        foreach ($cart->all() as $productId => $row) {
            $product = $products->get((int) $productId);

            if (! $product) {
                continue;
            }

            $quantity = min((int) ($row['quantity'] ?? 1), max(1, (int) $product->stock));

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'line_total' => round(((float) $product->price) * $quantity),
            ];
        }

        return $items;
    }
}
