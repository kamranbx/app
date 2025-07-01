<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleApiAuthController extends Controller
{
    public function handle(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $client = new \Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($request->token);

        if (!$payload) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $email = $payload['email'];
        $name = $payload['name'] ?? 'No Name';

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make(Str::random(32))]
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
