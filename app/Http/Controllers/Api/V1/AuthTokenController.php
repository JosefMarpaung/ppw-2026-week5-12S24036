<?php

// 1. Menentukan lokasi namespace pengendali API autentikasi versi 1
namespace App\Http\Controllers\Api\V1;

// 2. Mengimpor controller dasar Laravel
use App\Http\Controllers\Controller;
// 3. Mengimpor model User untuk pencarian akun pustakawan di basis data
use App\Models\User;
// 4. Mengimpor trait ApiResponse untuk standarisasi struktur JSON
use App\Traits\ApiResponse;
// 5. Mengimpor kelas JsonResponse
use Illuminate\Http\JsonResponse;
// 6. Mengimpor kelas Request HTTP
use Illuminate\Http\Request;
// 7. Mengimpor fasad Hash untuk verifikasi sandi terenkripsi Bcrypt
use Illuminate\Support\Facades\Hash;

/**
 * Kelas AuthTokenController menangani Otentikasi Stateless Berbasis Bearer Token (Sesuai Modul Hal 28 & 34 Poin 3).
 * Pada arsitektur REST, server TIDAK menyimpan sesi klien di memori server (Zero Server Session).
 * Klien menyimpan token di sisinya dan melampirkannya pada header 'Authorization: Bearer <token>' pada setiap request.
 */
class AuthTokenController extends Controller
{
    // 1. Mengaktifkan fungsi successResponse dan problemResponse
    use ApiResponse;

    /**
     * Endpoint: POST /api/v1/auth/login
     * Memverifikasi kredensial pustakawan dan menerbitkan token Sanctum baru.
     *
     * @param Request $request Membawa email dan password pustakawan
     * @return JsonResponse Respons 200 OK dengan access_token atau 401 Unauthorized
     */
    public function login(Request $request): JsonResponse
    {
        // 1. Memvalidasi format input email dan keberadaan password
        $validated = $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
            'device_name' => 'nullable|string|max:100', // Opsional: nama perangkat klien
        ]);

        // 2. Mencari data akun pengguna di tabel users berdasarkan alamat email
        $user = User::where('email', $validated['email'])->first();

        // 3. Memeriksa apakah user tidak ditemukan ATAU password salah (menggunakan Hash::check)
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            // Sesuai kaidah REST & RFC 7807: Wajib return HTTP 401 Unauthorized (bukan 200 dengan status false!)
            return $this->problemResponse(
                'Invalid Credentials',
                'Kombinasi email atau kata sandi yang Anda berikan tidak cocok dengan data pustakawan kami.',
                401,
                'invalid-credentials'
            );
        }

        // 4. Menentukan nama perangkat identifikasi token (default: postman-sipus)
        $deviceName = $validated['device_name'] ?? 'postman-sipus';

        // 5. Menerbitkan string stateless Bearer Token baru ke tabel personal_access_tokens
        $token = $user->createToken($deviceName)->plainTextToken;

        // 6. Mengembalikan respons JSON 200 OK yang membawa access_token untuk disimpan oleh klien
        return $this->successResponse([
            'token_type'   => 'Bearer',       // Standar skema autentikasi HTTP Bearer
            'access_token' => $token,         // String token yang wajib dikirim klien
            'user'         => [
                'id'    => $user->id,         // ID unik pustakawan
                'name'  => $user->name,       // Nama pustakawan
                'email' => $user->email,      // Email pustakawan
            ],
        ], 'Otentikasi API berhasil. Lampirkan access_token pada header Authorization: Bearer <token>.');
    }

    /**
     * Endpoint: GET /api/v1/auth/me
     * Menampilkan profil pustakawan pemilik token yang sedang aktif saat ini.
     * Endpoint ini diproteksi oleh middleware 'auth:sanctum'.
     *
     * @param Request $request
     * @return JsonResponse Profil user terautentikasi
     */
    public function me(Request $request): JsonResponse
    {
        // 1. Mengambil objek model User dari request yang sudah divalidasi oleh Sanctum
        return $this->successResponse(
            $request->user(),
            'Profil pemilik token aktif berhasil diambil.'
        );
    }

    /**
     * Endpoint: POST /api/v1/auth/logout
     * Mencabut (revoke) token yang sedang digunakan agar tidak dapat dipakai lagi.
     * Endpoint ini diproteksi oleh middleware 'auth:sanctum'.
     *
     * @param Request $request
     * @return JsonResponse Respons sukses pencabutan token
     */
    public function logout(Request $request): JsonResponse
    {
        // 1. Menghapus baris token yang sedang dipakai dari tabel personal_access_tokens
        $request->user()->currentAccessToken()->delete();

        // 2. Mengembalikan respons 200 OK bahwa sesi API telah berakhir secara stateless
        return $this->successResponse(
            null,
            'Bearer Token berhasil dicabut (revoked). Sesi API telah diakhiri secara aman.'
        );
    }
}
