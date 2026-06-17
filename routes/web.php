<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopCartController;
use App\Http\Controllers\ShopCheckoutController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleDemoController;
use App\Http\Controllers\SupportChatController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Các route công cộng (Public) của hệ thống cửa hàng
Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/products/{product}', [ShopController::class, 'show'])->name('shop.products.show');

// Các route quản lý giỏ hàng (Shopping Cart) sử dụng Session
Route::get('/cart', [ShopCartController::class, 'index'])->name('shop.cart.index');
Route::post('/cart/items/{product}', [ShopCartController::class, 'store'])->name('shop.cart.store');
Route::patch('/cart/items/{product}', [ShopCartController::class, 'update'])->name('shop.cart.update');
Route::delete('/cart/items/{product}', [ShopCartController::class, 'destroy'])->name('shop.cart.destroy');
Route::delete('/cart', [ShopCartController::class, 'clear'])->name('shop.cart.clear');

// Các route thực hiện quy trình Thanh toán và Đối soát qua cổng VNPay
Route::get('/checkout', [ShopCheckoutController::class, 'create'])->name('shop.checkout.create');
Route::post('/checkout', [ShopCheckoutController::class, 'store'])->name('shop.checkout.store');
Route::get('/checkout/vnpay/return', [ShopCheckoutController::class, 'vnpayReturn'])->name('shop.checkout.return');
Route::get('/checkout/vnpay/ipn', [ShopCheckoutController::class, 'ipn'])->name('shop.checkout.ipn');

Route::get('/check_fail', function () {
    echo 'checkfail page';

    return view('home.check-age-demo');
});

Route::get('/check_age/{age?}', function (?string $age = null) {
    echo $age;

    return view('home.check-age-demo');
})->middleware(\App\Http\Middleware\CheckAge::class);

Route::resource('articles', ArticleController::class);

Route::middleware(['auth'])->group(function () {
    // Toàn bộ route trong nhóm này đều yêu cầu người dùng phải đăng nhập trước.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    // Chỉ tài khoản quản trị viên (admin) mới được thay đổi trạng thái (toggle hoạt động) của người dùng khác.
    Route::patch('users/{user}/status', [UserController::class, 'toggleStatus'])
        ->middleware('role:admin')
        ->name('users.toggle-status');

    // Resource users quản lý người dùng: index, create, store, show, edit, update, destroy.
    // Chỉ cho phép admin truy cập.
    Route::resource('users', UserController::class)->middleware('role:admin');

    // Cả Editor và Admin đều được phép quản lý danh mục sản phẩm, sản phẩm và đơn hàng.
    Route::resource('product-categories', ProductCategoryController::class)
        ->middleware('role:editor,admin');
    Route::resource('products', ProductController::class)
        ->middleware('role:editor,admin');
    Route::get('orders', [OrderController::class, 'index'])
        ->middleware('role:editor,admin')
        ->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])
        ->middleware('role:editor,admin')
        ->name('orders.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->middleware('role:editor,admin')
        ->name('orders.update-status');

    // Chatbot hỗ trợ khách hàng được mở cho tất cả người dùng (user, editor, admin).
    Route::get('support-chat', [SupportChatController::class, 'index'])
        ->middleware('role:user,editor,admin')
        ->name('support-chat.index');
    Route::post('support-chat', ChatController::class)
        ->middleware('role:user,editor,admin')
        ->name('support-chat.store');
    Route::post('chat/send', ChatController::class)
        ->middleware('role:user,editor,admin')
        ->name('chat.send');
    Route::post('support-chat/clear', [SupportChatController::class, 'clear'])
        ->middleware('role:user,editor,admin')
        ->name('support-chat.clear');

    // Nhóm các route demo phục vụ test hoạt động của middleware EnsureUserHasRole theo từng mức quyền hạn.
    Route::get('/role-demo', [RoleDemoController::class, 'index'])->name('role-demo.index');
    Route::get('/role-demo/admin', [RoleDemoController::class, 'admin'])
        ->middleware('role:admin')
        ->name('role-demo.admin');
    Route::get('/role-demo/editor', [RoleDemoController::class, 'editor'])
        ->middleware('role:editor,admin')
        ->name('role-demo.editor');
    Route::get('/role-demo/user', [RoleDemoController::class, 'user'])
        ->middleware('role:user,editor,admin')
        ->name('role-demo.user');

    // Tương thích ngược: route viewer cũ trỏ vào trang demo user
    Route::get('/role-demo/viewer', [RoleDemoController::class, 'user'])
        ->middleware('role:user,editor,admin');
});

require __DIR__.'/auth.php';
