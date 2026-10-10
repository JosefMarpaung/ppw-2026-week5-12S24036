{{-- Mewarisi kerangka Master Shell Layout Del Purple --}}
<x-layout title="Kategori Buku — SIPUS-Del">
    {{-- Header Halaman Kategori --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Daftar Kategori Buku</h2>
            <p class="text-muted mb-0">Pengelompokan klasifikasi koleksi buku perpustakaan</p>
        </div>
        {{-- Tombol menuju form tambah kategori baru --}}
        <a href="{{ route('kategori.create') }}" class="btn btn-del shadow-sm">
            ➕ Tambah Kategori Baru
        </a>
    </div>

    {{-- Kartu Tabel Kategori --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-3" style="width: 10%;">No</th>
                            <th scope="col" style="width: 25%;">Kode Kategori</th>
                            <th scope="col" style="width: 45%;">Nama Kategori</th>
                            <th scope="col" class="text-center pe-3" style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Perulangan daftar kategori dengan fallback @empty jika kosong --}}
                        @forelse($kategoris as $index => $kategori)
                            <tr>
                                <td class="ps-3">{{ $kategoris->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge badge-del font-monospace fs-6">{{ $kategori->kode_kategori }}</span>
                                </td>
                                <td class="fw-semibold">{{ $kategori->nama_kategori }}</td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Tombol melihat koleksi buku pada kategori ini --}}
                                        <a href="{{ route('kategori.show', $kategori) }}" class="btn btn-outline-info" title="Lihat Koleksi">
                                            👁️ Lihat
                                        </a>
                                        {{-- Tombol edit kategori --}}
                                        <a href="{{ route('kategori.edit', $kategori) }}" class="btn btn-outline-warning" title="Ubah Kategori">
                                            ✏️ Edit
                                        </a>
                                        {{-- Form hapus kategori dengan konfirmasi dialog --}}
                                        <form action="{{ route('kategori.destroy', $kategori) }}" method="POST" class="d-inline" onsubmit="return confirm('PENTING: Menghapus kategori ini juga akan menghapus seluruh buku di dalamnya! Lanjutkan?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus Kategori">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <div class="fs-1">🏷️</div>
                                    <p class="mb-0">Belum ada kategori yang dibuat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Footer Paginasi Kategori --}}
        @if($kategoris->hasPages())
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-center">
                    {{ $kategoris->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</x-layout>
