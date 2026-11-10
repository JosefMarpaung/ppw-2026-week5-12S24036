<?php

// 1. Mengimpor fasad Route resmi dari framework Laravel
use Illuminate\Support\Facades\Route;

// 2. Mengimpor Controller API versi 1 yang menangani logika bisnis
use App\Http\Controllers\Api\V1\AuthTokenController;
use App\Http\Controllers\Api\V1\BukuApiController;
use App\Http\Controllers\Api\V1\PeminjamanApiController;

/**
 * Peta Rute RESTful Web API v1 (Sesuai Modul Praktikum Hal 22, 29, & 34).
 * Kaidah Emas REST:
 *   1. Menggunakan Versi API Sejak Awal (/api/v1/...) untuk stabilitas kontrak.
 *   2. Menggunakan Kata Benda Jamak (Plural Nouns: /bukus, /peminjamans), BUKAN kata kerja.
 *   3. Memisahkan secara tegas Endpoint Publik dan Endpoint Terproteksi Token (auth:sanctum).
 */
Route::prefix('v1')->group(function () {

    // =========================================================================
    // 1. ENDPOINT PUBLIK (Dapat Diakses Siapapun Tanpa Bearer Token)
    // =========================================================================

    // 1.1 Operasi Login: Memverifikasi email/password dan menerbitkan Stateless Bearer Token
    Route::post('/auth/login', [AuthTokenController::class, 'login']);

    // 1.2 Katalog Buku: Menampilkan koleksi buku terpaginasi, search, dan HATEOAS level 3
    Route::get('/bukus', [BukuApiController::class, 'index']);

    // 1.3 Detail Buku: Menampilkan detail satu buku berdasarkan ID (atau 404 RFC 7807)
    Route::get('/bukus/{id}', [BukuApiController::class, 'show']);

    // 1.4 REST Service Consumer: Mengambil metadata tambahan ISBN dari OpenLibrary REST API publik
    Route::get('/bukus/{id}/referensi-eksternal', [BukuApiController::class, 'referensiEksternal']);

    // =========================================================================
    // 2. ENDPOINT TERPROTEKSI STATELESS BEARER TOKEN (Wajib Header 'Authorization: Bearer <token>')
    // Diproteksi oleh middleware 'auth:sanctum'
    // =========================================================================
    Route::middleware('auth:sanctum')->group(function () {

        // 2.1 Manajemen Identitas Token Aktif
        Route::get('/auth/me', [AuthTokenController::class, 'me']);          // Menampilkan profil pemilik token
        Route::post('/auth/logout', [AuthTokenController::class, 'logout']); // Mencabut (revoke) token aktif

        // 2.2 Mutasi Katalog Buku (Kaidah Semantik HTTP Verbs)
        Route::post('/bukus', [BukuApiController::class, 'store']);          // POST: Menambah buku baru (201 Created)
        Route::put('/bukus/{id}', [BukuApiController::class, 'update']);     // PUT: Mengganti representasi utuh (Full Replacement)
        Route::patch('/bukus/{id}', [BukuApiController::class, 'update']);   // PATCH: Mengubah atribut tertentu saja (Partial Update)
        Route::delete('/bukus/{id}', [BukuApiController::class, 'destroy']); // DELETE: Menghapus buku (204 No Content / 409 Conflict)

        // 2.3 Sirkulasi Peminjaman & Pengembalian Buku (Transaksi ACID)
        Route::get('/peminjamans', [PeminjamanApiController::class, 'index']); // GET: Riwayat transaksi sirkulasi
        Route::post('/peminjamans', [PeminjamanApiController::class, 'store']); // POST: Pinjam buku baru ACID (lockForUpdate)
        Route::patch('/peminjamans/{id}/kembalikan', [PeminjamanApiController::class, 'kembalikan']); // PATCH: Kembalikan buku (+1 stok)
    });
});
