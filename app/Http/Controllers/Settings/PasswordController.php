<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Hiển thị giao diện chỉnh sửa mật khẩu trong trang cài đặt.
     */
    public function edit(Request $request): View
    {
        return view('pages.auth.settings.password', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Cập nhật mật khẩu mới của người dùng.
     */
    public function update(Request $request): RedirectResponse
    {
        // Kiểm tra tính hợp lệ của dữ liệu đầu vào:
        // - current_password: bắt buộc, phải khớp với mật khẩu hiện tại trong DB.
        // - password: bắt buộc, phải có tính bảo mật cao (theo quy tắc mặc định) và khớp với password_confirmation.
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Rules\Password::defaults(), 'confirmed'],
        ]);

        // Cập nhật mật khẩu mới đã mã hóa vào cơ sở dữ liệu
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Quay lại trang cũ kèm trạng thái thông báo thành công
        return back()->with('status', 'password-updated');
    }
}
