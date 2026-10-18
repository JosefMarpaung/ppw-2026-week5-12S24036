<?php

namespace App\Http\Controllers;

// Mengimpor Model Buku untuk manipulasi stok dan relasi buku
use App\Models\Buku;
// Mengimpor Model Peminjaman untuk pencatatan transaksi sirkulasi
use App\Models\Peminjaman;
// Mengimpor Request untuk menangkap data yang diinput oleh pengguna
use Illuminate\Http\Request;
// Mengimpor Facade DB untuk menjalankan DB::transaction (prinsip ACID)
use Illuminate\Support\Facades\DB;
// Mengimpor Facade Log untuk mencatat log error database (Log::error)
use Illuminate\Support\Facades\Log;
// Mengimpor QueryException untuk menangkap error pada level database
use Illuminate\Database\QueryException;

class PeminjamanController extends Controller
{
    /**
     * 1. INDEX: Menampilkan daftar riwayat seluruh transaksi peminjaman buku
     */
    public function index()
    {
        // Eager Loading relasi 'buku' untuk mencegah N+1 Query Problem, diurutkan dari yang terbaru
        $peminjamans = Peminjaman::with('buku')->latest()->paginate(10);

        // Menampilkan view index peminjaman membawa data $peminjamans
        return view('peminjaman.index', compact('peminjamans'));
    }

    /**
     * 2. CREATE: Menampilkan formulir transaksi peminjaman buku
     */
    public function create(Request $request)
    {
        // Mengambil seluruh buku yang stoknya lebih dari 0 untuk dipilih pada formulir
        $bukus = Buku::where('stok', '>', 0)->orderBy('judul')->get();
        // Menangkap ID buku jika pengguna menekan tombol "Pinjam" langsung dari katalog
        $selectedBukuId = $request->query('buku_id');

        // Merender formulir peminjaman
        return view('peminjaman.create', compact('bukus', 'selectedBukuId'));
    }

    /**
     * 3. STORE: Memproses Transaksi Peminjaman secara Atomik (ACID + lockForUpdate)
     * Sesuai Spesifikasi Teknis Wajib Poin 2 Modul Praktikum
     */
    public function store(Request $request)
    {
        // Validasi input data dari formulir peminjaman
        $validated = $request->validate([
            'buku_id'             => 'required|exists:bukus,id',       // Buku wajib ada di database
            'nim_peminjam'        => 'required|string|min:6|max:20',   // NIM peminjam wajib diisi
            'nama_peminjam'       => 'required|string|min:3|max:150',  // Nama peminjam wajib diisi
            'tanggal_pinjam'      => 'required|date',                  // Tanggal mulai pinjam
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam', // Tenggat harus setelah pinjam
        ]);

        try {
            // Membungkus seluruh operasi ke dalam DB::transaction dengan toleransi coba ulang 3x (Deadlock Retry)
            DB::transaction(function () use ($validated) {

                // [KRITIS] Mengunci baris data buku dengan lockForUpdate() agar antrean query lain menunggu
                $buku = Buku::where('id', $validated['buku_id'])->lockForUpdate()->firstOrFail();

                // 1. Validasi Stok: Tolak dan rollback jika stok habis (stok = 0)
                if ($buku->stok < 1) {
                    throw new \RuntimeException("Stok buku '{$buku->judul}' telah habis (0), peminjaman dibatalkan!");
                }

                // 2. Validasi Duplikasi: Cegah NIM yang sama meminjam buku yang sama jika belum dikembalikan
                $sedangPinjam = Peminjaman::where('buku_id', $buku->id)
                    ->where('nim_peminjam', $validated['nim_peminjam'])
                    ->where('status', 'dipinjam')
                    ->exists();

                if ($sedangPinjam) {
                    throw new \RuntimeException("Mahasiswa dengan NIM {$validated['nim_peminjam']} masih meminjam buku ini dan belum mengembalikannya!");
                }

                // 3. Mutasi Stok Atomik: Kurangi stok buku sebesar 1
                $buku->decrement('stok');

                // 4. Generate Kode Pinjam Unik (Contoh: PJ-20261018-A1B2)
                $kodePinjam = 'PJ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

                // 5. Catat transaksi ke tabel peminjamans
                Peminjaman::create([
                    'kode_pinjam'         => $kodePinjam,
                    'buku_id'             => $buku->id,
                    'nim_peminjam'        => strtoupper(trim($validated['nim_peminjam'])),
                    'nama_peminjam'       => $validated['nama_peminjam'],
                    'tanggal_pinjam'      => $validated['tanggal_pinjam'],
                    'tanggal_jatuh_tempo' => $validated['tanggal_jatuh_tempo'],
                    'status'              => 'dipinjam',
                ]);
            }, 3);

            // Jika transaksi sukses tanpa error, arahkan ke daftar peminjaman dengan notifikasi sukses
            return redirect()->route('peminjaman.index')->with('success', 'Buku berhasil dipinjam secara atomik (ACID)!');

        } catch (\RuntimeException $e) {
            // Menangkap penolakan logika bisnis (stok habis / belum kembalikan buku) dan rollback otomatis
            return back()->withInput()->with('error', $e->getMessage());

        } catch (QueryException $e) {
            // [KRITIS] Sesuai Modul: Tangkap QueryException dan catat error database ke file log
            Log::error('[DB EXCEPTION PEMINJAMAN]: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan basis data saat memproses peminjaman.');
        }
    }

    /**
     * 4. KEMBALIKAN: Memproses Transaksi Pengembalian Buku secara Atomik (ACID + lockForUpdate)
     * Sesuai Spesifikasi Teknis Wajib Poin 2 Modul Praktikum
     */
    public function kembalikan(int $id)
    {
        try {
            // Membungkus seluruh proses pengembalian dalam DB::transaction
            DB::transaction(function () use ($id) {

                // [KRITIS] Kunci data peminjaman dengan lockForUpdate()
                $peminjaman = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

                // Validasi status: pastikan buku belum pernah dikembalikan sebelumnya
                if ($peminjaman->status === 'dikembalikan') {
                    throw new \RuntimeException('Buku pada transaksi ini sudah pernah dikembalikan sebelumnya!');
                }

                // [KRITIS] Kunci data buku dan pulihkan (tambah) stoknya secara atomik
                $buku = Buku::where('id', $peminjaman->buku_id)->lockForUpdate()->firstOrFail();
                $buku->increment('stok');

                // Ubah status transaksi menjadi 'dikembalikan' dan catat tanggal kembalinya hari ini
                $peminjaman->update([
                    'status'          => 'dikembalikan',
                    'tanggal_kembali' => now()->toDateString(),
                ]);
            }, 3);

            // Jika sukses, kembali dengan notifikasi sukses
            return back()->with('success', 'Buku berhasil dikembalikan dan stok telah dipulihkan secara atomik!');

        } catch (\RuntimeException $e) {
            // Menangkap kesalahan logika bisnis
            return back()->with('error', $e->getMessage());

        } catch (QueryException $e) {
            // [KRITIS] Sesuai Modul: Tangkap QueryException dan catat ke Log::error()
            Log::error('[DB EXCEPTION PENGEMBALIAN]: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan basis data saat mengembalikan buku.');
        }
    }
}