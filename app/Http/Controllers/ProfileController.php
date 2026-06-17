<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang chỉnh sửa thông tin cá nhân của người dùng.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Cập nhật thông tin tài khoản người dùng (tên, email).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Gán các thuộc tính đã validate vào model user
        $request->user()->fill($request->validated());

        // Nếu người dùng thay đổi email, đánh dấu trạng thái xác thực email là chưa xác thực (null)
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Xóa hoàn toàn tài khoản người dùng khỏi hệ thống.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Xác thực mật khẩu hiện tại của người dùng trước khi đồng ý xóa tài khoản
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Đăng xuất tài khoản
        Auth::logout();

        // Xóa bản ghi user trong database
        $user->delete();

        // Vô hiệu hóa session hiện tại và làm mới CSRF token để bảo mật
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
