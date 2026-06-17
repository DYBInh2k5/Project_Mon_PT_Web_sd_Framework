<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * Trả về Blade view tương ứng làm khung giao diện (Layout) cho khách vãng lai chưa đăng nhập.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
