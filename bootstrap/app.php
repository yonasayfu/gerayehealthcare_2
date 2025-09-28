<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'ensure_staff_linked' => \App\Http\Middleware\EnsureStaffLinked::class,
            'custom_role' => \App\Http\Middleware\RoleMiddleware::class,
            'custom_permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'deny_roles' => \App\Http\Middleware\DenyRoles::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($e instanceof ResourceNotFoundException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Resource not found.'], 404);
                }

                return back()->with('error', 'Resource not found.');
            }

            if ($e instanceof ValidationException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => $e->getMessage(), 'errors' => []], 422);
                }

                return back()->with('error', $e->getMessage());
            }

            return null; // Let the default handler take over
        });
    })->create();
