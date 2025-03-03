<?php

namespace App\Services\Auth;

use App\Models\Auth\User;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthService extends Service
{

    public static function register(Request $request)
    {
        $input = $request->all();
        $validated = Validator::make($input, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4',
        ]);

        if ($validated->fails()) {
            return self::sendError('Validation Error.', $validated->errors());
        }
//        $user = User::create([
//            'name' => $validated['name'],
//            'email' => $validated['email'],
//            'password' => Hash::make($validated['password']),
//            'role_id' => $validated['role_id'],
//        ]);

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'birthday' => $input['birthday'] ?? null,
            'address' => $input['address'] ?? null,
            'phone' => $input['phone'] ?? null,
            'password' => Hash::make($input['password']),
            'role_id' => $input['role_id'],
        ]);

        return response()->json(['success' => true, 'data' => $user, 'message' => 'User registered successfully'], 201);
    }

    public static function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }

    public static function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public static function password_reset($id, Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'password' => 'required|min:4',
            'check_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Користувач не знайдений'], 404);
        }

        $user->password = Hash::make($input['password']);
        $user->save();

        $user->tokens()->delete();

        return response()->json(['success' => true, 'message' => 'Пароль успішно змінено, авторизацію скасовано'], 200);
    }
}
