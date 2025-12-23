<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add URIs that don't need CSRF protection
        // Example: 'webhook/*', 'api/*'
    ];
    
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;
    
    /**
     * The maximum age (in seconds) of the CSRF token before it expires.
     *
     * @var int|null
     */
    // protected $expires = 7200; // 2 hours (default)
}