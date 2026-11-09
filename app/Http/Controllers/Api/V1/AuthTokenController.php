<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthTokenController extends Controller
{
    use ApiResponse; // 1. Aktifkan trait respons standar

    // 2. POST /api/v1/auth/login (Terbitkan Sanctum Bearer Token)
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $user = User::where('email', $validated['email'])->first();

        // 3. Verifikasi kredensial password hash
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return $this->problemResponse('Invalid Credentials', 'Kombinasi email atau kata sandi tidak cocok.', 401, 'invalid-credentials');
        }

        // 4. Terbitkan plainTextToken stateless
        $token = $user->createToken('token-pustakawan')->plainTextToken;

        return $this->successResponse([
            'token_type'   => 'Bearer',
            'access_token' => $token,
            'user'         => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ], 'Otentikasi berhasil.');
    }

    // 5. GET /api/v1/auth/me (Profil user aktif)
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse($request->user(), 'Profil aktif.');
    }

    // 6. POST /api/v1/auth/logout (Revoke token aktif)
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(null, 'Token berhasil dicabut.');
    }
}