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
        // '/midtrans-callback', // Untuk rute bersih
        // '/midtrans-callback/*', // Untuk rute dengan parameter atau query string
    ];
}
