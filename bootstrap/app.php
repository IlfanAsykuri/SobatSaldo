<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Mempercayai semua proxy (karena Nginx & Cloudflare sudah memfilter di depan)
        $middleware->trustProxies(at: '*');
        $middleware->authenticateSessions();
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo('/workspace');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // expectsJson() wajib ikut dicek: tanpa ini, fetch() di workspace yang gagal validasi
        // mendapat redirect 302 ke halaman HTML, bukan JSON 422 berisi pesan error
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
