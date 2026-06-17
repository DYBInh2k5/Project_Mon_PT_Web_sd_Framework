<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Hiển thị giao diện đăng ký tài khoản mới (Sign Up).
     */
    public function create(): View
    {
        return view('pages.auth.signup');
    }

    /**
     * Xử lý yêu cầu đăng ký tài khoản mới của người dùng.
     */
    public function store(Request $request): RedirectResponse
    {
        // Kiểm tra dữ liệu đầu vào:
        // - Tên: bắt buộc, là chuỗi, tối đa 255 ký tự.
        // - Email: bắt buộc, đúng định dạng, chữ thường, tối đa 255 ký tự, không trùng lặp trong bảng users.
        // - Mật khẩu: bắt buộc, phải khớp với trường nhập lại mật khẩu (password_confirmation) và thỏa mãn các ràng buộc mặc định của Laravel.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Mã hóa mật khẩu bằng thuật toán Bcrypt (Hash) trước khi lưu vào database để đảm bảo an toàn thông tin
        $validated['password'] = Hash::make($validated['password']);

        // Tạo bản ghi người dùng mới và kích hoạt sự kiện Registered để Laravel xử lý các logic phụ (như gửi mail xác thực nếu có)
        event(new Registered(($user = User::create($validated))));

        // Tự động đăng nhập tài khoản vừa tạo thành công
        Auth::login($user);

        // Chuyển hướng người dùng về trang dashboard chính
        return redirect(route('dashboard', absolute: false));
    }
}

