<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclude login routes and webhooks from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'login',
            'login/*',
            'webhooks/*',
            'api/*'
        ]);
        
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuthMiddleware::class,
            'admin.only' => \App\Http\Middleware\AdminOnlyMiddleware::class,
            'finance.access' => \App\Http\Middleware\FinanceAccessMiddleware::class,
            'log.activity' => \App\Http\Middleware\LogActivity::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'management.access' => \App\Http\Middleware\ManagementAccessMiddleware::class,
            'settings.access' => \App\Http\Middleware\SettingsAccessMiddleware::class,
            'login.limit' => \App\Http\Middleware\LoginRateLimiter::class,
            'password.reset.rate.limit' => \App\Http\Middleware\PasswordResetRateLimit::class,
            'registration.limit' => \App\Http\Middleware\RegistrationRateLimiter::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
