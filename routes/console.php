<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Định nghĩa lệnh Artisan console tuỳ biến trong hệ thống.
// Bạn có thể chạy lệnh này bằng cách gõ: php artisan inspire
Artisan::command('inspire', function () {
    // Trả về câu châm ngôn truyền cảm hứng ngẫu nhiên trong CLI.
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
