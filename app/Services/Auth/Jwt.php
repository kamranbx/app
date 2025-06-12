<?php

namespace App\Services\Auth;

use Firebase\JWT\Key;

class Jwt
{
    public static function generateFromUser($user, $refresh = false)
    {
        $ttl = $refresh ? env('JWT_REFRESH_TTL') : env('JWT_TTL');
        $payload = [
            'type' => $refresh ? 'refresh' : 'access',
            'iss' => env('APP_NAME', 'app'),
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + $ttl,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ]
        ];

        $secret = $refresh ? env('JWT_REFRESH_SECRET') : env('JWT_SECRET');
        $jwt = \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');

        return $jwt;
    }

    public static function decode($token, $refresh = false)
    {
        $secret = $refresh ? env('JWT_REFRESH_SECRET') : env('JWT_SECRET');

        return \Firebase\JWT\JWT::decode($token, new Key($secret, 'HS256'));
    }

    public static function getSignature($jwt)
    {
        $parts = explode('.', $jwt);

        if (count($parts) !== 3) {
            throw new \InvalidArgumentException("Invalid JWT format");
        }

        return $parts[2];
    }
}
