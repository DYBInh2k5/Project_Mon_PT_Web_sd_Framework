<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Middleware này được đặt ở giữa Route và Controller.
        // Nhiệm vụ của nó là kiểm tra xem người dùng đang đăng nhập có đúng vai trò (role)
        // mà route yêu cầu hay không.
        //
        // Ví dụ cấu hình trong route:
        // ->middleware('role:admin')
        // ->middleware('role:editor,admin')
        //
        // Khi đó tham số biến $roles (dạng splat operator string ...$roles) sẽ nhận được mảng chứa các role từ route truyền vào.

        // Lấy thông tin người dùng đang đăng nhập từ session/auth hiện tại.
        $user = $request->user();

        // Nếu chưa đăng nhập hoặc vai trò (role) của người dùng không nằm trong danh sách được cho phép
        // thì dừng request ngay tại đây và trả về mã lỗi HTTP 403 (Forbidden).
        // Khi bị chặn ở đây, Controller phía sau sẽ KHÔNG được chạy.
        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'You do not have permission to access this page.');
        }

        // Nếu vai trò hợp lệ, cho phép request đi tiếp vào middleware tiếp theo
        // hoặc chuyển trực tiếp vào Controller để xử lý logic.
        return $next($request);
    }
}
