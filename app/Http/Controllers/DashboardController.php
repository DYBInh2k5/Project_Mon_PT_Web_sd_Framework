<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang quản trị Dashboard tổng quan hệ thống.
     * Thu thập các số liệu thống kê về người dùng, danh mục sản phẩm và trạng thái sản phẩm để hiển thị biểu đồ và bảng dữ liệu.
     */
    public function index(): View
    {
        // Lấy 5 người dùng đăng ký mới nhất
        $recentUsers = User::latest()->take(5)->get();

        // Lấy 5 danh mục sản phẩm mới nhất kèm số lượng sản phẩm
        $recentCategories = ProductCategory::withCount('products')->latest()->take(5)->get();

        // Lấy 5 sản phẩm mới nhất cùng thông tin danh mục
        $recentProducts = Product::with('category')->latest()->take(5)->get();

        return view('dashboard', [
            'title' => 'Dashboard',
            'stats' => [
                'totalUsers' => User::count(), // Tổng số tài khoản
                'verifiedUsers' => User::whereNotNull('email_verified_at')->count(), // Tài khoản đã xác thực email
                'newUsersThisWeek' => User::where('created_at', '>=', now()->subDays(7))->count(), // Tài khoản đăng ký trong tuần qua
                'editorUsers' => User::where('role', 'editor')->count(), // Tài khoản thuộc vai trò Editor
                'totalCategories' => ProductCategory::count(), // Tổng số danh mục
                'activeCategories' => ProductCategory::where('is_active', true)->count(), // Danh mục đang hoạt động (hiển thị)
                'totalProducts' => Product::count(), // Tổng số sản phẩm
                'activeProducts' => Product::where('is_active', true)->count(), // Sản phẩm đang hoạt động (bán)
                'lowStockProducts' => Product::where('stock', '<=', 10)->count(), // Sản phẩm sắp hết hàng (tồn kho <= 10)
            ],
            'recentUsers' => $recentUsers,
            'recentCategories' => $recentCategories,
            'recentProducts' => $recentProducts,
        ]);
    }
}
