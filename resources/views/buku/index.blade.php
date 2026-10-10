{{-- Mewarisi kerangka Master Shell Layout x-layout yang bertema Del Purple --}}
<x-layout title="Katalog Buku — SIPUS-Del">
    {{-- Baris Header: Judul Halaman dan Tombol Tambah Buku Baru --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            {{-- Judul utama halaman katalog buku --}}
            <h2 class="fw-bold mb-1">Katalog Buku Perpustakaan</h2>
            {{-- Deskripsi ringkas halaman --}}
            <p class="text-muted mb-0">Daftar koleksi buku yang tersedia di Institut Teknologi Del</p>
        </div>
        {{-- Tombol menuju halaman form tambah buku baru --}}
        <a href="{{ route('buku.create') }}" class="btn btn-del shadow-sm">
            ➕ Tambah Buku Baru
        </a>
    </div>

    {{-- Kartu pembungkus tabel data buku --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            {{-- Tabel responsif Bootstrap --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    {{-- Baris kepala kolom tabel --}}
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-3">No</th>
                            <th scope="col">ISBN</th>
                            <th scope="col">Judul Buku</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Penulis</th>
                            <th scope="col">Tahun</th>
                            <th scope="col" class="text-center">Stok</th>
                            <th scope="col" class="text-center pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Menggunakan direktif @forelse untuk perulangan aman sekaligus menangani kondisi data kosong --}}
                        @forelse($bukus as $index => $buku)
                            <tr>
                                {{-- Nomor urut terpaginasi secara dinamis --}}
                                <td class="ps-3">{{ $bukus->firstItem() + $index }}</td>
                                {{-- Menampilkan nomor unik ISBN --}}
                                <td><span class="badge bg-secondary font-monospace">{{ $buku->isbn }}</span></td>
                                {{-- Menampilkan judul buku berhuruf tebal --}}
                                <td class="fw-semibold">{{ $buku->judul }}</td>
                                {{-- Memanggil nama kategori hasil EAGER LOADING ($buku->kategori) tanpa query berulang --}}
                                <td>
                                    <span class="badge badge-del">{{ $buku->kategori->nama_kategori }}</span>
                                </td>
                                {{-- Menampilkan nama penulis buku --}}
                                <td>{{ $buku->penulis }}</td>
                                {{-- Menampilkan tahun terbit --}}
                                <td>{{ $buku->tahun_terbit }}</td>
                                {{-- Menampilkan jumlah ketersediaan fisik stok buku --}}
                                <td class="text-center">
                                    <span class="badge {{ $buku->stok > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $buku->stok }} eks
                                    </span>
                                </td>
                                {{-- Tombol aksi untuk operasi Detail, Edit, dan Hapus --}}
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Tombol melihat detail buku --}}
                                        <a href="{{ route('buku.show', $buku) }}" class="btn btn-outline-info" title="Lihat Detail">
                                            👁️ Detail
                                        </a>
                                        {{-- Tombol mengedit buku --}}
                                        <a href="{{ route('buku.edit', $buku) }}" class="btn btn-outline-warning" title="Ubah Data">
                                            ✏️ Edit
                                        </a>
                                        {{-- Formulir tombol hapus buku dengan proteksi CSRF dan method DELETE spoofing --}}
                                        <form action="{{ route('buku.destroy', $buku) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
                                            {{-- Token CSRF wajib untuk keamanan pengiriman data --}}
                                            @csrf
                                            {{-- Method spoofing DELETE karena HTML form bawaan tidak mendukung metode DELETE --}}
                                            @method('DELETE')
                                            {{-- Tombol submit aksi hapus data --}}
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus Buku">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Blok @empty dieksekusi jika belum ada data buku pada basis data --}}
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="fs-1">📖</div>
                                    <p class="mb-0">Belum ada data buku yang tersimpan dalam sistem perpustakaan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Footer kartu untuk menampilkan navigasi tombol paginasi Bootstrap 5 --}}
        @if($bukus->hasPages())
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-center">
                    {{-- Merender link paginasi bergaya Bootstrap 5 --}}
                    {{ $bukus->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</x-layout>
