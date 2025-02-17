<?php

namespace App\Services\Advocates;

use App\Models\Auth\User;
use App\Services\Service;
use Illuminate\Http\Request;

class AdvocatesService extends Service
{
    public static function index(Request $request) {
        $query = User::where('role_id', 4);
        $input = $request->all();
        if (count($input) > 0) {
            $query = self::sorting($input, $query);
            $query = self::pagination($input, $query);
        }
        $users = $query->get();
        $count = User::where('role_id', 4)->get()->count();
        return self::sendResponse($users->toArray(), 'Advocates returned successfully', $count);
    }

    public static function show($id, Request $request) {
        $user = User::find($id);
        return self::sendResponse($user, 'Advocate "' . $user->full_name . '" returned successfully');
    }

    public static function update(Request $request, User $advocate) {
        return self::sendResponse(null, 'Test');
    }
}
