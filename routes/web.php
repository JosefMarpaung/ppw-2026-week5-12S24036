<?php

// Mengimpor Controller Kategori untuk rute manajemen kategori buku
use App\Http\Controllers\KategoriController;
// Mengimpor Controller Buku untuk rute katalog buku
use App\Http\Controllers\BukuController;
// Mengimpor Controller Peminjaman untuk transaksi sirkulasi buku (ACID)
use App\Http\Controllers\PeminjamanController;
// Mengimpor Facade Route sebagai pengatur rute HTTP di Laravel
use Illuminate\Support\Facades\Route;

// 1. Rute Beranda: Mengarahkan pengunjung root ('/') langsung ke katalog buku
Route::get('/', function () {
    return redirect()->route('buku.index');
});

// 2. Rute RESTful Kategori Buku (index, create, store, show, edit, update, destroy)
Route::resource('kategori', KategoriController::class);

// 3. Rute RESTful Buku (index, create, store, show, edit, update, destroy)
Route::resource('buku', BukuController::class);

// 4. Rute Transaksi Sirkulasi Peminjaman Buku (Sesuai Modul Poin 2)
// Menampilkan riwayat peminjaman
Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
// Menampilkan form peminjaman buku
Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
// Memproses aksi peminjaman secara atomik (ACID)
Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
// Memproses pengembalian buku secara atomik (ACID + lockForUpdate)
Route::patch('/peminjaman/{id}/kembalikan', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');