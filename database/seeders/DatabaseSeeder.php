<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Mengeksekusi penanaman data awal (Seeding) ke basis data.
     */
    public function run(): void
    {
        // 1. Menyiapkan daftar 5 kategori buku pokok perpustakaan Del
        $daftarKategori = [
            ['kode_kategori' => 'IF-001', 'nama_kategori' => 'Rekayasa Perangkat Lunak'],
            ['kode_kategori' => 'IF-002', 'nama_kategori' => 'Kecerdasan Buatan & Data Science'],
            ['kode_kategori' => 'IF-003', 'nama_kategori' => 'Jaringan Komputer & Cyber Security'],
            ['kode_kategori' => 'SI-001', 'nama_kategori' => 'Sistem Informasi & Enterprise Architecture'],
            ['kode_kategori' => 'MK-001', 'nama_kategori' => 'Matematika & Komputasi Numerik'],
        ];

        // 2. Memasukkan ke-5 kategori ke tabel kategoris dan menanamkan buku di masing-masing kategori
        foreach ($daftarKategori as $dataKat) {
            // Membuat atau mengambil kategori
            $kategori = \App\Models\Kategori::create($dataKat);

            // Membuat 4 buku untuk setiap kategori (Total = 5 x 4 = 20 buku sesuai syarat minimal modul)
            \App\Models\Buku::factory(4)->create([
                'kategori_id' => $kategori->id, // Mengaitkan buku ke kategori saat ini
            ]);
        }
    }
}
