<?php

// 1. Mendeklarasikan namespace untuk direktori Trait pembantu aplikasi
namespace App\Traits;

// 2. Mengimpor kelas JsonResponse resmi Laravel untuk menghasilkan representasi respons HTTP JSON
use Illuminate\Http\JsonResponse;

/**
 * Trait ApiResponse (Sesuai Modul Hal 17–18 & 34 Poin 2).
 * Bertujuan menstandarkan amplop (envelope) respons JSON di seluruh Controller REST API SIM/SIPUS-Del.
 * Memastikan respons sukses selalu konsisten dan respons error mematuhi standar IETF RFC 7807 (Problem Details).
 */
trait ApiResponse
{
    /**
     * Menyusun respons sukses standar (Default: HTTP 200 OK).
     * Digunakan untuk operasi pembacaan data (GET) atau pembaruan sebagian (PATCH).
     *
     * @param mixed $data Data payload yang dikirim ke klien (array, objek, atau koleksi Resource)
     * @param string $message Pesan informatif penjelas keberhasilan operasi
     * @param int $status Kode status HTTP numerik (default: 200)
     * @param array $meta Metadata tambahan (misalnya informasi halaman/paginasi)
     * @return JsonResponse Objek JSON kembalian dengan kode status HTTP yang jujur
     */
    protected function successResponse(
        mixed $data = null,
        string $message = 'Permintaan berhasil diproses.',
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        // 1. Membangun amplop JSON dasar yang seragam untuk seluruh API
        $payload = [
            'success' => true,      // Penanda boolean status pemrosesan bisnis
            'message' => $message,   // Kalimat human-readable penjelas hasil
            'data'    => $data,      // Payload representasi sumber daya inti
        ];

        // 2. Jika parameter $meta tidak kosong (misalnya ada paginasi), sisipkan ke dalam amplop
        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        // 3. Mengembalikan objek response bertipe application/json dengan kode status HTTP semantik
        return response()->json($payload, $status);
    }

    /**
     * Menyusun respons sukses pembuatan sumber daya baru (HTTP 201 Created).
     * Kaidah Emas REST: Wajib menyertakan Header 'Location' yang menunjuk ke URI sumber daya baru!
     *
     * @param mixed $data Representasi objek data baru yang baru saja tersimpan di database
     * @param string $locationUrl URL absolut menuju endpoint detail data baru tersebut
     * @param string $message Pesan keberhasilan penyimpanan data baru
     * @return JsonResponse Objek JSON berstatus 201 Created lengkap dengan header Location
     */
    protected function createdResponse(
        mixed $data,
        string $locationUrl,
        string $message = 'Resource baru berhasil dibuat.'
    ): JsonResponse {
        // 1. Memanggil successResponse dengan status 201 Created
        // 2. Menyematkan header 'Location' sesuai konvensi standar RFC 7231 RESTful
        return $this->successResponse($data, $message, 201)
            ->header('Location', $locationUrl);
    }

    /**
     * Menyusun respons sukses penghapusan data tanpa body (HTTP 204 No Content).
     * Kaidah Emas REST: Operasi DELETE yang sukses tidak perlu memboroskan kuota mengirim body JSON!
     *
     * @return JsonResponse Objek response kosong dengan kode status HTTP 204
     */
    protected function noContentResponse(): JsonResponse
    {
        // 1. Mengembalikan body bernilai null dan status 204 No Content (0 bytes payload)
        return response()->json(null, 204);
    }

    /**
     * Menyusun respons galat resmi berstandar IETF RFC 7807 (Problem Details for HTTP APIs).
     * Standar ini mewajibkan 5 atribut pokok: type, title, status, detail, dan instance.
     *
     * @param string $title Ringkasan judul masalah (misal: 'Resource Not Found')
     * @param string $detail Penjelasan mendalam penyebab galat secara spesifik
     * @param int $status Kode status HTTP kategori 4xx (Client Error) atau 5xx (Server Error)
     * @param string $errorSlug Slug unik untuk penunjuk tautan dokumentasi masalah
     * @param array $errors Rincian daftar validasi gagal per kolom input (opsional, untuk 422)
     * @return JsonResponse Objek JSON bertipe Content-Type: application/problem+json
     */
    protected function problemResponse(
        string $title,
        string $detail,
        int $status,
        string $errorSlug = 'general-error',
        array $errors = []
    ): JsonResponse {
        // 1. Menyusun struktur 5 atribut inti RFC 7807
        $problem = [
            'type'     => url("/docs/errors/{$errorSlug}"), // URI dokumentasi referensi galat
            'title'    => $title,                            // Judul singkat kategori kesalahan
            'status'   => $status,                           // Kode numerik status HTTP
            'detail'   => $detail,                           // Penjelasan rinci yang ramah pengembang
            'instance' => request()->getRequestUri(),        // URI spesifik yang memicu galat ini
        ];

        // 2. Jika terdapat rincian kegagalan validasi kolom form, sematkan ke dalam array 'errors'
        if (!empty($errors)) {
            $problem['errors'] = $errors;
        }

        // 3. Mengembalikan respons JSON dengan header Content-Type khusus: application/problem+json
        return response()->json($problem, $status)
            ->header('Content-Type', 'application/problem+json');
    }
}
