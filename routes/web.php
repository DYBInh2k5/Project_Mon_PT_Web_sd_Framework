<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\RoleDemoController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->middleware('role:admin');
    Route::resource('product-categories', ProductCategoryController::class)
        ->middleware('role:editor,admin');
    Route::resource('products', ProductController::class)
        ->middleware('role:editor,admin');

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
    Route::get('/role-demo/viewer', [RoleDemoController::class, 'user'])
        ->middleware('role:user,editor,admin');
});

require __DIR__.'/auth.php';
