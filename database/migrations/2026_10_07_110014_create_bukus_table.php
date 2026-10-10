<?php

// Mengimpor kelas Migration sebagai fondasi utama pembuatan skema tabel di Laravel
use Illuminate\Database\Migrations\Migration;
// Mengimpor Blueprint untuk mendefinisikan kolom, tipe data, indeks, dan relasi foreign key
use Illuminate\Database\Schema\Blueprint;
// Mengimpor Facade Schema untuk mengelola struktur tabel pada basis data
use Illuminate\Support\Facades\Schema;

// Mengembalikan anonymous migration class agar tidak terjadi konflik penamaan kelas migrasi
return new class extends Migration
{
    /**
     * Menjalankan operasi migrasi untuk membuat tabel 'bukus' di basis data.
     */
    public function up(): void
    {
        // Memerintahkan Schema builder untuk membuat tabel baru bernama 'bukus'
        Schema::create('bukus', function (Blueprint $table) {
            // Kolom 'id': Primary Key bertipe Big Integer Auto-Increment
            $table->id();

            // Kolom 'isbn': Nomor ISBN buku bertipe VARCHAR(20) dengan aturan UNIQUE agar tidak ada duplikasi
            $table->string('isbn', 20)->unique();

            // Kolom 'judul': Menyimpan judul buku bertipe VARCHAR(200)
            $table->string('judul', 200);

            // Kolom 'penulis': Menyimpan nama penulis/pengarang bertipe VARCHAR(100)
            $table->string('penulis', 100);

            // Kolom 'penerbit': Menyimpan nama penerbit bertipe VARCHAR(100)
            $table->string('penerbit', 100);

            // Kolom 'tahun_terbit': Menyimpan tahun terbit buku bertipe INTEGER (contoh: 2024)
            $table->integer('tahun_terbit');

            // Kolom 'kategori_id': Kunci tamu (Foreign Key) yang terhubung ke 'id' tabel 'kategoris'
            // cascadeOnDelete(): Menghapus otomatis semua buku terkait jika kategorinya dihapus
            $table->foreignId('kategori_id')
                  ->constrained('kategoris')
                  ->cascadeOnDelete();

            // Kolom 'stok': Menyimpan jumlah ketersediaan fisik buku bertipe INTEGER dengan nilai bawaan 0
            $table->integer('stok')->default(0);

            // Kolom 'sinopsis': Menyimpan ringkasan buku bertipe TEXT bersifat NULLABLE (opsional/boleh kosong)
            $table->text('sinopsis')->nullable();

            // Kolom 'created_at' dan 'updated_at' otomatis bertipe TIMESTAMP
            $table->timestamps();
        });
    }

    /**
     * Membatalkan proses migrasi (rollback) dengan menghapus tabel bukus jika diperlukan.
     */
    public function down(): void
    {
        // Menghapus tabel 'bukus' dari basis data jika tabel tersebut eksis
        Schema::dropIfExists('bukus');
    }
};
