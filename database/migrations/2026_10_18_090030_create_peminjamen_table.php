<?php

// Mengimpor kelas Migration dasar dari framework Laravel
use Illuminate\Database\Migrations\Migration;
// Mengimpor kelas Blueprint untuk merancang struktur kolom tabel
use Illuminate\Database\Schema\Blueprint;
// Mengimpor Schema facade untuk menjalankan operasi pembuatan tabel di database
use Illuminate\Support\Facades\Schema;

// Mendefinisikan class migrasi anonim (standar modern Laravel 11)
return new class extends Migration
{
    /**
     * Method up() berjalan saat kita menjalankan perintah 'php artisan migrate'
     */
    public function up(): void
    {
        // Membuat tabel baru bernama 'peminjamans' di MySQL sesuai instruksi modul
        Schema::create('peminjamans', function (Blueprint $table) {
            
            // 1. Kolom ID utama (Primary Key) yang bertambah otomatis (auto-increment)
            $table->id();

            // 2. Kolom kode transaksi (misal: 'PJ-001'), wajib unique agar tidak ada kode kembar
            $table->string('kode_pinjam')->unique();

            // 3. Foreign Key ke tabel bukus, jika buku dihapus maka riwayat pinjamnya ikut terhapus
            $table->foreignId('buku_id')->constrained('bukus')->cascadeOnDelete();

            // 4. Kolom untuk menyimpan NIM mahasiswa yang meminjam buku (maksimal 20 karakter)
            $table->string('nim_peminjam', 20);

            // 5. Kolom untuk menyimpan nama lengkap mahasiswa peminjam (maksimal 150 karakter)
            $table->string('nama_peminjam', 150);

            // 6. Kolom tanggal saat buku mulai dipinjam
            $table->date('tanggal_pinjam');

            // 7. Kolom batas akhir tanggal buku harus dikembalikan oleh mahasiswa
            $table->date('tanggal_jatuh_tempo');

            // 8. Kolom tanggal saat buku dikembalikan, nullable() artinya boleh kosong saat awal dipinjam
            $table->date('tanggal_kembali')->nullable();

            // 9. Kolom status, nilainya hanya boleh 'dipinjam' atau 'dikembalikan' (default: 'dipinjam')
            $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam');

            // 10. Kolom otomatis buatan Laravel untuk mencatat waktu dibuat (created_at) dan diubah (updated_at)
            $table->timestamps();
        });
    }

    /**
     * Method down() berjalan jika kita membatalkan migrasi (rollback)
     */
    public function down(): void
    {
        // Menghapus tabel 'peminjamans' jika migrasi ditarik kembali
        Schema::dropIfExists('peminjamans');
    }
};