<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleDemoController extends Controller
{
    public function index(Request $request): View
    {
        // Trang tổng hợp để mở nhanh các route test middleware role.
        // Đây là nơi để demo cho thấy cơ chế phân quyền đang hoạt động như thế nào.
        return view('demos.role-index', [
            'title' => 'Role Demo',
            'user' => $request->user(),
        ]);
    }

    public function admin(Request $request): View
    {
        // Route mẫu chỉ dành riêng cho quản trị viên (admin).
        return $this->renderAccessPage($request, 'Admin Area', 'admin');
    }

    public function editor(Request $request): View
    {
        // Route mẫu dành cho biên tập viên (editor) và quản trị viên (admin).
         return $this->renderAccessPage($request, 'Editor Area', 'editor');
    }

    public function user(Request $request): View
    {
        // Route mẫu dành cho người dùng thông thường (user), editor và admin.
        return $this->renderAccessPage($request, 'User Area', 'user');
    }

    private function renderAccessPage(Request $request, string $title, string $requiredRole): View
    {
        // Hàm dùng chung để tránh lặp code hiển thị giữa các trang demo quyền.
        // Nếu request vào được đến đây, có nghĩa là middleware EnsureUserHasRole đã kiểm tra phân quyền thành công
        // và cho phép đi tiếp.
        return view('demos.role-access', [
            'title' => $title,
            'user' => $request->user(),
            'requiredRole' => $requiredRole,
        ]);
    }
}
