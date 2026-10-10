<?php

namespace Database\Factories;

use App\Models\Buku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Buku>
 */
class BukuFactory extends Factory
{
    /**
     * Mendefinisikan nilai atribut bawaan untuk mock data Model Buku.
     *
     * @return array<string, mixed>
     */
     public function definition(): array
     {
         return [
             // Menghasilkan nomor ISBN unik realistis berformat 13 digit
             'isbn'         => fake()->unique()->isbn13(),
             // Menghasilkan judul buku ilmiah/komputer dengan kalimat singkat yang diawali huruf kapital
             'judul'        => rtrim(fake()->sentence(rand(3, 6)), '.'),
             // Menghasilkan nama lengkap penulis buku fiktif
             'penulis'      => fake()->name(),
             // Menghasilkan nama perusahaan penerbit buku
             'penerbit'     => fake()->company() . ' Publishing',
             // Menghasilkan angka tahun terbit antara 2015 hingga tahun sekarang
             'tahun_terbit' => fake()->numberBetween(2015, date('Y')),
             // Menghubungkan secara otomatis ke ID Kategori baru jika belum disuplai
             'kategori_id'  => \App\Models\Kategori::factory(),
             // Menghasilkan jumlah stok buku fisik antara 1 hingga 50 eksemplar
             'stok'         => fake()->numberBetween(1, 50),
             // Menghasilkan teks paragraf sinopsis ringkas
             'sinopsis'     => fake()->paragraph(3),
         ];
     }
}
