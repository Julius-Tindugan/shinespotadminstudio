<?php

if (!function_exists('secure_asset')) {
    /**
     * Generate a secure (HTTPS) asset URL.
     * This helps prevent mixed content issues on production.
     *
     * @param string $path
     * @return string
     */
    function secure_asset($path)
    {
        return asset($path, true);
    }
}
