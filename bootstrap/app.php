<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',       // 1. Jalur rute web HTML
        api: __DIR__.'/../routes/api.php',       // 2. Jalur rute REST API
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 3. Bebaskan endpoint SOAP dari token CSRF browser karena dipanggil oleh mesin
        $middleware->validateCsrfTokens(except: ['soap/*']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 4. Paksa rute /api/* merender respons JSON murni jika terjadi kesalahan
        $exceptions->shouldRenderJsonWhen(fn (Request $r) => $r->is('api/*') || $r->expectsJson());

        // 5. Standar Error Validasi Gagal (HTTP 422 - RFC 7807)
        $exceptions->render(function (ValidationException $e, Request $r) {
            if ($r->is('api/*')) {
                return response()->json([
                    'type'     => url('/docs/errors/validation-failed'),
                    'title'    => 'Unprocessable Content',
                    'status'   => 422,
                    'detail'   => 'Atribut input yang dikirim tidak memenuhi aturan validasi.',
                    'instance' => $r->getRequestUri(),
                    'errors'   => $e->errors(),
                ], 422)->header('Content-Type', 'application/problem+json');
            }
        });

        // 6. Standar Error Belum Login / Token Salah (HTTP 401 - RFC 7807)
        $exceptions->render(function (AuthenticationException $e, Request $r) {
            if ($r->is('api/*')) {
                return response()->json([
                    'type'     => url('/docs/errors/unauthenticated'),
                    'title'    => 'Unauthorized',
                    'status'   => 401,
                    'detail'   => 'Akses ditolak. Sertakan Bearer Token valid pada header Authorization.',
                    'instance' => $r->getRequestUri(),
                ], 401)->header('Content-Type', 'application/problem+json');
            }
        });

        // 7. Standar Error URL Tidak Ditemukan (HTTP 404 - RFC 7807)
        $exceptions->render(function (NotFoundHttpException $e, Request $r) {
            if ($r->is('api/*')) {
                return response()->json([
                    'type'     => url('/docs/errors/endpoint-not-found'),
                    'title'    => 'Not Found',
                    'status'   => 404,
                    'detail'   => 'Alamat URI endpoint atau data yang diminta tidak ditemukan.',
                    'instance' => $r->getRequestUri(),
                ], 404)->header('Content-Type', 'application/problem+json');
            }
        });
    })->create();