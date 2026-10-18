<?php

// 1. Menentukan namespace controller di dalam arsitektur Laravel
namespace App\Http\Controllers;

// 2. Mengimpor Model Buku untuk manipulasi stok dan relasi buku
use App\Models\Buku;
// 3. Mengimpor Model Peminjaman untuk pencatatan transaksi sirkulasi
use App\Models\Peminjaman;
// 4. Mengimpor Request untuk menangkap data yang diinput oleh pengguna
use Illuminate\Http\Request;
// 5. Mengimpor Facade DB untuk menjalankan DB::transaction (prinsip ACID)
use Illuminate\Support\Facades\DB;
// 6. Mengimpor Facade Log untuk mencatat log error database (Log::error)
use Illuminate\Support\Facades\Log;
// 7. Mengimpor QueryException untuk menangkap error pada level database
use Illuminate\Database\QueryException;

// 8. Mendefinisikan kelas PeminjamanController turunan dari Controller dasar
class PeminjamanController extends Controller
{
    /**
     * 9. INDEX: Menampilkan daftar riwayat seluruh transaksi peminjaman buku
     */
    public function index()
    {
        // 10. Eager Loading relasi 'buku' untuk mencegah N+1 Query Problem, diurutkan dari yang terbaru
        $peminjamans = Peminjaman::with('buku')->latest()->paginate(10);

        // 11. Menampilkan view index peminjaman membawa data $peminjamans
        return view('peminjaman.index', compact('peminjamans'));
    }

    /**
     * 12. CREATE: Menampilkan formulir transaksi peminjaman buku
     */
    public function create(Request $request)
    {
        // 13. [KRITIS] Mengambil SELURUH buku (termasuk stok 0) agar penguji bisa membuktikan penolakan transaksi ACID
        $bukus = Buku::orderBy('judul')->get();
        
        // 14. Menangkap ID buku jika pengguna menekan tombol "Pinjam" langsung dari katalog buku
        $selectedBukuId = $request->query('buku_id');

        // 15. Merender formulir peminjaman membawa koleksi seluruh buku dan buku yang terpilih
        return view('peminjaman.create', compact('bukus', 'selectedBukuId'));
    }

    /**
     * 16. STORE: Memproses Transaksi Peminjaman secara Atomik (ACID + lockForUpdate)
     * Sesuai Spesifikasi Teknis Wajib Poin 2 Modul Praktikum
     */
    public function store(Request $request)
    {
        // 17. Validasi ketat seluruh input data dari formulir peminjaman
        $validated = $request->validate([
            'buku_id'             => 'required|exists:bukus,id',       // Buku wajib ada di database
            'nim_peminjam'        => 'required|string|min:6|max:20',   // NIM peminjam wajib diisi
            'nama_peminjam'       => 'required|string|min:3|max:150',  // Nama peminjam wajib diisi
            'tanggal_pinjam'      => 'required|date',                  // Tanggal mulai pinjam
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam', // Tenggat setelah pinjam
        ]);

        // 18. Blok penanganan eksepsi untuk menjamin integritas transaksi atomik
        try {
            // 19. Membungkus seluruh operasi ke dalam DB::transaction dengan toleransi coba ulang 3x
            DB::transaction(function () use ($validated) {

                // 20. [KRITIS MODUL] Mengunci baris data buku dengan lockForUpdate() agar antrean query lain menunggu
                $buku = Buku::where('id', $validated['buku_id'])->lockForUpdate()->firstOrFail();

                // 21. [KRITIS MODUL] Validasi Stok: Tolak dan rollback jika stok habis (stok = 0)
                if ($buku->stok < 1) {
                    throw new \RuntimeException("Stok buku '{$buku->judul}' telah habis (0), peminjaman dibatalkan!");
                }

                // 22. Validasi Duplikasi: Cegah NIM yang sama meminjam buku yang sama jika belum dikembalikan
                $sedangPinjam = Peminjaman::where('buku_id', $buku->id)
                    ->where('nim_peminjam', $validated['nim_peminjam'])
                    ->where('status', 'dipinjam')
                    ->exists();

                // 23. Jika mahasiswa tersebut masih meminjam buku ini, lempar error dan rollback
                if ($sedangPinjam) {
                    throw new \RuntimeException("Mahasiswa dengan NIM {$validated['nim_peminjam']} masih meminjam buku ini dan belum mengembalikannya!");
                }

                // 24. [KRITIS MODUL] Mutasi Stok Atomik: Kurangi stok buku sebesar 1
                $buku->decrement('stok');

                // 25. Generate Kode Pinjam Unik secara otomatis (Contoh: PJ-20261018-A1B2)
                $kodePinjam = 'PJ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

                // 26. Mencatat riwayat transaksi ke tabel peminjamans
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

            // 27. Jika transaksi sukses tanpa error, arahkan ke daftar peminjaman dengan notifikasi sukses
            return redirect()->route('peminjaman.index')->with('success', 'Buku berhasil dipinjam secara atomik (ACID)!');

        } catch (\RuntimeException $e) {
            // 28. Menangkap penolakan logika bisnis (stok habis / belum kembalikan buku) dan rollback otomatis
            return back()->withInput()->with('error', $e->getMessage());

        } catch (QueryException $e) {
            // 29. [KRITIS MODUL] Tangkap QueryException dan catat error database ke file log
            Log::error('[DB EXCEPTION PEMINJAMAN]: ' . $e->getMessage());
            // 30. Kembalikan respons error ramah pengguna
            return back()->withInput()->with('error', 'Terjadi kesalahan basis data saat memproses peminjaman.');
        }
    }

    /**
     * 31. KEMBALIKAN: Memproses Transaksi Pengembalian Buku secara Atomik (ACID + lockForUpdate)
     * Sesuai Spesifikasi Teknis Wajib Poin 2 Modul Praktikum
     */
    public function kembalikan(int $id)
    {
        // 32. Blok try-catch pengembalian buku
        try {
            // 33. Membungkus seluruh proses pengembalian dalam DB::transaction
            DB::transaction(function () use ($id) {

                // 34. [KRITIS MODUL] Kunci data transaksi peminjaman dengan lockForUpdate()
                $peminjaman = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

                // 35. Validasi status: pastikan buku belum pernah dikembalikan sebelumnya
                if ($peminjaman->status === 'dikembalikan') {
                    throw new \RuntimeException('Buku pada transaksi ini sudah pernah dikembalikan sebelumnya!');
                }

                // 36. [KRITIS MODUL] Kunci data buku dan pulihkan (tambah) stoknya secara atomik
                $buku = Buku::where('id', $peminjaman->buku_id)->lockForUpdate()->firstOrFail();
                $buku->increment('stok');

                // 37. Ubah status transaksi menjadi 'dikembalikan' dan catat tanggal kembalinya hari ini
                $peminjaman->update([
                    'status'          => 'dikembalikan',
                    'tanggal_kembali' => now()->toDateString(),
                ]);
            }, 3);

            // 38. Jika sukses, kembali ke halaman sebelumnya dengan notifikasi sukses
            return back()->with('success', 'Buku berhasil dikembalikan dan stok telah dipulihkan secara atomik!');

        } catch (\RuntimeException $e) {
            // 39. Menangkap kesalahan logika bisnis
            return back()->with('error', $e->getMessage());

        } catch (QueryException $e) {
            // 40. [KRITIS MODUL] Tangkap QueryException dan catat ke Log::error()
            Log::error('[DB EXCEPTION PENGEMBALIAN]: ' . $e->getMessage());
            // 41. Kembalikan respons gagal
            return back()->with('error', 'Terjadi kesalahan basis data saat mengembalikan buku.');
        }
    }
}