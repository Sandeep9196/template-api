<?php

namespace App\Services;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(array $data): JsonResponse
    {
        try {
            $data = strpos($data['email'], '@') === false ? ['name' => $data['email'], 'password' => $data['password']] : $data;
            unset($data['lang_id']);
            if (Auth::attempt($data)) {
                $user = Auth::user();
                LoginLog::create([
                    'user_id' => $user->id,
                    'user_type' => User::class,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

                return response()->json([
                    'user' => $user,
                    'roles' => $user->roles,
                    'permissions' => $user->getAllPermissions(),
                    'notifications' => $user->notifications(),
                    'token' => $user->createToken($user->email)->plainTextToken,
                ], 200);
            }

            return response()->json([
                'messages' => ['Incorrect Username or password'],
            ], 401);
        } catch (\Exception $e) {
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function register($request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);
        $user = Auth::user();


        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function user()
    {
        if (Auth::user()) {
            $user = Auth::user();
            return response()->json([
                'user' => $user,
                'roles' => $user->roles,
                'permissions' => $user->getAllPermissions(),
                'token' => $user->createToken($user->email)->plainTextToken,
            ], 200);
        }

        return response()->json(['messages' => ['User not found']], 401);
    }

    public function logout()
    {
        if (Auth::user()) {
            Auth::user()->tokens()->delete();
            Auth::guard('web')->logout();
        }

        return response()->json(['messages' => ['Logout successfully']],200);
    }
}
