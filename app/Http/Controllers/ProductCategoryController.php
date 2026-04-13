<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $baseQuery = ProductCategory::query();

        $categories = ProductCategory::with(['creator'])
            ->withCount('products')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->string('status')->toString() === 'active');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('product-categories.index', [
            'title' => 'Product Categories',
            'categories' => $categories,
            'filters' => $request->only(['search', 'status']),
            'summary' => [
                'total' => (clone $baseQuery)->count(),
                'active' => (clone $baseQuery)->where('is_active', true)->count(),
                'hidden' => (clone $baseQuery)->where('is_active', false)->count(),
                'withProducts' => (clone $baseQuery)->has('products')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('product-categories.create', [
            'title' => 'Create Product Category',
        ]);
    }

    public function store(ProductCategoryRequest $request): RedirectResponse
    {
        ProductCategory::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Product category created successfully.');
    }

    public function edit(ProductCategory $productCategory): View
    {
        return view('product-categories.edit', [
            'title' => 'Edit Product Category',
            'category' => $productCategory,
        ]);
    }

    public function show(ProductCategory $productCategory): View
    {
        $productCategory->load(['creator', 'products.creator']);

        return view('product-categories.show', [
            'title' => 'Product Category Detail',
            'category' => $productCategory,
        ]);
    }

    public function update(ProductCategoryRequest $request, ProductCategory $productCategory): RedirectResponse
    {
        $productCategory->update($request->validated());

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Product category updated successfully.');
    }

    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        $productCategory->delete();

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Product category deleted successfully.');
    }
}
