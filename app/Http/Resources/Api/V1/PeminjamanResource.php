<?php

// 1. Mendeklarasikan namespace direktori resource API v1
namespace App\Http\Resources\Api\V1;

// 2. Mengimpor Request HTTP dari framework Laravel
use Illuminate\Http\Request;
// 3. Mengimpor basis JsonResource untuk serialisasi model
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Kelas PeminjamanResource bertugas mentransformasikan data sirkulasi ke format JSON.
 * Menerapkan prinsip HATEOAS: Tautan aksi 'kembalikan_buku' hanya disajikan secara dinamis
 * apabila transaksi buku tersebut memang masih dalam status 'dipinjam'.
 */
class PeminjamanResource extends JsonResource
{
    /**
     * Mengubah model Peminjaman menjadi array representasi JSON.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        // 1. Menyusun tautan navigasi mandiri (self link)
        $links = [
            'self' => [
                'href'   => url("/api/v1/peminjamans/{$this->id}"),
                'method' => 'GET',
            ],
        ];

        // 2. [SYARAT MUTLAK HATEOAS MODUL HALAMAN 34 POIN 4]:
        // Tautan 'kembalikan_buku' (metode PATCH) HANYA muncul apabila status === 'dipinjam'!
        if ($this->status === 'dipinjam') {
            // 3. Jika buku masih dipinjam, sertakan endpoint aksi pengembalian
            $links['kembalikan_buku'] = [
                'href'   => url("/api/v1/peminjamans/{$this->id}/kembalikan"),
                'method' => 'PATCH',
            ];
        }
        // Jika buku sudah berstatus 'dikembalikan', link aksi pengembalian ini otomatis lenyap

        // 4. Mengembalikan struktur representasi data transaksi peminjaman
        return [
            'id'                  => $this->id,                  // ID transaksi unik
            'kode_pinjam'         => $this->kode_pinjam,         // Nomor referensi transaksi (PINJ-...)
            'buku_id'             => $this->buku_id,             // ID buku yang dipinjam
            'buku_judul'          => $this->buku?->judul,        // Judul buku yang diambil dari relasi
            'nim_peminjam'        => $this->nim_peminjam,        // NIM mahasiswa peminjam
            'nama_peminjam'       => $this->nama_peminjam,       // Nama mahasiswa peminjam
            'tanggal_pinjam'      => $this->tanggal_pinjam,      // Tanggal peminjaman
            'tanggal_jatuh_tempo' => $this->tanggal_jatuh_tempo, // Batas waktu pengembalian
            'tanggal_kembali'     => $this->tanggal_kembali,     // Tanggal riil pengembalian (null jika belum kembali)
            'status'              => $this->status,              // Status sirkulasi ('dipinjam' / 'dikembalikan')
            '_links'              => $links,                     // Objek HATEOAS navigasi berikutnya
        ];
    }
}
