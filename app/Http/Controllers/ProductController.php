<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $baseQuery = Product::query();

        $products = Product::with('category')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('product_category_id', $request->integer('category'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->string('status')->toString() === 'active');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'title' => 'Products',
            'products' => $products,
            'categories' => ProductCategory::orderBy('name')->get(),
            'filters' => $request->only(['search', 'category', 'status']),
            'summary' => [
                'total' => (clone $baseQuery)->count(),
                'active' => (clone $baseQuery)->where('is_active', true)->count(),
                'lowStock' => (clone $baseQuery)->where('stock', '<=', 10)->count(),
                'inventoryValue' => (float) (clone $baseQuery)->selectRaw('COALESCE(SUM(price * stock), 0) as value')->value('value'),
            ],
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'title' => 'Create Product',
            'categories' => ProductCategory::orderBy('name')->get(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        Product::create([
            ...$data,
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'title' => 'Edit Product',
            'product' => $product,
            'categories' => ProductCategory::orderBy('name')->get(),
        ]);
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'creator']);

        return view('products.show', [
            'title' => 'Product Detail',
            'product' => $product,
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
