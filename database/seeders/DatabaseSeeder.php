<?php

// 1. Menentukan namespace seeder bawaan Laravel
namespace Database\Seeders;

// 2. Mengimpor Model Buku untuk pembuatan sampel buku uji
use App\Models\Buku;
// 3. Mengimpor Model Kategori untuk pembuatan kategori buku
use App\Models\Kategori;
// 4. Mengimpor Model Peminjaman untuk pembuatan sampel sirkulasi buku
use App\Models\Peminjaman;
// 5. Mengimpor Model User untuk pembuatan akun pustakawan resmi
use App\Models\User;
// 6. Mengimpor kelas Hash untuk mengenkripsi password dengan algoritma Bcrypt yang aman
use Illuminate\Support\Facades\Hash;
// 7. Mengimpor kelas Seeder dasar dari framework Laravel
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * 8. Method run() dijalankan secara otomatis saat kita mengeksekusi 'php artisan db:seed'
     */
    public function run(): void
    {
        // 9. [KRITIS MODUL] Membuat atau memperbarui akun Pustakawan Uji resmi Del
        User::updateOrCreate(
            // 10. Kunci pencarian unik berdasarkan alamat email
            ['email' => 'pustakawan@del.ac.id'],
            // 11. Data akun pustakawan sesuai instruksi halaman 7 modul
            [
                'name'     => 'Pustakawan Institut Teknologi Del',
                'password' => Hash::make('password123'), // Dienkripsi dengan Hash Bcrypt
            ]
        );

        // 12. Menyiapkan kategori pokok perpustakaan Del jika belum ada
        $kategoriTI = Kategori::firstOrCreate(
            ['kode_kategori' => 'IF-001'],
            ['nama_kategori' => 'Rekayasa Perangkat Lunak & Basis Data']
        );

        $kategoriSI = Kategori::firstOrCreate(
            ['kode_kategori' => 'SI-001'],
            ['nama_kategori' => 'Sistem Informasi & Arsitektur Enterprise']
        );

        // 13. [KRITIS MODUL] Buku Uji 1: Stok = 0 (Khusus untuk menguji penolakan pinjam & rollback ACID)
        $bukuStokNol = Buku::updateOrCreate(
            ['isbn' => '978-602-0001'],
            [
                'judul'        => 'Rekayasa Perangkat Lunak Lanjut (Buku Uji Stok Habis)',
                'penulis'      => 'Chandro Pardede, S.Kom., M.Sc.',
                'penerbit'     => 'Del Press',
                'tahun_terbit' => 2025,
                'kategori_id'  => $kategoriTI->id,
                'stok'         => 0, // [PENTING] Stok 0 untuk membuktikan penolakan ACID
                'sinopsis'     => 'Buku sampel dengan stok 0 untuk verifikasi penolakan transaksi dan rollback atomik.',
            ]
        );

        // 14. [KRITIS MODUL] Buku Uji 2: Stok = 1 (Khusus untuk menguji lockForUpdate & pengurangan stok)
        $bukuStokSatu = Buku::updateOrCreate(
            ['isbn' => '978-602-0002'],
            [
                'judul'        => 'Arsitektur Basis Data & Transaksi ACID (Buku Uji Stok 1)',
                'penulis'      => 'Tim Dosen PPW Del',
                'penerbit'     => 'Informatika IT Del',
                'tahun_terbit' => 2026,
                'kategori_id'  => $kategoriTI->id,
                'stok'         => 1, // [PENTING] Stok 1 untuk membuktikan atomisitas pengurangan stok
                'sinopsis'     => 'Buku sampel dengan stok 1 untuk membuktikan penguncian baris (lockForUpdate).',
            ]
        );

        // 15. Buku Uji 3: Stok normal untuk pemakaian reguler
        $bukuNormal = Buku::updateOrCreate(
            ['isbn' => '978-602-0003'],
            [
                'judul'        => 'Pemrograman Web Modern dengan Laravel 11.x',
                'penulis'      => 'Laboratorium Komputasi Del',
                'penerbit'     => 'Del Press',
                'tahun_terbit' => 2026,
                'kategori_id'  => $kategoriSI->id,
                'stok'         => 15,
                'sinopsis'     => 'Panduan komprehensif implementasi web framework Laravel 11.',
            ]
        );

        // 16. [KRITIS MODUL] Sampel Transaksi 1: Status 'dipinjam'
        Peminjaman::firstOrCreate(
            ['kode_pinjam' => 'PJ-20261018-0001'],
            [
                'buku_id'             => $bukuNormal->id,
                'nim_peminjam'        => '12S24036',
                'nama_peminjam'       => 'Mahasiswa Praktikan Del',
                'tanggal_pinjam'      => now()->subDays(3)->toDateString(),
                'tanggal_jatuh_tempo' => now()->addDays(4)->toDateString(),
                'tanggal_kembali'     => null,
                'status'              => 'dipinjam',
            ]
        );

        // 17. [KRITIS MODUL] Sampel Transaksi 2: Status 'dikembalikan'
        Peminjaman::firstOrCreate(
            ['kode_pinjam' => 'PJ-20261018-0002'],
            [
                'buku_id'             => $bukuStokSatu->id,
                'nim_peminjam'        => '12S24001',
                'nama_peminjam'       => 'Alumni Del',
                'tanggal_pinjam'      => now()->subDays(10)->toDateString(),
                'tanggal_jatuh_tempo' => now()->subDays(3)->toDateString(),
                'tanggal_kembali'     => now()->subDays(2)->toDateString(),
                'status'              => 'dikembalikan',
            ]
        );
    }
}