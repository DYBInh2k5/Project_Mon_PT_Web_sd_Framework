<?php

namespace App\Providers;

use App\Support\SafeFilesystem;
use App\View\Components\Alert;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Đăng ký các dịch vụ (Services) vào Service Container của hệ sinh thái Laravel.
     */
    public function register(): void
    {
        // Khởi tạo SafeFilesystem để thay thế lớp Filesystem mặc định của Laravel.
        // Giúp khắc phục triệt để lỗi phân quyền ghi file cache trên Windows.
        $filesystem = new SafeFilesystem();

        $this->app->instance('files', $filesystem);
        $this->app->instance(Filesystem::class, $filesystem);
        $this->app->alias('files', Filesystem::class);
    }

    /**
     * Chạy các xử lý khởi động (Bootstrap) sau khi các dịch vụ đã được đăng ký hoàn tất.
     */
    public function boot(): void
    {
        // Cấu hình độ dài chuỗi mặc định của các cột VARCHAR trong MySQL/SQLite là 191 ký tự (tránh lỗi khóa chính quá dài khi dùng UTF8MB4)
        Schema::defaultStringLength(191);

        // Đăng ký alias Blade component hiển thị Alert thông báo lỗi/thành công
        Blade::component('package-alert', Alert::class);
    }
}
