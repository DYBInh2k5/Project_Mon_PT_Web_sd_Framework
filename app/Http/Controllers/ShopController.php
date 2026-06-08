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
        $categories = ProductCategory::query()
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('name')
            ->get();

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
                'products' => Product::where('is_active', true)->count(),
                'categories' => $categories->count(),
                'inStock' => Product::where('is_active', true)->where('stock', '>', 0)->count(),
            ],
        ]);
    }

    public function show(Product $product, ShoppingCartService $cart): View
    {
        abort_if(! $product->is_active, 404);

        $product->load('category');

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
