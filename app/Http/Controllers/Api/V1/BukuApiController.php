<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BukuResource;
use App\Models\Buku;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BukuApiController extends Controller
{
    use ApiResponse;

    // 1. GET /api/v1/bukus (Publik - Bebas N+1 via with('kategori'))
    public function index(Request $request): JsonResponse
    {
        $sortBy = in_array($request->query('sort'), ['judul', 'stok', 'tahun_terbit']) ? $request->query('sort') : 'judul';
        $sortDir = strtolower($request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $perPage = min(max((int)$request->query('per_page', 5), 1), 50);

        $paginator = Buku::query()
            ->with('kategori')
            ->when($request->query('search'), fn($q, $s) => $q->where(fn($sub) => $sub->where('judul', 'like', "%{$s}%")->orWhere('penulis', 'like', "%{$s}%")->orWhere('isbn', 'like', "%{$s}%")))
            ->when($request->query('kategori_id'), fn($q, $id) => $q->where('kategori_id', $id))
            ->when($request->boolean('tersedia'), fn($q) => $q->where('stok', '>', 0))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);

        return $this->successResponse(BukuResource::collection($paginator->items()), 'Katalog buku berhasil diambil.', 200, [
            'current_page' => $paginator->currentPage(),
            'total_items'  => $paginator->total(),
            'total_pages'  => $paginator->lastPage(),
        ]);
    }

    // 2. GET /api/v1/bukus/{id} (Publik)
    public function show(string $id): JsonResponse
    {
        $buku = Buku::with('kategori')->find($id);
        if (!$buku) { return $this->problemResponse('Resource Not Found', "Buku ID '{$id}' tidak ditemukan.", 404, 'book-not-found'); }
        return $this->successResponse(new BukuResource($buku), 'Detail buku ditemukan.');
    }

    // 3. POST /api/v1/bukus (Bearer Token -> 201 Created + Header Location)
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'isbn'         => 'required|string|max:20|unique:bukus,isbn',
            'judul'        => 'required|string|max:255',
            'penulis'      => 'required|string|max:150',
            'penerbit'     => 'required|string|max:150',
            'tahun_terbit' => 'required|integer',
            'kategori_id'  => 'required|exists:kategoris,id',
            'stok'         => 'required|integer|min:0',
            'sinopsis'     => 'nullable|string',
        ]);

        $buku = Buku::create($validated);
        $buku->load('kategori');
        return $this->createdResponse(new BukuResource($buku), url("/api/v1/bukus/{$buku->id}"), 'Buku berhasil dibuat.');
    }

    // 4. PUT / PATCH /api/v1/bukus/{id} (Perbedaan Semantik PUT vs PATCH)
    public function update(Request $request, string $id): JsonResponse
    {
        $buku = Buku::find($id);
        if (!$buku) { return $this->problemResponse('Resource Not Found', "Buku ID '{$id}' tidak ditemukan.", 404, 'book-not-found'); }

        $isPut = $request->isMethod('put');
        if ($isPut) {
            // PUT: Seluruh atribut wajib dikirim ulang
            $validated = $request->validate([
                'isbn'         => "required|string|max:20|unique:bukus,isbn,{$buku->id}",
                'judul'        => 'required|string|max:255',
                'penulis'      => 'required|string|max:150',
                'penerbit'     => 'required|string|max:150',
                'tahun_terbit' => 'required|integer',
                'kategori_id'  => 'required|exists:kategoris,id',
                'stok'         => 'required|integer|min:0',
                'sinopsis'     => 'nullable|string',
            ]);
            $validated['sinopsis'] = $request->input('sinopsis', null);
        } else {
            // PATCH: Hanya field yang dikirim yang divalidasi dan diubah
            $validated = $request->validate([
                'isbn'         => "sometimes|required|string|max:20|unique:bukus,isbn,{$buku->id}",
                'judul'        => 'sometimes|required|string|max:255',
                'penulis'      => 'sometimes|required|string|max:150',
                'penerbit'     => 'sometimes|required|string|max:150',
                'tahun_terbit' => 'sometimes|required|integer',
                'kategori_id'  => 'sometimes|required|exists:kategoris,id',
                'stok'         => 'sometimes|required|integer|min:0',
                'sinopsis'     => 'sometimes|nullable|string',
            ]);
            if (empty($validated)) { return $this->problemResponse('Bad Request', 'Payload PATCH tidak boleh kosong.', 400, 'empty-patch'); }
        }

        $buku->update($validated);
        $buku->load('kategori');
        return $this->successResponse(new BukuResource($buku), $isPut ? 'Data diganti penuh (PUT).' : 'Atribut diubah sebagian (PATCH).');
    }

    // 5. DELETE /api/v1/bukus/{id} (Tolak 409 Conflict jika ada pinjaman aktif)
    public function destroy(string $id): JsonResponse
    {
        $buku = Buku::find($id);
        if (!$buku) { return $this->problemResponse('Resource Not Found', "Buku ID '{$id}' tidak ditemukan.", 404, 'book-not-found'); }

        if ($buku->peminjamans()->where('status', 'dipinjam')->exists()) {
            return $this->problemResponse('Conflict — Book Has Active Loans', "Buku tidak boleh dihapus karena sedang dipinjam.", 409, 'active-loans');
        }

        $buku->delete();
        return $this->noContentResponse();
    }

    // 6. GET /api/v1/bukus/{id}/referensi-eksternal (Konsumen OpenLibrary API)
    public function referensiEksternal(string $id): JsonResponse
    {
        $buku = Buku::find($id);
        if (!$buku) { return $this->problemResponse('Resource Not Found', 'Buku tidak ditemukan.', 404, 'book-not-found'); }

        try {
            $isbnClean = preg_replace('/[^0-9X]/i', '', $buku->isbn);
            $res = Http::timeout(8)->acceptJson()->get("https://openlibrary.org/api/books?bibkeys=ISBN:{$isbnClean}&format=json&jscmd=data");
            if ($res->successful() && isset($res->json()["ISBN:{$isbnClean}"])) {
                $ext = $res->json()["ISBN:{$isbnClean}"];
                return $this->successResponse([
                    'buku_lokal'     => $buku->judul,
                    'isbn'           => $buku->isbn,
                    'judul_resmi'    => $ext['title'] ?? $buku->judul,
                    'jumlah_halaman' => $ext['number_of_pages'] ?? 'Tidak tersedia',
                ], 'Metadata eksternal ditemukan.');
            }
            return $this->successResponse(['buku_lokal' => $buku->judul, 'catatan' => 'Buku tidak ada di OpenLibrary.'], 'Kueri selesai.');
        } catch (\Throwable $th) {
            return $this->problemResponse('Service Unavailable', 'Gagal memanggil API luar.', 503, 'external-error');
        }
    }
}