<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleDemoController extends Controller
{
    public function index(Request $request): View
    {
        // Trang tong hop de mo nhanh cac route test middleware role.
        // Day la noi de demo voi co cach phan quyen dang hoat dong.
        return view('demos.role-index', [
            'title' => 'Role Demo',
            'user' => $request->user(),
        ]);
    }

    public function admin(Request $request): View
    {
        // Route mau chi cho admin.
        return $this->renderAccessPage($request, 'Admin Area', 'admin');
    }

    public function editor(Request $request): View
    {
        // Route mau cho editor va admin.
         return $this->renderAccessPage($request, 'Editor Area', 'editor');
    }

    public function user(Request $request): View
    {
        // Route mau cho user, editor va admin.
        return $this->renderAccessPage($request, 'User Area', 'user');
    }

    private function renderAccessPage(Request $request, string $title, string $requiredRole): View
    {
        // Ham dung chung de tranh lap code giua admin / editor / user.
        // Neu request vao duoc day thi co nghia la middleware da kiem tra xong
        // va cho phep di tiep.
        return view('demos.role-access', [
            'title' => $title,
            'user' => $request->user(),
            'requiredRole' => $requiredRole,
        ]);
    }
}
