<?php

// 1. Menentukan namespace direktori resource API versi 1
namespace App\Http\Resources\Api\V1;

// 2. Mengimpor kelas Request HTTP dari Laravel
use Illuminate\Http\Request;
// 3. Mengimpor basis JsonResource untuk transformasi model Eloquent ke format JSON
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Kelas BukuResource bertugas sebagai Transformation Layer (Sesuai Modul Hal 19 & 34 Poin 2).
 * Berfungsi memisahkan struktur tabel fisik database internal dari representasi JSON publik.
 * Mengimplementasikan kontrol Hypermedia HATEOAS (Richardson Maturity Model Level 3).
 */
class BukuResource extends JsonResource
{
    /**
     * Mengubah instansiasi Model Buku menjadi representasi array JSON.
     *
     * @param Request $request Permintaan HTTP saat ini
     * @return array Struktur array representasi data dan navigasi HATEOAS
     */
    public function toArray(Request $request): array
    {
        // 1. Memeriksa ketersediaan stok fisik buku secara logika bisnis
        $tersedia = $this->stok > 0;

        // 2. Menyusun daftar kontrol navigasi Hypermedia HATEOAS dasar
        $links = [
            // 3. Tautan 'self': Menunjuk ke URI detail sumber daya buku ini sendiri (metode GET)
            'self' => [
                'href'   => url("/api/v1/bukus/{$this->id}"),
                'method' => 'GET',
            ],
            // 4. Tautan 'kategori': Memfilter buku lain yang memiliki kategori serupa
            'kategori' => [
                'href'   => url("/api/v1/bukus?kategori_id={$this->kategori_id}"),
                'method' => 'GET',
            ],
            // 5. Tautan 'update_full': Menunjukkan operasi penggantian total seluruh kolom (metode PUT)
            'update_full' => [
                'href'   => url("/api/v1/bukus/{$this->id}"),
                'method' => 'PUT',
            ],
            // 6. Tautan 'update_partial': Menunjukkan operasi pembaruan sebagian kolom (metode PATCH)
            'update_partial' => [
                'href'   => url("/api/v1/bukus/{$this->id}"),
                'method' => 'PATCH',
            ],
            // 7. Tautan 'delete': Menunjukkan operasi penghapusan buku (metode DELETE)
            'delete' => [
                'href'   => url("/api/v1/bukus/{$this->id}"),
                'method' => 'DELETE',
            ],
            // 8. Tautan 'referensi_eksternal': Menunjuk ke layanan pengayaan data buku dari OpenLibrary
            'referensi_eksternal' => [
                'href'   => url("/api/v1/bukus/{$this->id}/referensi-eksternal"),
                'method' => 'GET',
            ],
        ];

        // 9. [SYARAT MUTLAK HATEOAS MODUL HALAMAN 34 POIN 4]:
        // Tautan aksi 'pinjam_buku' HANYA dimunculkan di dalam _links apabila stok > 0!
        if ($tersedia) {
            // 10. Jika stok tersedia, sematkan tautan peminjaman ke dalam respons
            $links['pinjam_buku'] = [
                'href'   => url('/api/v1/peminjamans'),
                'method' => 'POST',
            ];
        }
        // Catatan: Jika stok habis (0), tautan 'pinjam_buku' otomatis tidak ada, sehingga klien mobile
        // tahu tombol pinjam harus dinonaktifkan tanpa perlu menghitung ulang rumus backend!

        // 11. Mengembalikan struktur representasi JSON yang bersih, aman, dan konsisten
        return [
            'id'           => $this->id,                   // ID unik buku
            'isbn'         => $this->isbn,                 // Kode unik ISBN buku
            'judul'        => $this->judul,                // Judul lengkap buku
            'penulis'      => $this->penulis,              // Nama pengarang/penulis
            'penerbit'     => $this->penerbit,             // Nama penerbit
            'tahun_terbit' => (int) $this->tahun_terbit,   // Memastikan tipe data integer
            'stok'         => (int) $this->stok,           // Sisa jumlah stok fisik buku
            'status_stok'  => $tersedia ? 'TERSEDIA' : 'HABIS', // Label status untuk tampilan klien
            'sinopsis'     => $this->sinopsis,             // Teks ringkasan sinopsis buku
            // 12. Eager Loading Relasi Kategori (Mencegah N+1 Query Problem):
            // Objek kategori HANYA dimuat jika controller memanggil with('kategori')
            'kategori'     => $this->whenLoaded('kategori', function () {
                return [
                    'id'            => $this->kategori->id,
                    'kode_kategori' => $this->kategori->kode_kategori,
                    'nama_kategori' => $this->kategori->nama_kategori,
                ];
            }),
            // 13. Format waktu pembaruan terakhir menggunakan standar internasional ISO 8601
            'updated_at'   => $this->updated_at?->toIso8601String(),
            // 14. Menyertakan blok tautan Hypermedia HATEOAS
            '_links'       => $links,
        ];
    }
}
