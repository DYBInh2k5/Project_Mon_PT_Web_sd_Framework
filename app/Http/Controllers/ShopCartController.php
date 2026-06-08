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
            return back()->withErrors(['product' => 'San pham nay dang tam ngung ban.']);
        }

        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.$product->stock],
        ]);

        $quantity = (int) ($validated['quantity'] ?? 1);
        $cart->add($product, $quantity);

        return back()->with('success', "{$product->name} da duoc them vao gio hang.");
    }

    public function update(Request $request, Product $product, ShoppingCartService $cart): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:'.$product->stock],
        ]);

        $cart->update($product, (int) $validated['quantity']);

        return back()->with('success', 'Da cap nhat so luong san pham trong gio hang.');
    }

    public function destroy(Product $product, ShoppingCartService $cart): RedirectResponse
    {
        $cart->remove($product);

        return back()->with('success', 'Da xoa san pham khoi gio hang.');
    }

    public function clear(ShoppingCartService $cart): RedirectResponse
    {
        $cart->clear();

        return redirect()->route('shop.index')->with('success', 'Gio hang da duoc xoa.');
    }

    private function resolveItems(ShoppingCartService $cart): array
    {
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
