<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleDemoController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/check_fail', function () {
    echo 'checkfail page';

    return view('home.check-age-demo');
});

Route::get('/check_age/{age?}', function (?string $age = null) {
    echo $age;

    return view('home.check-age-demo');
})->middleware(\App\Http\Middleware\CheckAge::class);

Route::middleware(['auth'])->group(function () {
    // Toan bo route trong nhom nay deu yeu cau user phai dang nhap truoc.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    // Chi admin moi duoc doi status user.
    Route::patch('users/{user}/status', [UserController::class, 'toggleStatus'])
        ->middleware('role:admin')
        ->name('users.toggle-status');

    // Resource users gom cac route:
    // index, create, store, show, edit, update, destroy
    // Tat ca deu chi danh cho admin.
    Route::resource('users', UserController::class)->middleware('role:admin');

    // editor hoac admin duoc quan ly danh muc va san pham.
    Route::resource('product-categories', ProductCategoryController::class)
        ->middleware('role:editor,admin');
    Route::resource('products', ProductController::class)
        ->middleware('role:editor,admin');

    // Nhom route demo de kiem tra middleware role theo tung muc quyen.
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

    // Route cu viewer duoc giu lai de tranh gay loi neu da tung truy cap bang URL cu.
    Route::get('/role-demo/viewer', [RoleDemoController::class, 'user'])
        ->middleware('role:user,editor,admin');
});

require __DIR__.'/auth.php';
