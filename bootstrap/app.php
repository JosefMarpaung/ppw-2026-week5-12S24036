<?php

// 1. Mengimpor kelas exception autentikasi saat token tidak sah atau hilang
use Illuminate\Auth\AuthenticationException;
// 2. Mengimpor kelas basis aplikasi Laravel 11
use Illuminate\Foundation\Application;
// 3. Mengimpor konfigurator exception global
use Illuminate\Foundation\Configuration\Exceptions;
// 4. Mengimpor konfigurator middleware global
use Illuminate\Foundation\Configuration\Middleware;
// 5. Mengimpor kelas Request HTTP
use Illuminate\Http\Request;
// 6. Mengimpor kelas exception validasi input form/JSON
use Illuminate\Validation\ValidationException;
// 7. Mengimpor kelas exception HTTP 404 saat URL atau data tidak ditemukan
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Berkas Konfigurasi Inti Aplikasi Laravel 11 (bootstrap/app.php).
 * Memenuhi Spesifikasi Modul Praktikum Hal 12, 17, 22–23, & 34:
 *   1. Mendaftarkan jalur rute web dan REST API.
 *   2. Membebaskan rute SOAP (/soap/*) dari proteksi CSRF Cookie browser.
 *   3. Memasang Global JSON Exception Handler berstandar IETF RFC 7807 (Problem Details).
 */
return Application::configure(basePath: dirname(__DIR__))
    // 1. Konfigurasi Routing Aplikasi
    ->withRouting(
        web: __DIR__.'/../routes/web.php',       // Menangani antarmuka web HTML tradisional (Minggu 1-6)
        api: __DIR__.'/../routes/api.php',       // Menangani rute RESTful API nir-status (Minggu 9)
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // 2. Konfigurasi Middleware Global
    ->withMiddleware(function (Middleware $middleware): void {
        // [KRITIS MODUL HALAMAN 13]: Kecualikan endpoint SOAP dari token CSRF browser (@csrf)
        // karena pemanggil SOAP adalah mesin eksternal (machine-to-machine), bukan browser!
        $middleware->validateCsrfTokens(except: [
            'soap/*',
        ]);
    })
    // 3. Konfigurasi Global Exception Handler Standar IETF RFC 7807 (Problem Details for HTTP APIs)
    ->withExceptions(function (Exceptions $exceptions): void {

        // [JEBAKAN PEMULA LARAVEL API - MODUL HALAMAN 17 & 23]:
        // Paksa setiap request pada awalan /api/* untuk selalu menghasilkan format JSON murni.
        // Ini mencegah Laravel melakukan redirect 302 Found ke halaman HTML Welcome jika klien lupa header Accept!
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        // Standar 1: Error Validasi Input (HTTP 422 Unprocessable Content - RFC 7807)
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'type'     => url('/docs/errors/validation-failed'),
                    'title'    => 'Unprocessable Content',
                    'status'   => 422,
                    'detail'   => 'Satu atau lebih atribut input yang dikirim tidak memenuhi aturan validasi bisnis.',
                    'instance' => $request->getRequestUri(),
                    'errors'   => $e->errors(), // Daftar error spesifik per kolom form/JSON
                ], 422)->header('Content-Type', 'application/problem+json');
            }
        });

        // Standar 2: Error Belum Login / Bearer Token Tidak Valid (HTTP 401 Unauthorized - RFC 7807)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'type'     => url('/docs/errors/unauthenticated'),
                    'title'    => 'Unauthorized',
                    'status'   => 401,
                    'detail'   => 'Akses ditolak. Sertakan Bearer Token yang valid pada header Authorization: Bearer <token>.',
                    'instance' => $request->getRequestUri(),
                ], 401)->header('Content-Type', 'application/problem+json');
            }
        });

        // Standar 3: Error URL Salah / Endpoint Tidak Ada (HTTP 404 Not Found - RFC 7807)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'type'     => url('/docs/errors/endpoint-not-found'),
                    'title'    => 'Not Found',
                    'status'   => 404,
                    'detail'   => 'Alamat URI endpoint atau sumber daya yang Anda minta tidak ditemukan di server.',
                    'instance' => $request->getRequestUri(),
                ], 404)->header('Content-Type', 'application/problem+json');
            }
        });
    })->create();
