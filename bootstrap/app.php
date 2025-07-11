<?php

use App\Http\Middleware\JwtAuthenticate;
use App\Http\Middleware\RoleAuthorize;
use App\Http\Middleware\RolePermissionAuthorize;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'jwt'  => JwtAuthenticate::class,
            'role' => RoleAuthorize::class,
            'role-perm' => RolePermissionAuthorize::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
