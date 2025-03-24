<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Advocate\AdvocateCase;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function password_reset($id, Request $request)
    {
        return AuthService::password_reset($id, $request);
    }
    public function logout(Request $request)
    {
        return AuthService::logout($request);
    }

    public function convert_client_case() {
        $data = AdvocateCase::all()->pluck('client_id', 'id');

        $sql = "UPDATE advocate_cases SET clients = CASE id ";
        foreach ($data as $id => $client_id) {
            if ($client_id !== null) {
                $client_id = $client_id . '';
//                $serialized = ($client_id !== null) ? serialize([$client_id]) : null;
                $serialized = ($client_id !== null) ? '"' . str_replace('"', '\"', serialize([$client_id])) . '"' : null;
                $sql .= "WHEN $id THEN '" . addslashes($serialized) . "' ";
            }
        }
        $sql .= "END WHERE id IN (" . implode(',', array_keys($data->toArray())) . ")";

        DB::statement($sql);
    }
}
