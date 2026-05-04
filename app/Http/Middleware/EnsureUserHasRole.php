<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Middleware nay duoc dat giua Route va Controller.
        // Nhiem vu cua no la kiem tra xem user dang dang nhap co dung role
        // ma route yeu cau hay khong.
        //
        // Vi du trong route:
        // ->middleware('role:admin')
        // ->middleware('role:editor,admin')
        //
        // Khi do bien $roles se nhan duoc mang role tu route truyen vao.

        // Lay user dang dang nhap tu session/auth hien tai.
        $user = $request->user();

        // Neu chua dang nhap hoac role cua user khong nam trong danh sach cho phep
        // thi dung request tai day va tra ve 403.
        // Khi bi chan o day thi controller phia sau se KHONG duoc chay.
        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'You do not have permission to access this page.');
        }

        // Neu role hop le thi cho request di tiep vao middleware tiep theo
        // hoac vao controller.
        return $next($request);
    }
}
