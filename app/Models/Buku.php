<?php

// Mendeklarasikan namespace model agar dapat dipanggil secara modular oleh komponen lain di Laravel
namespace App\Models;

// Mengimpor trait HasFactory untuk memungkinkan pembuatan data uji (mock data) via Factory
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Mengimpor kelas Model dasar Eloquent (implementasi pola Active Record)
use Illuminate\Database\Eloquent\Model;
// Mengimpor kelas relasi BelongsTo untuk menghubungkan relasi kebalikan (inverse) One-to-Many
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Mendefinisikan relasi kebalikan (inverse) One-to-Many:
     * Setiap Buku pasti dimiliki oleh (BelongsTo) tepat satu Kategori.
     *
     * @return BelongsTo
     */
    public function kategori(): BelongsTo
    {
        // Mengembalikan relasi bawaan Eloquent bahwa Buku ini terikat ke Model Kategori induknya
        return $this->belongsTo(Kategori::class);
    }
}
