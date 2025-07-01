<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleApiAuthController extends Controller
{
    public function handle(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->token);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            ['name' => $googleUser->getName(), 'password' => Hash::make(Str::random(32))]
        );

        $tokens = auth()->login($user);

        if ($tokens) {
            return response()->json([
                'message' => 'Successfully logged in via Google!',
                'access_token' => $tokens['access'],
                'refresh_token' => $tokens['refresh'],
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
