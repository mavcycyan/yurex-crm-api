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
        'api/clients',
        'api/clients/*',
        'api/client_works',
        'api/client_works/*',
        'api/subscribers',
        'api/subscribers/*',
        'api/subscriber_works',
        'api/subscriber_works/*',
        'api/advocates',
        'api/advocates/*',
        'api/advocates_categories',
        'api/advocates_categories/*',
        'api/advocates_cases',
        'api/advocates_cases/*',
        'api/advocates_works',
        'api/advocates_works/*',
        'api/register',
        'api/login',
    ];
}
