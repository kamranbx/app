<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Services\Auth\Jwt;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleAuthorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $token = $request->bearerToken();
        $decoded = Jwt::decode($token);
        $role = $decoded->user->role ?? null;

        if (!$role || !UserRole::tryFrom($role)) {
            return response()->json(['error' => 'Invalid or missing role in token'], 403);
        }

        $roleEnum = UserRole::from($role);
        $allowedRoles = collect($roles)->map(fn($r) => UserRole::from($r));

        if (!$allowedRoles->contains($roleEnum)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
