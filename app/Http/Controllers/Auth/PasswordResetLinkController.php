<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Hiển thị giao diện yêu cầu khôi phục mật khẩu (Forgot Password).
     */
    public function create(): View
    {
        return view('pages.auth.forgot-password');
    }

    /**
     * Xử lý gửi liên kết đặt lại mật khẩu đến email người dùng.
     */
    public function store(Request $request): RedirectResponse
    {
        // Kiểm tra định dạng email nhập vào
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Sử dụng Password broker mặc định của Laravel để tạo token khôi phục mật khẩu và gửi email.
        // Laravel sẽ tự động gửi email nếu tài khoản này tồn tại trong cơ sở dữ liệu.
        Password::sendResetLink($request->only('email'));

        // Trả về trang trước kèm theo trạng thái thông báo thành công
        return back()->with('status', __('A reset link will be sent if the account exists.'));
    }
}

