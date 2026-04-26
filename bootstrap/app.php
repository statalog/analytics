<?php

use App\Http\Middleware\AddCorsHeaders;
use App\Http\Middleware\ApiAuthenticate;
use App\Http\Middleware\EnsureNotViewer;
use App\Http\Middleware\ResolveActiveAccount;
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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'cors'        => AddCorsHeaders::class,
            'not-viewer'  => EnsureNotViewer::class,
            'api.auth'    => ApiAuthenticate::class,
        ]);

        // Resolve the active account (own vs switched-into-owner) on every
        // web request AFTER auth has run. SetLocale runs in the same group
        // so it can read the authed user's preferred locale.
        $middleware->web(append: [
            SetLocale::class,
            ResolveActiveAccount::class,
        ]);

        // Stripe webhook signs every request — CSRF doesn't apply.
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
