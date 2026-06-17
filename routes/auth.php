<?php

use App\Http\Controllers\Settings;
use App\Http\Controllers\Auth\ConfirmationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

// Các route chỉ cho phép khách vãng lai truy cập (chưa đăng nhập)
Route::middleware('guest')->group(function () {
    // Đăng ký tài khoản
    Route::get('register', [RegistrationController::class, 'create'])->name('register');
    Route::post('register', [RegistrationController::class, 'store']);

    // Đăng nhập hệ thống
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    // Yêu cầu và gửi link reset mật khẩu qua email
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    // Thiết lập mật khẩu mới sau khi nhấn liên kết từ email
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Các route yêu cầu người dùng đã đăng nhập (auth)
Route::middleware('auth')->group(function () {
    // Xác minh email (thông báo, gửi lại email và thực hiện xác minh)
    Route::get('verify-email', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::post('verify-email', [VerificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.store');
    Route::get('verify-email/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    // Xác nhận mật khẩu trước các thao tác nhạy cảm
    Route::get('confirm-password', [ConfirmationController::class, 'create'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmationController::class, 'store'])->name('confirmation.store');
});

// Nhóm route quản lý cấu hình cá nhân (Settings) yêu cầu đăng nhập
Route::middleware(['auth'])->group(function () {
    // Quản lý thông tin profile cá nhân (Sửa, cập nhật và xóa tài khoản)
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    
    // Đổi mật khẩu tài khoản
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
});

// Đăng xuất tài khoản
Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

