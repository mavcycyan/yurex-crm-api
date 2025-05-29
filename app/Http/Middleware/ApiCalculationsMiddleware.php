<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiCalculationsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        if ($request->is('api/*')) {
//            app()->call([app()->make(\App\Http\Controllers\Controller::class), 'checkAndProcessSubscrCalc']);
//        }

        return $next($request);
    }


}
