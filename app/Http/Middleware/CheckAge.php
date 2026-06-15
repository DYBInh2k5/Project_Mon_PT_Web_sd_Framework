<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAge
{
    /**
     * Xử lý request đi qua middleware kiểm tra độ tuổi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Lấy giá trị tuổi từ tham số của route (ví dụ: /check_age/20) hoặc từ input request.
        // Nếu không có, mặc định tuổi bằng 0.
        $age = (int) $request->route('age', $request->input('age', 0));

        // Kiểm tra điều kiện: Nếu độ tuổi từ 200 trở lên (tuổi phi thực tế),
        // hệ thống sẽ chuyển hướng người dùng đến trang thông báo lỗi kiểm tra độ tuổi (/check_fail).
        if ($age >= 200) {
            return redirect('/check_fail');
        }

        // Nếu hợp lệ, cho phép request đi tiếp qua middleware này.
        return $next($request);
    }
}
