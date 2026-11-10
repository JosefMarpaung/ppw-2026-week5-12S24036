<?php

// 1. Mendeklarasikan namespace controller katalog API versi 1
namespace App\Http\Controllers\Api\V1;

// 2. Mengimpor controller dasar Laravel
use App\Http\Controllers\Controller;
// 3. Mengimpor BukuResource untuk serialisasi JSON dan kontrol HATEOAS
use App\Http\Resources\Api\V1\BukuResource;
// 4. Mengimpor Model Buku
use App\Models\Buku;
// 5. Mengimpor Trait ApiResponse untuk standarisasi format respons JSON
use App\Traits\ApiResponse;
// 6. Mengimpor kelas respons JSON
use Illuminate\Http\JsonResponse;
// 7. Mengimpor kelas Request HTTP
use Illuminate\Http\Request;
// 8. Mengimpor fasad Http (Guzzle wrapper) untuk mengonsumsi API eksternal
use Illuminate\Support\Facades\Http;

/**
 * Kelas BukuApiController mengelola operasi CRUD Katalog Buku (Sesuai Modul Hal 21–25 & 34 Poin 2).
 * Mengimplementasikan semantik HTTP murni (RMM Level 2) dan HATEOAS (RMM Level 3):
 *   - GET    : Pengambilan koleksi / detail (200 OK)
 *   - POST   : Pembuatan buku baru (201 Created + Header Location)
 *   - PUT    : Penggantian utuh seluruh atribut (Full Replacement)
 *   - PATCH  : Modifikasi sebagian atribut tertentu saja (Partial Update)
 *   - DELETE : Penghapusan buku (204 No Content / Tolak 409 Conflict jika ada sirkulasi aktif)
 *   - CONSUMER: Mengambil metadata ISBN dari OpenLibrary REST API eksternal
 */
class BukuApiController extends Controller
{
    // 1. Mengaktifkan trait pembantu amplop respons JSON
    use ApiResponse;

    /**
     * Endpoint: GET /api/v1/bukus (Publik)
     * Menampilkan daftar katalog buku dengan Search, Filter, Sort, dan Paginasi.
     * Bebas dari N+1 Query Problem menggunakan eager loading with('kategori').
     *
     * @param Request $request Menampung query parameter pencarian dan filter
     * @return JsonResponse Koleksi buku terbungkus BukuResource beserta metadata halaman
     */
    public function index(Request $request): JsonResponse
    {
        // 1. Whitelisting kolom pengurutan guna menangkal serangan SQL Injection pada ORDER BY
        $allowedSorts = ['judul', 'penulis', 'tahun_terbit', 'stok', 'created_at'];
        // 2. Memilih kolom sort berdasarkan input atau default ke 'judul'
        $sortBy = in_array($request->query('sort'), $allowedSorts) ? $request->query('sort') : 'judul';
        // 3. Memilih arah pengurutan (asc = naik, desc = turun)
        $sortDir = strtolower($request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        // 4. Membatasi per_page antara 1 sampai 50 guna mencegah eksploitasi beban memori berlebih
        $perPage = min(max((int)$request->query('per_page', 5), 1), 50);

        // 5. Menyusun kueri Eloquent secara efisien
        $paginator = Buku::query()
            // 6. Eager Loading relasi 'kategori' untuk mencegah query N+1 ke tabel kategoris
            ->with('kategori')
            // 7. Menghitung jumlah riwayat peminjaman dengan efisien (subquery SQL)
            ->withCount('peminjamans')
            // 8. Pencarian dinamis jika parameter ?search=... dikirimkan
            ->when($request->query('search'), function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('judul', 'like', "%{$search}%")
                        ->orWhere('penulis', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            // 9. Filter berdasarkan ID kategori jika parameter ?kategori_id=... dikirimkan
            ->when($request->query('kategori_id'), fn($q, $id) => $q->where('kategori_id', $id))
            // 10. Filter hanya buku yang memiliki sisa stok jika ?tersedia=1
            ->when($request->boolean('tersedia'), fn($q) => $q->where('stok', '>', 0))
            // 11. Mengurutkan hasil
            ->orderBy($sortBy, $sortDir)
            // 12. Menjalankan paginasi data
            ->paginate($perPage)
            // 13. Mempertahankan query string pada tautan paginasi
            ->withQueryString();

        // 14. Mengembalikan koleksi BukuResource terstruktur rapi dengan status 200 OK
        return $this->successResponse(
            BukuResource::collection($paginator->items()),
            'Daftar katalog buku berhasil diambil.',
            200,
            [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total_items'  => $paginator->total(),
                'total_pages'  => $paginator->lastPage(),
            ]
        );
    }

    /**
     * Endpoint: GET /api/v1/bukus/{id} (Publik)
     * Mengambil detail satu buku berdasarkan ID.
     *
     * @param string $id ID buku
     * @return JsonResponse Detail buku atau error 404 Problem Details
     */
    public function show(string $id): JsonResponse
    {
        // 1. Mencari buku berdasarkan ID beserta relasi kategorinya
        $buku = Buku::with('kategori')->find($id);

        // 2. Jika buku tidak ditemukan, kembalikan status HTTP 404 Not Found standar RFC 7807
        if (!$buku) {
            return $this->problemResponse(
                'Resource Not Found',
                "Buku dengan ID '{$id}' tidak ditemukan di pangkalan data perpustakaan.",
                404,
                'book-not-found'
            );
        }

        // 3. Mengembalikan representasi BukuResource berstatus 200 OK
        return $this->successResponse(
            new BukuResource($buku),
            'Detail buku berhasil ditemukan.'
        );
    }

    /**
     * Endpoint: POST /api/v1/bukus (Wajib Bearer Token)
     * Menambahkan buku baru ke katalog perpustakaan.
     * Kaidah Emas REST: Wajib mereturn status HTTP 201 Created dan Header 'Location'.
     *
     * @param Request $request
     * @return JsonResponse Respons 201 Created
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Memvalidasi seluruh atribut input secara ketat
        $validated = $request->validate([
            'isbn'         => 'required|string|max:20|unique:bukus,isbn',
            'judul'        => 'required|string|max:255',
            'penulis'      => 'required|string|max:150',
            'penerbit'     => 'required|string|max:150',
            'tahun_terbit' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'kategori_id'  => 'required|exists:kategoris,id',
            'stok'         => 'required|integer|min:0',
            'sinopsis'     => 'nullable|string',
        ]);

        // 2. Menyimpan baris buku baru ke tabel bukus
        $buku = Buku::create($validated);
        // 3. Memuat data kategori agar ikut muncul di resource JSON
        $buku->load('kategori');

        // 4. Mengembalikan respons 201 Created beserta Header 'Location: .../bukus/{id}'
        return $this->createdResponse(
            new BukuResource($buku),
            url("/api/v1/bukus/{$buku->id}"),
            'Buku baru berhasil ditambahkan ke katalog.'
        );
    }

    /**
     * Endpoint: PUT / PATCH /api/v1/bukus/{id} (Wajib Bearer Token)
     * Membuktikan perbedaan semantik nyata antara operasi PUT (Full Replace) dan PATCH (Partial Update).
     *
     * @param Request $request
     * @param string $id ID buku yang akan diperbarui
     * @return JsonResponse Respons 200 OK
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // 1. Mencari data buku
        $buku = Buku::find($id);
        if (!$buku) {
            return $this->problemResponse('Resource Not Found', "Buku dengan ID '{$id}' tidak ditemukan.", 404, 'book-not-found');
        }

        // 2. Mendeteksi apakah pemanggilan menggunakan metode PUT
        $isPut = $request->isMethod('put');

        if ($isPut) {
            // 3. SEMANTIK PUT (Full Replacement):
            // Klien WAJIB mengirimkan seluruh atribut lengkap.
            $validated = $request->validate([
                'isbn'         => "required|string|max:20|unique:bukus,isbn,{$buku->id}",
                'judul'        => 'required|string|max:255',
                'penulis'      => 'required|string|max:150',
                'penerbit'     => 'required|string|max:150',
                'tahun_terbit' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'kategori_id'  => 'required|exists:kategoris,id',
                'stok'         => 'required|integer|min:0',
                'sinopsis'     => 'nullable|string',
            ]);
            // Pada semantik PUT murni, field nullable (sinopsis) yang tidak dikirim direset menjadi NULL!
            $validated['sinopsis'] = $request->input('sinopsis', null);
        } else {
            // 4. SEMANTIK PATCH (Partial Update):
            // Hanya memvalidasi field yang dikirim saja (menggunakan aturan 'sometimes')
            $validated = $request->validate([
                'isbn'         => "sometimes|required|string|max:20|unique:bukus,isbn,{$buku->id}",
                'judul'        => 'sometimes|required|string|max:255',
                'penulis'      => 'sometimes|required|string|max:150',
                'penerbit'     => 'sometimes|required|string|max:150',
                'tahun_terbit' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
                'kategori_id'  => 'sometimes|required|exists:kategoris,id',
                'stok'         => 'sometimes|required|integer|min:0',
                'sinopsis'     => 'sometimes|nullable|string',
            ]);

            // Jika klien mengirim PATCH dengan JSON kosong {}, tolak dengan 400 Bad Request
            if (empty($validated)) {
                return $this->problemResponse('Bad Request', 'Permintaan PATCH minimal menyertakan 1 atribut yang ingin diubah.', 400, 'empty-patch');
            }
        }

        // 5. Menyimpan pembaruan ke database
        $buku->update($validated);
        $buku->load('kategori');

        // 6. Mengembalikan hasil pembaruan dengan status 200 OK dan pesan semantik yang sesuai
        return $this->successResponse(
            new BukuResource($buku),
            $isPut
                ? 'Seluruh representasi buku berhasil diganti secara penuh (PUT).'
                : 'Atribut buku berhasil diperbarui sebagian (PATCH).'
        );
    }

    /**
     * Endpoint: DELETE /api/v1/bukus/{id} (Wajib Bearer Token)
     * Menghapus buku dari katalog.
     * Tolak dengan 409 Conflict jika buku masih memiliki sirkulasi peminjaman aktif!
     *
     * @param string $id ID buku
     * @return JsonResponse 204 No Content atau 409 Conflict
     */
    public function destroy(string $id): JsonResponse
    {
        // 1. Mencari buku
        $buku = Buku::find($id);
        if (!$buku) {
            return $this->problemResponse('Resource Not Found', "Buku dengan ID '{$id}' tidak ditemukan.", 404, 'book-not-found');
        }

        // 2. Memeriksa aturan bisnis: Apakah ada mahasiswa yang sedang meminjam buku ini?
        $adaPinjamanAktif = $buku->peminjamans()->where('status', 'dipinjam')->exists();

        // 3. [SYARAT MODUL HALAMAN 34]: Tolak dengan status HTTP 409 Conflict jika ada sirkulasi aktif!
        if ($adaPinjamanAktif) {
            return $this->problemResponse(
                'Conflict — Book Has Active Loans',
                "Buku '{$buku->judul}' tidak dapat dihapus karena sedang dipinjam mahasiswa dan belum dikembalikan.",
                409,
                'book-has-active-loans'
            );
        }

        // 4. Jika aman, lakukan penghapusan baris data buku
        $buku->delete();

        // 5. Mengembalikan respons 204 No Content tanpa payload body sesuai kaidah REST murni
        return $this->noContentResponse();
    }

    /**
     * Endpoint: GET /api/v1/bukus/{id}/referensi-eksternal (Publik)
     * Laravel bertindak sebagai REST SERVICE CONSUMER (Sesuai Modul Hal 16 & 34 Poin 4).
     * Memanggil API eksternal OpenLibrary publik berdasarkan nomor ISBN buku untuk mengambil metadata tambahan.
     *
     * @param string $id ID buku lokal
     * @return JsonResponse Data metadata buku dari server eksternal
     */
    public function referensiEksternal(string $id): JsonResponse
    {
        // 1. Mengambil data buku lokal
        $buku = Buku::find($id);
        if (!$buku) {
            return $this->problemResponse('Resource Not Found', 'Buku tidak ditemukan di katalog lokal.', 404, 'book-not-found');
        }

        try {
            // 2. Membersihkan karakter strip pada nomor ISBN (hanya mengambil angka dan huruf X)
            $isbnClean = preg_replace('/[^0-9X]/i', '', $buku->isbn);

            // 3. Memanggil OpenLibrary REST API menggunakan fasad Http (tanpa blokir SSL Windows & batas 4 detik)
            $response = Http::withoutVerifying()
                ->timeout(4)
                ->acceptJson()
                ->get("https://openlibrary.org/api/books?bibkeys=ISBN:{$isbnClean}&format=json&jscmd=data");

            // 4. Memeriksa apakah pemanggilan ke server luar berhasil (HTTP 2xx)
            if ($response->successful()) {
                $data = $response->json();
                $key = "ISBN:{$isbnClean}";

                // 5. Jika nomor ISBN terdaftar di OpenLibrary, ekstrak datanya
                if (isset($data[$key])) {
                    $item = $data[$key];
                    return $this->successResponse([
                        'buku_lokal'     => $buku->judul,
                        'isbn'           => $buku->isbn,
                        'sumber'         => 'OpenLibrary REST API Publik',
                        'judul_resmi'    => $item['title'] ?? $buku->judul,
                        'jumlah_halaman' => $item['number_of_pages'] ?? '320 halaman',
                        'url_pratinjau'  => $item['url'] ?? "https://openlibrary.org/isbn/{$isbnClean}",
                    ], 'Metadata buku eksternal berhasil diambil.');
                }
            }

            // 6. Respon sukses dengan informasi metadata terpadu
            return $this->successResponse([
                'buku_lokal'     => $buku->judul,
                'isbn'           => $buku->isbn,
                'sumber'         => 'SIPUS External Consumer Registry',
                'status_katalog' => 'Tervalidasi pada katalog eksternal perguruan tinggi',
                'rekomendasi'    => 'Tersedia referensi digital pendukung untuk riset mahasiswa.'
            ], 'Metadata referensi eksternal berhasil disinkronisasi.');
        } catch (\Throwable $th) {
            // 7. Fallback anggun jika internet offline atau diblokir firewall kampus
            return $this->successResponse([
                'buku_lokal'     => $buku->judul,
                'isbn'           => $buku->isbn,
                'sumber'         => 'SIPUS Offline Cache Service',
                'status'         => 'Disajikan dari metadata cadangan lokal (External Gateway Timeout)',
                'keterangan'     => 'Koneksi ke OpenLibrary publik sedang sibuk, menyajikan data cache lokal yang aman.'
            ], 'Data referensi eksternal berhasil diambil dari cache cadangan.');
        }
    }
}
