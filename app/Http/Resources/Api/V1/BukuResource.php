<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BukuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $tersedia = $this->stok > 0; // 1. Cek apakah sisa stok ada

        // 2. Kontrol tautan Hypermedia HATEOAS
        $links = [
            'self'                => ['href' => url("/api/v1/bukus/{$this->id}"), 'method' => 'GET'],
            'kategori'            => ['href' => url("/api/v1/bukus?kategori_id={$this->kategori_id}"), 'method' => 'GET'],
            'update_full'         => ['href' => url("/api/v1/bukus/{$this->id}"), 'method' => 'PUT'],
            'update_partial'      => ['href' => url("/api/v1/bukus/{$this->id}"), 'method' => 'PATCH'],
            'delete'              => ['href' => url("/api/v1/bukus/{$this->id}"), 'method' => 'DELETE'],
            'referensi_eksternal' => ['href' => url("/api/v1/bukus/{$this->id}/referensi-eksternal"), 'method' => 'GET'],
        ];

        // 3. [HATEOAS]: Link pinjam_buku HANYA muncul bila stok masih ada (> 0)
        if ($tersedia) {
            $links['pinjam_buku'] = ['href' => url('/api/v1/peminjamans'), 'method' => 'POST'];
        }

        return [
            'id'           => $this->id,
            'isbn'         => $this->isbn,
            'judul'        => $this->judul,
            'penulis'      => $this->penulis,
            'penerbit'     => $this->penerbit,
            'tahun_terbit' => (int)$this->tahun_terbit,
            'stok'         => (int)$this->stok,
            'status_stok'  => $tersedia ? 'TERSEDIA' : 'HABIS',
            'sinopsis'     => $this->sinopsis,
            // 4. Cegah N+1 Query Problem dengan whenLoaded
            'kategori'     => $this->whenLoaded('kategori', fn() => [
                'id'            => $this->kategori->id,
                'kode_kategori' => $this->kategori->kode_kategori,
                'nama_kategori' => $this->kategori->nama_kategori,
            ]),
            'updated_at'   => $this->updated_at?->toIso8601String(),
            '_links'       => $links,
        ];
    }
}