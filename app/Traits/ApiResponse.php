<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    // 1. Format respons sukses 200 OK dengan amplop seragam
    protected function successResponse(mixed $data = null, string $message = 'Permintaan berhasil diproses.', int $status = 200, array $meta = []): JsonResponse
    {
        $payload = ['success' => true, 'message' => $message, 'data' => $data];
        if (!empty($meta)) { $payload['meta'] = $meta; } // 2. Sertakan pagination bila ada
        return response()->json($payload, $status);
    }

    // 3. Format respons sukses 201 Created + Header Location wajib sesuai REST
    protected function createdResponse(mixed $data, string $locationUrl, string $message = 'Resource baru berhasil dibuat.'): JsonResponse
    {
        return $this->successResponse($data, $message, 201)->header('Location', $locationUrl);
    }

    // 4. Format respons hapus 204 No Content tanpa payload body
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    // 5. Format respons galat terstandar IETF RFC 7807 Problem Details
    protected function problemResponse(string $title, string $detail, int $status, string $errorSlug = 'general-error', array $errors = []): JsonResponse
    {
        $problem = [
            'type'     => url("/docs/errors/{$errorSlug}"),
            'title'    => $title,
            'status'   => $status,
            'detail'   => $detail,
            'instance' => request()->getRequestUri(),
        ];
        if (!empty($errors)) { $problem['errors'] = $errors; }
        return response()->json($problem, $status)->header('Content-Type', 'application/problem+json');
    }
}