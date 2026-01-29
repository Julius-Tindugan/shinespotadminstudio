<?php

namespace App\Providers;

use App\Http\Middleware\AdminAuth;
use App\Models\Backdrop;
use App\Models\Equipment;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Explicit route model bindings with custom primary keys
        Route::bind('backdrop', function ($value) {
            return Backdrop::where('backdrop_id', $value)->firstOrFail();
        });

        Route::bind('equipment', function ($value) {
            return Equipment::where('equipment_id', $value)->firstOrFail();
        });

        // Define routes
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
