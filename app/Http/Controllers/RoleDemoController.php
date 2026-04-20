<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleDemoController extends Controller
{
    public function index(Request $request): View
    {
        return view('demos.role-index', [
            'title' => 'Role Demo',
            'user' => $request->user(),
        ]);
    }

    public function admin(Request $request): View
    {
        return $this->renderAccessPage($request, 'Admin Area', 'admin');
    }

    public function editor(Request $request): View
    {
        return $this->renderAccessPage($request, 'Editor Area', 'editor');
    }

    public function user(Request $request): View
    {
        return $this->renderAccessPage($request, 'User Area', 'user');
    }

    private function renderAccessPage(Request $request, string $title, string $requiredRole): View
    {
        return view('demos.role-access', [
            'title' => $title,
            'user' => $request->user(),
            'requiredRole' => $requiredRole,
        ]);
    }
}
