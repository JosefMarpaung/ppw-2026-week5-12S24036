<?php

// Mendeklarasikan namespace model agar dapat dipanggil secara modular oleh komponen lain di Laravel
namespace App\Models;

// Mengimpor trait HasFactory untuk memungkinkan pembuatan data uji (mock data) via Factory
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Mengimpor kelas Model dasar Eloquent (implementasi pola Active Record)
use Illuminate\Database\Eloquent\Model;
// Mengimpor kelas relasi HasMany untuk menghubungkan relasi One-to-Many
use Illuminate\Database\Eloquent\Relations\HasMany;

// Mendefinisikan Model Kategori yang merepresentasikan tabel 'kategoris' di database
class Kategori extends Model
{
    /** 
     * Mengaktifkan trait HasFactory agar model ini terhubung dengan database/factories/KategoriFactory.php
     */
    use HasFactory;

    /**
     * Properti $fillable berfungsi sebagai proteksi Mass Assignment.
     * Hanya atribut yang didaftarkan di sini yang diizinkan untuk diisi secara massal melalui form request.
     */
    protected $fillable = [
        'kode_kategori', // Mengizinkan kolom 'kode_kategori' diisi secara massal
        'nama_kategori', // Mengizinkan kolom 'nama_kategori' diisi secara massal
    ];

    /**
     * Mendefinisikan relasi kardinalitas One-to-Many:
     * Satu Kategori dapat memiliki banyak (HasMany) data Buku.
     *
     * @return HasMany
     */
    public function bukus(): HasMany
    {
        // Mengembalikan relasi bawaan Eloquent bahwa Kategori ini memiliki banyak instansiasi model Buku
        return $this->hasMany(Buku::class);
    }
}
