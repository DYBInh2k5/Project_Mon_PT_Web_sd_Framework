<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\ShoppingCartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        // Lấy danh mục để hiển thị ở sidebar và đếm số sản phẩm còn active trong từng danh mục.
        $categories = ProductCategory::query()
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('name')
            ->get();

        // Trang shop public chỉ hiển thị sản phẩm đang bán, có hỗ trợ search và lọc theo category.
        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('product_category_id', $request->integer('category'));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', [
            'categories' => $categories,
            'products' => $products,
            'filters' => $request->only(['search', 'category']),
            'summary' => [
                // Các số liệu này giúp header của shop có cảm giác như một cửa hàng thật.
                'products' => Product::where('is_active', true)->count(),
                'categories' => $categories->count(),
                'inStock' => Product::where('is_active', true)->where('stock', '>', 0)->count(),
            ],
        ]);
    }

    public function show(Product $product, ShoppingCartService $cart): View
    {
        // Chỉ cho xem chi tiết nếu sản phẩm đang active.
        abort_if(! $product->is_active, 404);

        // Load thêm category để view có thể hiển thị thông tin đầy đủ của sản phẩm.
        $product->load('category');

        // Gợi ý sản phẩm liên quan để trang chi tiết nhìn giống shop thật hơn.
        $relatedProducts = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->product_category_id, function ($query) use ($product) {
                $query->where('product_category_id', $product->product_category_id);
            })
            ->latest()
            ->limit(4)
            ->get();

        return view('shop.show', [
            'title' => $product->name,
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'cartCount' => $cart->countItems(),
            'quantityInCart' => $cart->quantityFor($product),
        ]);
    }
}
