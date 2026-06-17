<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    /**
     * Hiển thị thông báo yêu cầu xác thực email cho người dùng chưa xác thực.
     * Nếu đã xác thực rồi thì chuyển hướng về trang dashboard.
     */
    public function notice(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('pages.auth.verify-email');
    }

    /**
     * Gửi lại email chứa liên kết xác thực tài khoản.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Gửi email chứa thông tin xác thực
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    /**
     * Xử lý xác thực tài khoản khi người dùng click vào liên kết được gửi qua email.
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        // Kiểm tra xem email đã được xác thực trước đó chưa
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        // Đánh dấu email đã được xác thực trong database
        if ($request->user()->markEmailAsVerified()) {
            /** @var \Illuminate\Contracts\Auth\MustVerifyEmail $user */
            $user = $request->user();

            // Phát sự kiện Verified để Laravel chạy các logic phụ liên quan
            event(new Verified($user));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}

