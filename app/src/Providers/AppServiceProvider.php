<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->useAppPath(base_path('src'));
    }

    public function boot(): void
    {
        //
    }
}
