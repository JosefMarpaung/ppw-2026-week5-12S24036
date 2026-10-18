<?php

// Menentukan namespace lokasi berkas model ini
namespace App\Models;

// Mengimpor kelas Model dasar milik Laravel
use Illuminate\Database\Eloquent\Model;
// Mengimpor kelas relasi BelongsTo untuk menghubungkan ke tabel induk (Buku)
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    // 1. Menentukan nama tabel secara manual agar Laravel mencari tabel 'peminjamans' (bukan peminjamen)
    protected $table = 'peminjamans';

    // 2. Mendaftarkan kolom mana saja yang boleh diisi datanya oleh aplikasi (Mass Assignment Protection)
    protected $fillable = [
        'kode_pinjam',         // Izin isi kolom kode transaksi pinjam
        'buku_id',             // Izin isi ID buku yang dipinjam
        'nim_peminjam',        // Izin isi NIM mahasiswa peminjam
        'nama_peminjam',       // Izin isi nama mahasiswa peminjam
        'tanggal_pinjam',      // Izin isi tanggal mulai pinjam
        'tanggal_jatuh_tempo', // Izin isi batas akhir pengembalian
        'tanggal_kembali',     // Izin isi tanggal pengembalian
        'status',              // Izin isi status ('dipinjam' atau 'dikembalikan')
    ];

    /**
     * 3. Relasi Eloquent: Setiap 1 data transaksi Peminjaman pasti memiliki (BelongsTo) tepat 1 Buku
     */
    public function buku(): BelongsTo
    {
        // Menghubungkan model Peminjaman kembali ke model Buku
        return $this->belongsTo(Buku::class);
    }
}