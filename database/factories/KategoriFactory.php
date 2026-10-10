<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kategori>
 */
class KategoriFactory extends Factory
{
    /**
     * Mendefinisikan nilai atribut bawaan untuk pembuatan data tiruan (mock data) Model Kategori.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Daftar nama rumpun ilmu/kategori buku realistis untuk perpustakaan kampus
        $kategoriList = [
            'Pemrograman & Rekayasa Perangkat Lunak',
            'Kecerdasan Buatan & Data Science',
            'Jaringan Komputer & Cyber Security',
            'Sistem Informasi & Bisnis Digital',
            'Matematika & Komputasi Dasar',
            'Basis Data & Cloud Computing',
            'Desain Grafis & UI/UX Design',
        ];

        return [
            // Menghasilkan kode kategori acak unik seperti CAT-001, CAT-002, dst.
            'kode_kategori' => 'CAT-' . fake()->unique()->numerify('###'),
            // Memilih nama kategori realistis secara acak dari daftar yang disediakan
            'nama_kategori' => fake()->unique()->randomElement($kategoriList),
        ];
    }
}
