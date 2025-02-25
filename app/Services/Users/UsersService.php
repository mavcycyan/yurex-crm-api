<?php

namespace App\Services\Users;

use App\Models\Auth\User;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersService extends Service
{
    public static function index(Request $request) {
        $query = User::select('*');
        $input = $request->all();
        if (count($input) > 0) {
            $query = self::sorting($input, $query);
            $query = self::pagination($input, $query);
        }
        $users = $query->get();
        $count = User::select('*')->get()->count();
        return self::sendResponse($users->toArray(), 'Users returned successfully', $count);
    }

    public static function show($id, Request $request) {
        $user = User::find($id);
        return self::sendResponse($user, 'User "' . $user->full_name . '" returned successfully');
    }

    public static function update(Request $request, $id) {
        $input = $request->all();
        $user = User::find($id);

        $validator = Validator::make($input, [
            'email' => 'required',
        ]);

        if($validator->fails()){
            return self::sendError('Validation Error.', $validator->errors());
        }

        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->birthday = $input['birthday'];
        $user->address = $input['address'];
        $user->phone = $input['phone'];
        $user->role_id = $input['role_id'];

        $user->save();

        return self::sendResponse($user, 'User "' . $user->name . '" updated successfully.');
    }

    public static function destroy($id) {
        $user = User::find($id);

        if (!$user) {
            return self::sendError('User case not found.');
        }
        $user->delete();

        return response()->json(['success' => true,'message' => 'User deleted successfully.'], 200);
    }
}
