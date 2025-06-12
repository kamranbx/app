<?php

namespace App\Services\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JwtGuard implements Guard
{
    use GuardHelpers;
    private $request;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    public function user()
    {
        if ($this->user)
            return $this->user;

        $token = $this->request->bearerToken();

        try
        {
            $decoded = Jwt::decode($token);

            $user = $this->provider->retrieveById($decoded->sub);

            return $this->user = $user;
        }
        catch (\Exception $e)
        {
            Log::error('JWT auth failed: ' . $e->getMessage());
            return null;
        }
    }

    public function validate(array $credentials = [])
    {
        return (bool) $this->attempt($credentials, false);
    }

    public function attempt(array $credentials)
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if($user)
        {
            return $this->login($user);
        }

        return false;
    }

    public function login($user)
    {
        $access_token = Jwt::generateFromUser($user);
        $refresh_token = Jwt::generateFromUser($user, true);

        $this->setUser($user);

        return [
            'access'  => $access_token,
            'refresh' => $refresh_token
        ];
    }

//    public function setToken($token)
//    {
//        $this->jwt->setToken($token);
//
//        return $this;
//    }
}
