<?php

// Mengimpor Controller Kategori untuk menangani aksi terkait data kategori buku
use App\Http\Controllers\KategoriController;
// Mengimpor Controller Buku untuk menangani aksi terkait data katalog buku perpustakaan
use App\Http\Controllers\BukuController;
// Mengimpor Facade Route sebagai pengelola rute HTTP di Laravel
use Illuminate\Support\Facades\Route;

// Mengarahkan halaman utama (root '/') langsung ke halaman katalog buku (buku.index)
Route::get('/', function () {
    return redirect()->route('buku.index');
});

// Mendaftarkan 7 rute RESTful baku untuk Kategori (index, create, store, show, edit, update, destroy)
Route::resource('kategori', KategoriController::class);

// Mendaftarkan 7 rute RESTful baku untuk Buku (index, create, store, show, edit, update, destroy)
Route::resource('buku', BukuController::class);

