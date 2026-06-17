<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Hiển thị giao diện đăng nhập (Sign In).
     */
    public function create(): View
    {
        return view('pages.auth.signin');
    }

    /**
     * Xử lý yêu cầu đăng nhập của người dùng.
     */
    public function store(Request $request): RedirectResponse
    {
        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Đảm bảo IP này chưa bị giới hạn tần suất đăng nhập sai (Rate Limiting)
        $this->ensureIsNotRateLimited($request);

        // Thử xác thực thông tin đăng nhập (email và mật khẩu)
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Tăng số lần đăng nhập sai của IP/email này lên 1
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Đăng nhập thành công, xóa lịch sử đếm lỗi đăng nhập
        RateLimiter::clear($this->throttleKey($request));

        // Tái tạo session để chống tấn công Session Fixation
        $request->session()->regenerate();

        // Chuyển hướng người dùng về trang trước đó họ muốn truy cập, hoặc trang dashboard mặc định
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Đăng xuất người dùng hiện tại khỏi hệ thống.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Thực hiện đăng xuất guard web
        Auth::guard('web')->logout();

        // Hủy bỏ session hiện tại
        $request->session()->invalidate();

        // Làm mới CSRF token
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Kiểm tra xem người dùng có đang bị giới hạn tần suất đăng nhập hay không.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        // Laravel cho phép tối đa 5 lần thử đăng nhập sai liên tiếp
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        // Phát sự kiện Lockout khi bị khóa tài khoản tạm thời
        event(new Lockout($request));

        // Lấy thời gian còn lại (tính bằng giây) trước khi được thử lại
        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Tạo khóa định danh duy nhất dựa trên Email và Địa chỉ IP của client để áp dụng Rate Limit.
     */
    public function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip());
    }
}

