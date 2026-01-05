<?php

namespace App\Providers;

use App\Services\YellikaSmsService;
use App\Services\OrangeSmsService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OrangeSmsService::class, function ($app) {
            return new OrangeSmsService();
        });

        $this->app->singleton(YellikaSmsService::class, function ($app) {
            return new YellikaSmsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
