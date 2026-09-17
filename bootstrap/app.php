<?php

use App\Http\Middleware\CheckEntitlement;
use App\Http\Middleware\EnsureSubscriptionNotLocked;
use App\Http\Middleware\ResolveTenant;
use App\Http\Middleware\SetLocale;
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
        // Register middleware aliases
        $middleware->alias([
            'resolve.tenant' => ResolveTenant::class,
            'entitlement' => CheckEntitlement::class,
            'set.locale' => SetLocale::class,
            'subscription.active' => EnsureSubscriptionNotLocked::class,
        ]);

        // Apply locale and subscription lock check globally to web routes
        $middleware->web(append: [
            SetLocale::class,
            EnsureSubscriptionNotLocked::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Return JSON for API routes on AuthenticationException (expired/missing token)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => __('auth.token_expired'),
                ], 401);
            }
        });

        // Return JSON for API routes on ModelNotFoundException (404)
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.not_found'),
                ], 404);
            }
        });

        // Return JSON for API routes on ValidationException (422)
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => __('validation.failed'),
                    'errors'  => $e->errors(),
                ], 422);
            }
        });
    })->create();
