<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmationController extends Controller
{
    /**
     * Hiển thị giao diện yêu cầu xác nhận mật khẩu (Confirm Password) trước khi thực hiện hành động nhạy cảm.
     */
    public function create(): View
    {
        return view('pages.auth.confirm-password');
    }

    /**
     * Xác thực mật khẩu nhập vào của người dùng.
     */
    public function store(Request $request): RedirectResponse
    {
        // Kiểm tra xem mật khẩu nhập vào có trùng khớp với mật khẩu tài khoản hiện tại không
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        // Lưu thời gian xác nhận mật khẩu vào session để tạm thời không cần hỏi lại mật khẩu trong một khoảng thời gian
        $request->session()->put('auth.password_confirmed_at', time());

        // Chuyển hướng người dùng về trang trước hoặc dashboard chính
        return redirect()->intended(route('dashboard', absolute: false));
    }
}

