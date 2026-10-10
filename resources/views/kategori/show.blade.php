{{-- Mewarisi kerangka Master Shell Layout Del Purple --}}
<x-layout title="Kategori — {{ $kategori->nama_kategori }}">
    <div class="mb-4">
        {{-- Tombol navigasi kembali ke daftar kategori --}}
        <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary mb-3">
            ← Kembali ke Daftar Kategori
        </a>

        {{-- Kartu Info Ringkas Kategori --}}
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge badge-del font-monospace fs-6 mb-2">{{ $kategori->kode_kategori }}</span>
                    <h3 class="fw-bold mb-1">{{ $kategori->nama_kategori }}</h3>
                    <p class="text-muted mb-0">Total Buku Terdaftar: <strong>{{ $kategori->bukus->count() }} judul</strong></p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('kategori.edit', $kategori) }}" class="btn btn-warning">
                        ✏️ Ubah Kategori
                    </a>
                </div>
            </div>
        </div>

        {{-- Tabel Daftar Buku di bawah Kategori Ini --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0">Koleksi Buku pada Kategori Ini</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-3">No</th>
                                <th scope="col">ISBN</th>
                                <th scope="col">Judul Buku</th>
                                <th scope="col">Penulis</th>
                                <th scope="col">Tahun</th>
                                <th scope="col" class="text-center">Stok</th>
                                <th scope="col" class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kategori->bukus as $index => $buku)
                                <tr>
                                    <td class="ps-3">{{ $index + 1 }}</td>
                                    <td><span class="badge bg-secondary font-monospace">{{ $buku->isbn }}</span></td>
                                    <td class="fw-semibold">{{ $buku->judul }}</td>
                                    <td>{{ $buku->penulis }}</td>
                                    <td>{{ $buku->tahun_terbit }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $buku->stok > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $buku->stok }} eks
                                        </span>
                                    </td>
                                    <td class="text-center pe-3">
                                        <a href="{{ route('buku.show', $buku) }}" class="btn btn-sm btn-outline-info">
                                            👁️ Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Belum ada buku yang didaftarkan pada kategori ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout>
