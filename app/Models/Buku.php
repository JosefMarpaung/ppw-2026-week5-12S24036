<?php

// Mendeklarasikan namespace model agar dapat dipanggil secara modular oleh komponen lain di Laravel
namespace App\Models;

// Mengimpor trait HasFactory untuk memungkinkan pembuatan data uji (mock data) via Factory
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Mengimpor kelas Model dasar Eloquent (implementasi pola Active Record)
use Illuminate\Database\Eloquent\Model;
// Mengimpor kelas relasi BelongsTo untuk menghubungkan relasi kebalikan (inverse) One-to-Many
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// Mengimpor kelas relasi HasMany untuk relasi 1 Buku memiliki banyak Peminjaman
use Illuminate\Database\Eloquent\Relations\HasMany;

// Mendefinisikan Model Buku yang merepresentasikan tabel 'bukus' di database
class Buku extends Model
{
    /**
     * Mengaktifkan trait HasFactory agar model ini terhubung dengan database/factories/BukuFactory.php
     */
    use HasFactory;

    /**
     * Properti $fillable berfungsi sebagai proteksi Mass Assignment.
     * Hanya atribut yang didaftarkan di dalam array ini yang diizinkan untuk diisi melalui form request.
     */
    protected $fillable = [
        'isbn',         // Mengizinkan nomor ISBN buku diisi secara massal
        'judul',        // Mengizinkan judul buku diisi secara massal
        'penulis',      // Mengizinkan nama penulis buku diisi secara massal
        'penerbit',     // Mengizinkan nama penerbit buku diisi secara massal
        'tahun_terbit', // Mengizinkan angka tahun terbit buku diisi secara massal
        'kategori_id',  // Mengizinkan ID kategori (Foreign Key) diisi secara massal
        'stok',         // Mengizinkan jumlah stok buku diisi secara massal
        'sinopsis',     // Mengizinkan teks sinopsis buku diisi secara massal
    ];

    /**
     * Relasi One-to-Many ke Kategori:
     * Setiap Buku pasti dimiliki oleh (BelongsTo) tepat satu Kategori.
     */
    public function kategori(): BelongsTo
    {
        // Mengembalikan relasi bahwa Buku ini terikat ke Model Kategori induknya
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Relasi One-to-Many ke Peminjaman (Sesuai Modul Praktikum Halaman 6 Poin 1):
     * Satu Buku dapat dipinjam berkali-kali / memiliki banyak (HasMany) riwayat Peminjaman.
     */
    public function peminjamans(): HasMany
    {
        // Menghubungkan Buku ke seluruh riwayat di tabel peminjamans
        return $this->hasMany(Peminjaman::class);
    }
}