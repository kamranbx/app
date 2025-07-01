<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try
        {
            $user = Socialite::driver('google')->user();
        }
        catch (Throwable $e)
        {
            return redirect('/')->with('error', 'Google authentication failed.');
        }

        $user = User::firstOrCreate(
            ['email' => $user->getEmail()],
            [
                'name' => $user->getName(),
                'password' => Hash::make(uniqid()),
            ]
        );

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
}
