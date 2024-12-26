<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        return AuthService::register($request);
    }

    public function login(Request $request)
    {
        return AuthService::login($request);
    }

    public function logout(Request $request)
    {
        return AuthService::logout($request);
    }
}
