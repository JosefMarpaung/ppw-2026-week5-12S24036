<?php

// Mengimpor kelas Migration sebagai fondasi utama pembuatan skema tabel di Laravel
use Illuminate\Database\Migrations\Migration;
// Mengimpor Blueprint yang berfungsi mendefinisikan struktur kolom, tipe data, dan indeks tabel
use Illuminate\Database\Schema\Blueprint;
// Mengimpor Facade Schema untuk mengeksekusi manipulasi skema basis data
use Illuminate\Support\Facades\Schema;

// Mengembalikan anonymous migration class (standar Laravel modern untuk mencegah konflik nama class)
return new class extends Migration
{
    /**
     * Menjalankan operasi migrasi untuk membuat tabel baru di basis data.
     */
    public function up(): void
    {
        // Memerintahkan Schema builder untuk membuat tabel baru bernama 'kategoris'
        Schema::create('kategoris', function (Blueprint $table) {
            // Kolom 'id': Primary Key bertipe Big Integer Auto-Increment untuk identitas unik setiap baris
            $table->id();

            // Kolom 'kode_kategori': Tipe VARCHAR(20) dengan modifier UNIQUE agar tidak boleh ada kode kembar
            $table->string('kode_kategori', 20)->unique();

            // Kolom 'nama_kategori': Tipe VARCHAR(100) untuk menampung nama lengkap kelompok/kategori buku
            $table->string('nama_kategori', 100);

            // Kolom bawaan 'created_at' dan 'updated_at' bertipe TIMESTAMP untuk audit waktu pembuatan & pembaruan data
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi (rollback) jika skema tabel ingin dikembalikan ke kondisi sebelumnya.
     */
    public function down(): void
    {
        // Menghapus tabel 'kategoris' dari basis data jika tabel tersebut memang ada
        Schema::dropIfExists('kategoris');
    }
};
