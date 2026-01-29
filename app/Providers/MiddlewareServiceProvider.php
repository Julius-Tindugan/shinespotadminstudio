<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use App\Http\Middleware\AdminAuthMiddleware;
use App\Http\Middleware\LoginRateLimiter;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\FinanceAccessMiddleware;
use App\Http\Middleware\SettingsAccessMiddleware;
use App\Http\Middleware\LogActivity;
use App\Http\Middleware\AdminOnlyMiddleware;
use App\Http\Middleware\ManagementAccessMiddleware;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register the middleware for routes
        $router = $this->app['router'];
        
        // Register admin auth middleware
        $router->aliasMiddleware('admin.auth', AdminAuthMiddleware::class);
        
        // Register role middleware for authorization
        $router->aliasMiddleware('role', RoleMiddleware::class);
        
        // Register login rate limiter middleware
        $router->aliasMiddleware('login.limit', LoginRateLimiter::class);
        
        // Register finance access middleware
        $router->aliasMiddleware('finance.access', FinanceAccessMiddleware::class);
        
        // Register settings access middleware
        $router->aliasMiddleware('settings.access', SettingsAccessMiddleware::class);
        
        // Register activity logging middleware
        $router->aliasMiddleware('log.activity', LogActivity::class);
        
        // Register admin-only middleware
        $router->aliasMiddleware('admin.only', AdminOnlyMiddleware::class);
        
        // Register management access middleware (for admin and staff)
        $router->aliasMiddleware('management.access', ManagementAccessMiddleware::class);
    }
}
