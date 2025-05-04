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
        'api/client_works_list',
        'api/client_works_list/*',

        'api/lawyer_advocates_cases',
        'api/lawyer_advocates_categories',
        'api/lawyer_workers',

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
        'api/advocates_clients',
        'api/advocates_clients/*',

        'api/register',
        'api/login',
        'api/convert_client_case',

        'api/fin/clientworks/prices',
        'api/fin/clientworks/prices_bulk_update',

        'api/fin/subscribers/prices',
        'api/fin/subscribers/prices_bulk_update',

        'api/fin/subscriberworks/prices',
        'api/fin/subscriberworks/prices_bulk_update',

        'api/fin/advocatecases/prices',
        'api/fin/advocatecases/prices_bulk_update',

        'api/users',
        'api/users/*',

        'api/password_reset/*',

        'api/archive',

        'api/calculations/subscribers',
        'api/calculations/subscribers/*'
    ];
}
