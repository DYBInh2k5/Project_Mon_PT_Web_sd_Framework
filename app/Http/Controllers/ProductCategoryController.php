<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    /**
     * Hiển thị danh sách các danh mục sản phẩm.
     */
    public function index(Request $request): View
    {
        // Khởi tạo query gốc để tính toán các chỉ số tổng quan.
        $baseQuery = ProductCategory::query();

        // Lấy danh sách danh mục có kèm thông tin người tạo và đếm số sản phẩm thuộc mỗi danh mục.
        $categories = ProductCategory::with(['creator'])
            ->withCount('products')
            // Hỗ trợ tìm kiếm theo tên hoặc slug danh mục.
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            // Lọc theo trạng thái Kích hoạt / Tạm ẩn.
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->string('status')->toString() === 'active');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('product-categories.index', [
            'title' => 'Danh mục sản phẩm',
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

    /**
     * Hiển thị form tạo mới danh mục sản phẩm.
     */
    public function create(): View
    {
        return view('product-categories.create', [
            'title' => 'Tạo danh mục sản phẩm',
        ]);
    }

    /**
     * Lưu danh mục sản phẩm mới vào cơ sở dữ liệu.
     */
    public function store(ProductCategoryRequest $request): RedirectResponse
    {
        // Lưu thông tin danh mục với thông tin người tạo (created_by) lấy từ tài khoản đang đăng nhập.
        ProductCategory::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Tạo danh mục sản phẩm thành công.');
    }

    /**
     * Hiển thị form chỉnh sửa danh mục sản phẩm.
     */
    public function edit(ProductCategory $productCategory): View
    {
        return view('product-categories.edit', [
            'title' => 'Chỉnh sửa danh mục sản phẩm',
            'category' => $productCategory,
        ]);
    }

    /**
     * Hiển thị thông tin chi tiết danh mục kèm theo danh sách sản phẩm thuộc danh mục đó.
     */
    public function show(ProductCategory $productCategory): View
    {
        // Eager load người tạo danh mục và các sản phẩm thuộc danh mục này cùng người tạo sản phẩm tương ứng.
        $productCategory->load(['creator', 'products.creator']);

        return view('product-categories.show', [
            'title' => 'Chi tiết danh mục sản phẩm',
            'category' => $productCategory,
        ]);
    }

    /**
     * Cập nhật thông tin danh mục sản phẩm trong database.
     */
    public function update(ProductCategoryRequest $request, ProductCategory $productCategory): RedirectResponse
    {
        $productCategory->update($request->validated());

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Cập nhật danh mục sản phẩm thành công.');
    }

    /**
     * Xóa danh mục sản phẩm khỏi database.
     */
    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        $productCategory->delete();

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Xóa danh mục sản phẩm thành công.');
    }
}
