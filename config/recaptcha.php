<?php

return [
    'site_key' => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    'version' => env('RECAPTCHA_VERSION', 'v2'), // v2, v3, or enterprise
    'v3_minimum_score' => env('RECAPTCHA_V3_MIN_SCORE', 0.5),
];