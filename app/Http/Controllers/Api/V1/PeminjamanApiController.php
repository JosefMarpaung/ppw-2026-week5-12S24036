<?php

// 1. Menentukan namespace controller sirkulasi peminjaman API versi 1
namespace App\Http\Controllers\Api\V1;

// 2. Mengimpor controller dasar Laravel
use App\Http\Controllers\Controller;
// 3. Mengimpor PeminjamanResource untuk format representasi JSON dan kontrol link dinamis
use App\Http\Resources\Api\V1\PeminjamanResource;
// 4. Mengimpor Model Buku untuk manipulasi stok fisik
use App\Models\Buku;
// 5. Mengimpor Model Peminjaman
use App\Models\Peminjaman;
// 6. Mengimpor Trait ApiResponse untuk keseragaman amplop respons JSON
use App\Traits\ApiResponse;
// 7. Mengimpor pustaka Carbon untuk manipulasi tanggal transaksi
use Carbon\Carbon;
// 8. Mengimpor JsonResponse
use Illuminate\Http\JsonResponse;
// 9. Mengimpor Request HTTP
use Illuminate\Http\Request;
// 10. Mengimpor fasad DB untuk transaksi ACID (Atomicity, Consistency, Isolation, Durability)
use Illuminate\Support\Facades\DB;

/**
 * Kelas PeminjamanApiController mengelola Transaksi Sirkulasi Peminjaman & Pengembalian Buku.
 * Memenuhi Spesifikasi Teknis Wajib (Modul Hal 25–27 & 34 Poin 3):
 *   - Menjamin sifat ACID menggunakan DB::transaction().
 *   - Menangkal Race Condition menggunakan Pessimistic Locking (lockForUpdate()).
 *   - Menolak peminjaman jika stok habis (422 Unprocessable Content).
 *   - Menolak duplikasi peminjaman aktif oleh mahasiswa yang sama (409 Conflict).
 *   - Menangani pengembalian buku dan pemulihan stok (+1) secara atomik (PATCH).
 */
class PeminjamanApiController extends Controller
{
    // 1. Mengaktifkan trait ApiResponse
    use ApiResponse;

    /**
     * Endpoint: GET /api/v1/peminjamans (Wajib Bearer Token)
     * Menampilkan riwayat transaksi sirkulasi peminjaman terpaginasi.
     *
     * @param Request $request Menampung filter ?status=... dan ?nim=...
     * @return JsonResponse Koleksi sirkulasi terpaginasi
     */
    public function index(Request $request): JsonResponse
    {
        // 1. Membangun kueri Eloquent dengan Eager Loading relasi 'buku'
        $paginator = Peminjaman::query()
            ->with('buku')
            // 2. Filter berdasarkan status sirkulasi ('dipinjam' atau 'dikembalikan')
            ->when($request->query('status'), fn($q, $st) => $q->where('status', $st))
            // 3. Filter berdasarkan NIM mahasiswa peminjam
            ->when($request->query('nim'), fn($q, $nim) => $q->where('nim_peminjam', $nim))
            // 4. Urutkan berdasarkan transaksi terbaru
            ->orderBy('id', 'desc')
            // 5. Paginasi 10 transaksi per halaman
            ->paginate(10)
            ->withQueryString();

        // 6. Mengembalikan respons 200 OK dibungkus PeminjamanResource
        return $this->successResponse(
            PeminjamanResource::collection($paginator->items()),
            'Daftar transaksi peminjaman berhasil diambil.'
        );
    }

    /**
     * Endpoint: POST /api/v1/peminjamans (Wajib Bearer Token)
     * Mencatat transaksi peminjaman buku baru dengan jaminan ACID + Pessimistic Locking.
     *
     * @param Request $request Membawa buku_id, nim_peminjam, nama_peminjam, durasi_hari
     * @return JsonResponse 201 Created jika sukses, 409 Conflict, atau 422 Unprocessable
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Memvalidasi payload input JSON
        $validated = $request->validate([
            'buku_id'       => 'required|exists:bukus,id',
            'nim_peminjam'  => 'required|string|max:15',
            'nama_peminjam' => 'required|string|max:150',
            'durasi_hari'   => 'nullable|integer|min:1|max:30',
        ]);

        // 2. [SYARAT MUTLAK ACID]: Wajib dibungkus di dalam closure DB::transaction()
        return DB::transaction(function () use ($validated) {

            // 3. [PESSIMISTIC LOCKING]: Mengunci baris buku secara eksklusif (SELECT ... FOR UPDATE)
            // Ini mencegah dua mahasiswa meminjam 1 buku terakhir secara bersamaan (Race Condition)!
            $buku = Buku::where('id', $validated['buku_id'])->lockForUpdate()->first();

            // 4. Memeriksa keberadaan buku
            if (!$buku) {
                return $this->problemResponse('Resource Not Found', 'Buku tidak ditemukan.', 404, 'book-not-found');
            }

            // 5. [ATURAN BISNIS]: Cek apakah mahasiswa masih meminjam buku yang sama dan belum dikembalikan -> 409 Conflict
            $sudahPinjam = Peminjaman::where('buku_id', $buku->id)
                ->where('nim_peminjam', $validated['nim_peminjam'])
                ->where('status', 'dipinjam')
                ->exists();

            if ($sudahPinjam) {
                // Sesuai modul: Wajib ditolak dengan 409 Conflict (Konflik State Bisnis)
                return $this->problemResponse(
                    'Duplicate Active Loan Conflict',
                    "Mahasiswa dengan NIM '{$validated['nim_peminjam']}' masih meminjam buku ini dan belum mengembalikannya.",
                    409,
                    'duplicate-loan'
                );
            }

            // 6. [ATURAN BISNIS]: Cek ketersediaan sisa stok fisik buku -> 422 Unprocessable Content jika habis (< 1)
            if ($buku->stok < 1) {
                // Sesuai modul: Wajib ditolak dengan 422 Unprocessable Content
                return $this->problemResponse(
                    'Book Stock Exhausted',
                    "Maaf, stok untuk buku '{$buku->judul}' sedang habis (0 tersisa).",
                    422,
                    'stock-exhausted'
                );
            }

            // 7. Mengurangi stok fisik buku (-1) secara atomik
            $buku->decrement('stok');

            // 8. Menghitung tanggal pinjam hari ini dan batas waktu jatuh tempo
            $durasi = $validated['durasi_hari'] ?? 7;
            $tglPinjam = Carbon::today();
            $tglTempo  = Carbon::today()->addDays($durasi);

            // 9. Membuat baris data transaksi peminjaman baru di tabel peminjamans
            $peminjaman = Peminjaman::create([
                'kode_pinjam'         => 'PINJ-' . strtoupper(uniqid()),
                'buku_id'             => $buku->id,
                'nim_peminjam'        => strtoupper(trim($validated['nim_peminjam'])),
                'nama_peminjam'       => $validated['nama_peminjam'],
                'tanggal_pinjam'      => $tglPinjam->toDateString(),
                'tanggal_jatuh_tempo' => $tglTempo->toDateString(),
                'status'              => 'dipinjam',
            ]);

            // 10. Mengembalikan respons 201 Created lengkap dengan Header Location dan BukuResource
            return $this->createdResponse(
                new PeminjamanResource($peminjaman),
                url("/api/v1/peminjamans/{$peminjaman->id}"),
                "Peminjaman buku '{$buku->judul}' berhasil dicatat. Sisa stok kini: {$buku->fresh()->stok}."
            );
        });
    }

    /**
     * Endpoint: PATCH /api/v1/peminjamans/{id}/kembalikan (Wajib Bearer Token)
     * Memproses pengembalian buku dan mengembalikan stok fisik (+1) secara atomik.
     *
     * @param string $id ID transaksi peminjaman
     * @return JsonResponse Respons 200 OK atau 409 Conflict
     */
    public function kembalikan(string $id): JsonResponse
    {
        // 1. Dibungkus transaksi ACID
        return DB::transaction(function () use ($id) {
            // 2. Mencari data transaksi sirkulasi
            $peminjaman = Peminjaman::find($id);

            // 3. Validasi keberadaan data transaksi
            if (!$peminjaman) {
                return $this->problemResponse('Resource Not Found', 'Data transaksi peminjaman tidak ditemukan.', 404, 'loan-not-found');
            }

            // 4. [ATURAN BISNIS]: Tolak jika buku sudah berstatus 'dikembalikan' sebelumnya
            if ($peminjaman->status === 'dikembalikan') {
                return $this->problemResponse(
                    'Conflict — Already Returned',
                    'Buku pada transaksi ini sudah pernah dikembalikan sebelumnya.',
                    409,
                    'already-returned'
                );
            }

            // 5. Kunci baris buku dengan lockForUpdate() dan tambahkan kuantitas stok (+1) secara atomik
            $buku = Buku::where('id', $peminjaman->buku_id)->lockForUpdate()->first();
            if ($buku) {
                $buku->increment('stok');
            }

            // 6. Memperbarui status sirkulasi menjadi 'dikembalikan' dan mencatat tanggal kembalinya
            $peminjaman->update([
                'status'          => 'dikembalikan',
                'tanggal_kembali' => Carbon::today()->toDateString(),
            ]);

            // 7. Mengembalikan respons 200 OK dengan PeminjamanResource terbaru
            return $this->successResponse(
                new PeminjamanResource($peminjaman),
                "Buku '{$buku?->judul}' berhasil dikembalikan. Stok fisik kini bertambah menjadi: {$buku?->fresh()->stok}."
            );
        });
    }
}
