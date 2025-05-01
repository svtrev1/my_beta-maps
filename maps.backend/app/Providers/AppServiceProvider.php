<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // Регистрируем rate-limiter "api", чтобы middleware throttle:api его находил:
        RateLimiter::for('api', function (Request $request) {
            // 60 запросов в минуту на пользователя (или по IP, если не авторизован)
            $key = optional($request->user())->id ?: $request->ip();
            return Limit::perMinute(60)->by($key);
        });
    }
}
