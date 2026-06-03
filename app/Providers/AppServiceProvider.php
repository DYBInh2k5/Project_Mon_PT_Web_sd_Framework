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
     * Register any application services.
     */
    public function register(): void
    {
        $filesystem = new SafeFilesystem();

        $this->app->instance('files', $filesystem);
        $this->app->instance(Filesystem::class, $filesystem);
        $this->app->alias('files', Filesystem::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Blade::component('package-alert', Alert::class);
    }
}
