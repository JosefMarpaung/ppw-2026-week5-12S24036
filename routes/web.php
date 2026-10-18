<?php

// 1. Mengimpor AuthController untuk mengelola proses autentikasi (login, logout, sesi, cookie)
use App\Http\Controllers\AuthController;
// 2. Mengimpor BukuController untuk mengelola rute katalog dan manipulasi data buku
use App\Http\Controllers\BukuController;
// 3. Mengimpor KategoriController untuk mengelola rute data kategori buku perpustakaan
use App\Http\Controllers\KategoriController;
// 4. Mengimpor PeminjamanController untuk mengelola rute transaksi peminjaman & pengembalian ACID
use App\Http\Controllers\PeminjamanController;
// 5. Mengimpor Facade Route sebagai pengatur seluruh rute HTTP aplikasi Laravel
use Illuminate\Support\Facades\Route;

// 6. Rute utama root ('/') yang langsung mengarahkan pengunjung ke halaman katalog buku
Route::get('/', function () {
    // 7. Mengalihkan (redirect) ke rute bernama 'buku.index'
    return redirect()->route('buku.index');
});

// 8. RUTE PUBLIK: Menampilkan katalog buku & statistik (bisa dilihat siapa saja tanpa login)
Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
// 9. RUTE PUBLIK: Menampilkan detail 1 buku (bisa dilihat siapa saja tanpa login)
Route::get('/buku/{buku}', [BukuController::class, 'show'])->name('buku.show');
// 10. Grup rute khusus tamu yang belum login (middleware 'guest')
Route::middleware('guest')->group(function () {
    // 11. Menampilkan halaman formulir login pustakawan
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    // 12. Memproses data formulir login saat tombol submit ditekan (metode POST)
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
});

// 13. [KRITIS MODUL] Grup rute mutasi yang WAJIB diproteksi middleware 'auth' (hanya untuk pustakawan yang login)
Route::middleware('auth')->group(function () {
    // 14. Rute logout pustakawan untuk menghancurkan sesi server secara aman
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // 15. Rute operasi mutasi buku (create, store, edit, update, destroy) - kecuali index dan show yang sudah publik
    Route::resource('buku', BukuController::class)->except(['index', 'show']);
    // 16. Rute manajemen kategori buku penuh (hanya pustakawan yang boleh kelola kategori)
    Route::resource('kategori', KategoriController::class);
    // 17. Menampilkan daftar sirkulasi riwayat peminjaman buku
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    // 18. Menampilkan formulir pencatatan peminjaman buku baru
    Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    // 19. Memproses transaksi peminjaman buku baru dengan transaksi ACID + lockForUpdate
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    // 20. Memproses pengembalian buku dan pemulihan stok secara atomik
    Route::patch('/peminjaman/{id}/kembalikan', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
});