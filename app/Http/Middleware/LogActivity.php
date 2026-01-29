<?php

namespace App\Http\Middleware;

use App\Services\ActivityLoggerService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    protected $activityLogger;

    public function __construct(ActivityLoggerService $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only log for authenticated users (admin or staff)
        if (Auth::guard('admin')->check() || Auth::guard('staff')->check()) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    /**
     * Log the activity based on the request.
     */
    private function logActivity(Request $request, $response)
    {
        $method = $request->method();
        $route = $request->route();
        
        if (!$route) {
            return;
        }

        $routeName = $route->getName();
        $uri = $request->path();

        // Skip logging for certain routes (to avoid noise)
        $skipRoutes = [
            'dashboard',
            'activity-logs.index', // Avoid recursive logging
            'logout',
        ];

        $skipPatterns = [
            '/api/',
            '/assets/',
            '/css/',
            '/js/',
            '/images/',
        ];

        // Check if we should skip this route
        foreach ($skipPatterns as $pattern) {
            if (strpos($uri, $pattern) !== false) {
                return;
            }
        }

        if (in_array($routeName, $skipRoutes)) {
            return;
        }

        // Only log significant actions
        $loggableActions = [
            'POST' => 'created',
            'PUT' => 'updated',
            'PATCH' => 'updated',
            'DELETE' => 'deleted',
        ];

        if (isset($loggableActions[$method])) {
            $action = $loggableActions[$method];
            $description = $this->generateDescription($method, $uri, $routeName);
            
            // Check if response was successful (2xx status codes)
            $statusCode = $response->getStatusCode();
            if ($statusCode >= 200 && $statusCode < 300) {
                $this->activityLogger->logAction($action, $description, [
                    'route' => $routeName,
                    'method' => $method,
                    'status_code' => $statusCode,
                ]);
            }
        }

        // Log specific GET actions that are important
        if ($method === 'GET') {
            $importantGetRoutes = [
                'bookings.show' => 'viewed booking details',
                'clients.show' => 'viewed client details',
                'finance.dashboard' => 'accessed finance dashboard',
                'reports.' => 'accessed report',
            ];

            foreach ($importantGetRoutes as $pattern => $description) {
                if (strpos($routeName, $pattern) !== false) {
                    $this->activityLogger->logAction('viewed', $description, [
                        'route' => $routeName,
                    ]);
                    break;
                }
            }
        }
    }

    /**
     * Generate a human-readable description for the activity.
     */
    private function generateDescription(string $method, string $uri, ?string $routeName): string
    {
        // Try to extract meaningful information from the route
        $segments = explode('/', trim($uri, '/'));
        
        if (count($segments) >= 1) {
            $resource = $segments[0];
            
            switch ($method) {
                case 'POST':
                    return "Created new " . str_replace('-', ' ', $resource);
                case 'PUT':
                case 'PATCH':
                    return "Updated " . str_replace('-', ' ', $resource);
                case 'DELETE':
                    return "Deleted " . str_replace('-', ' ', $resource);
            }
        }

        return ucfirst(strtolower($method)) . " action on " . $uri;
    }
}