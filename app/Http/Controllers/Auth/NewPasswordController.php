<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Hiển thị giao diện điền mật khẩu mới (Reset Password).
     * Yêu cầu chứa thông tin email và token xác thực được gửi qua URL.
     */
    public function create(Request $request): View
    {
        return view('pages.auth.reset-password', ['request' => $request]);
    }

    /**
     * Xử lý xác nhận đặt lại mật khẩu mới.
     */
    public function store(Request $request): RedirectResponse
    {
        // Kiểm tra dữ liệu đầu vào bao gồm email, mật khẩu mới khớp nhau và token xác thực hợp lệ
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Thực hiện đặt lại mật khẩu bằng Password Broker của Laravel
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                // Thay đổi mật khẩu mới (đã mã hóa) và làm mới remember_token
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Phát sự kiện PasswordReset để Laravel biết mật khẩu của người dùng đã thay đổi
                event(new PasswordReset($user));
            }
        );

        // Nếu đặt lại mật khẩu thành công, chuyển hướng người dùng về trang đăng nhập
        // Ngược lại, trả về lỗi tương ứng
        return $status == Password::PASSWORD_RESET
                    ? to_route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}

