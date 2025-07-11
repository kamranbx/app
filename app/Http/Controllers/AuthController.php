<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\Auth\Jwt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', new Enum(UserRole::class)],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        $tokens = auth()->login($user);

        if($tokens)
            return response()->json([
               'message' => 'Successfully registered!',
               'access_token'  => $tokens['access'],
               'refresh_token' => $tokens['refresh'],
            ], 200);
        else
            return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if($tokens = auth()->attempt($credentials))
            return response()->json([
                'message' => 'Successfully logged in!',
                'access_token'  => $tokens['access'],
                'refresh_token' => $tokens['refresh'],
            ], 200);
        else
            return response()->json([
                'message' => 'Invalid credentials!',
            ], 401);
    }

    public function logout(Request $request)
    {
        $request->validate(['refresh_token' => 'required|string']);
        $token = $request->refresh_token;

        if (!$token)
            return response()->json(['message' => 'No token provided'], 400);

        try
        {
            $signature = Jwt::getSignature($token);
            $decoded = Jwt::decode($token, true);

            $ttl = $decoded->exp - time();
            Cache::put('blacklisted_refresh:' . $signature, true, now()->addSeconds($ttl));

            return response()->json(['message' => 'Successfully logged out']);
        }
        catch (\Exception $e)
        {
            return response()->json(['message' => 'Invalid token'], 401);
        }
    }

    public function user()
    {
        return response()->json([
            'message' => 'Data retrieved',
            'user' => auth()->user()
        ]);
    }

    public function refreshToken(Request $request)
    {
        $request->validate(['refresh_token' => 'required|string']);

        $refreshToken = $request->refresh_token;

        try
        {
            $decoded = Jwt::decode($refreshToken, true);

            if($decoded->type !== 'refresh')
                return response()->json(['error' => 'Invalid token type'], 401);

            $signature = Jwt::getSignature($refreshToken);
            if(Cache::has("blacklisted_refresh:$signature"))
                return response()->json(['error' => 'Token already used'], 401);

            $user = \App\Models\User::find($decoded->sub);
            if(!$user)
                return response()->json(['error' => 'User not found'], 404);

            $ttl = $decoded->exp - time();
            Cache::put("blacklisted_refresh:$signature", true, $ttl);

            $newRefreshToken = Jwt::generateFromUser($user, true);
            $newAccessToken  = Jwt::generateFromUser($user);

            return response()->json([
                'message' => 'Access token refreshed',
                'access_token' => $newAccessToken,
                'refresh_token' => $newRefreshToken,
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }
    }
}
