<?php

return [
    // Standard reCAPTCHA v2 configuration (fallback)
    'secret' => env('NOCAPTCHA_SECRET'),
    'sitekey' => env('NOCAPTCHA_SITEKEY'),
    'options' => [
        'timeout' => 30,
    ],
    
    // reCAPTCHA Enterprise configuration
    'enterprise' => [
        'sitekey' => env('RECAPTCHA_ENTERPRISE_SITEKEY', env('NOCAPTCHA_SITEKEY')),
        'project_id' => env('RECAPTCHA_ENTERPRISE_PROJECT_ID'),
        'minimum_score' => env('RECAPTCHA_ENTERPRISE_MIN_SCORE', 0.5),
        'enabled' => env('RECAPTCHA_ENTERPRISE_ENABLED', false),
    ],
];
