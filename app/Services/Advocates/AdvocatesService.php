<?php

namespace App\Services\Advocates;

use App\Models\Auth\User;
use App\Services\Service;
use Illuminate\Http\Request;

class AdvocatesService extends Service
{
    public static function index(Request $request) {
        $users = User::where('role_id', 4)
            ->get();
        return self::sendResponse($users->toArray(), 'Advocates returned successfully');
    }

    public static function show($id, Request $request) {
        $user = User::find($id);
        return self::sendResponse($user, 'Advocate "' . $user->full_name . '" returned successfully');
    }

    public static function update(Request $request, User $advocate) {
        return self::sendResponse(null, 'Test');
    }
}
