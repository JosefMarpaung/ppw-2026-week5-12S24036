{{-- Mewarisi kerangka Master Shell Layout Del Purple --}}
<x-layout title="Detail Buku — {{ $buku->judul }}">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Tombol navigasi kembali ke katalog buku --}}
            <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary mb-3">
                ← Kembali ke Katalog
            </a>

            {{-- Kartu rincian informasi buku --}}
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <span class="badge badge-del fs-6">{{ $buku->kategori->nama_kategori }}</span>
                    <span class="badge bg-secondary font-monospace">{{ $buku->isbn }}</span>
                </div>
                <div class="card-body p-4">
                    {{-- Judul Buku Utama --}}
                    <h3 class="fw-bold mb-3 text-dark">{{ $buku->judul }}</h3>

                    {{-- Tabel Spesifikasi Data Teknis Buku --}}
                    <table class="table table-bordered mb-4">
                        <tbody>
                            <tr>
                                <th scope="row" class="bg-light" style="width: 30%;">Penulis / Pengarang</th>
                                <td>{{ $buku->penulis }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="bg-light">Penerbit</th>
                                <td>{{ $buku->penerbit }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="bg-light">Tahun Terbit</th>
                                <td>{{ $buku->tahun_terbit }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="bg-light">Ketersediaan Stok</th>
                                <td>
                                    <span class="badge {{ $buku->stok > 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                                        {{ $buku->stok }} eksemplar
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="bg-light">Waktu Dicatat</th>
                                <td>{{ $buku->created_at->format('d F Y, H:i') }} WIB</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Bagian Sinopsis Buku --}}
                    <h5 class="fw-bold mb-2">Sinopsis Buku</h5>
                    <div class="p-3 bg-light rounded text-secondary mb-4">
                        {{-- Menggunakan null coalescing untuk teks jika sinopsis kosong --}}
                        {{ $buku->sinopsis ?? 'Belum ada ringkasan sinopsis untuk buku ini.' }}
                    </div>

                    {{-- Tombol Aksi: Edit & Hapus --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('buku.edit', $buku) }}" class="btn btn-warning">
                            ✏️ Ubah Buku
                        </a>
                        <form action="{{ route('buku.destroy', $buku) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                🗑️ Hapus Buku
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
