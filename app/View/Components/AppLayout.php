<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Trả về Blade view tương ứng làm khung giao diện chính (Layout) cho người dùng đã đăng nhập.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
