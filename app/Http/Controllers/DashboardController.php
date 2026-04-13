<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $recentUsers = User::latest()->take(5)->get();
        $recentCategories = ProductCategory::withCount('products')->latest()->take(5)->get();
        $recentProducts = Product::with('category')->latest()->take(5)->get();

        return view('dashboard', [
            'title' => 'Dashboard',
            'stats' => [
                'totalUsers' => User::count(),
                'verifiedUsers' => User::whereNotNull('email_verified_at')->count(),
                'newUsersThisWeek' => User::where('created_at', '>=', now()->subDays(7))->count(),
                'editorUsers' => User::where('role', 'editor')->count(),
                'totalCategories' => ProductCategory::count(),
                'activeCategories' => ProductCategory::where('is_active', true)->count(),
                'totalProducts' => Product::count(),
                'activeProducts' => Product::where('is_active', true)->count(),
                'lowStockProducts' => Product::where('stock', '<=', 10)->count(),
            ],
            'recentUsers' => $recentUsers,
            'recentCategories' => $recentCategories,
            'recentProducts' => $recentProducts,
        ]);
    }
}
