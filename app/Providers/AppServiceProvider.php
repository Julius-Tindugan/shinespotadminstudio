<?php

namespace App\Providers;

use App\Services\KpiService;
use App\Models\Booking;
use App\Models\BookingObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(KpiService::class, function ($app) {
            return new KpiService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the BookingObserver to auto-update booking status
        Booking::observe(BookingObserver::class);
        
        // Force HTTPS in production (fixes mixed content issues on Hostinger)
        if (config('app.env') === 'production' || request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme('https');
        }
    }
}
