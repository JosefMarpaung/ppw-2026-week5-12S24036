<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PeminjamanResource;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanApiController extends Controller
{
    use ApiResponse;

    // 1. GET /api/v1/peminjamans (Riwayat peminjaman)
    public function index(Request $request): JsonResponse
    {
        $paginator = Peminjaman::query()
            ->with('buku')
            ->when($request->query('status'), fn($q, $st) => $q->where('status', $st))
            ->when($request->query('nim'), fn($q, $nim) => $q->where('nim_peminjam', $nim))
            ->orderBy('id', 'desc')
            ->paginate(10);

        return $this->successResponse(PeminjamanResource::collection($paginator->items()), 'Data peminjaman diambil.');
    }

    // 2. POST /api/v1/peminjamans (Transaksi ACID + lockForUpdate)
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'buku_id'       => 'required|exists:bukus,id',
            'nim_peminjam'  => 'required|string|max:15',
            'nama_peminjam' => 'required|string|max:150',
            'durasi_hari'   => 'nullable|integer|min:1|max:30',
        ]);

        return DB::transaction(function () use ($validated) {
            // 3. Kunci baris buku secara pesimistik (SELECT ... FOR UPDATE)
            $buku = Buku::where('id', $validated['buku_id'])->lockForUpdate()->first();

            // 4. Tolak 409 Conflict jika mahasiswa masih meminjam buku yang sama
            if (Peminjaman::where('buku_id', $buku->id)->where('nim_peminjam', $validated['nim_peminjam'])->where('status', 'dipinjam')->exists()) {
                return $this->problemResponse('Duplicate Loan Conflict', 'Mahasiswa masih meminjam buku ini.', 409, 'duplicate-loan');
            }

            // 5. Tolak 422 Unprocessable Content jika stok habis
            if ($buku->stok < 1) {
                return $this->problemResponse('Stock Exhausted', 'Stok buku habis (0).', 422, 'stock-exhausted');
            }

            // 6. Kurangi stok secara atomik
            $buku->decrement('stok');

            $peminjaman = Peminjaman::create([
                'kode_pinjam'         => 'PINJ-' . strtoupper(uniqid()),
                'buku_id'             => $buku->id,
                'nim_peminjam'        => strtoupper(trim($validated['nim_peminjam'])),
                'nama_peminjam'       => $validated['nama_peminjam'],
                'tanggal_pinjam'      => Carbon::today()->toDateString(),
                'tanggal_jatuh_tempo' => Carbon::today()->addDays($validated['durasi_hari'] ?? 7)->toDateString(),
                'status'              => 'dipinjam',
            ]);

            return $this->createdResponse(new PeminjamanResource($peminjaman), url("/api/v1/peminjamans/{$peminjaman->id}"), 'Peminjaman berhasil dicatat.');
        });
    }

    // 7. PATCH /api/v1/peminjamans/{id}/kembalikan (Pengembalian buku ACID)
    public function kembalikan(string $id): JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $peminjaman = Peminjaman::find($id);
            if (!$peminjaman) { return $this->problemResponse('Resource Not Found', 'Data peminjaman tidak ditemukan.', 404, 'not-found'); }

            // 8. Tolak 409 jika buku sudah dikembalikan sebelumnya
            if ($peminjaman->status === 'dikembalikan') {
                return $this->problemResponse('Conflict — Already Returned', 'Buku ini sudah dikembalikan.', 409, 'already-returned');
            }

            // 9. Kunci baris buku dan tambahkan stok kembali (+1)
            $buku = Buku::where('id', $peminjaman->buku_id)->lockForUpdate()->first();
            if ($buku) { $buku->increment('stok'); }

            $peminjaman->update(['status' => 'dikembalikan', 'tanggal_kembali' => Carbon::today()->toDateString()]);

            return $this->successResponse(new PeminjamanResource($peminjaman), 'Buku berhasil dikembalikan.');
        });
    }
}