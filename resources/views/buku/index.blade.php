{{-- Mewarisi kerangka Master Layout bertema Del Purple --}}
<x-layout title="Katalog & Analitik Buku — SIPUS-Del">

    {{-- Baris Header: Judul Halaman dan Tombol Tambah Buku Baru --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Katalog Koleksi Buku & Analitik</h2>
            <p class="text-muted mb-0">Sistem Informasi Perpustakaan Institut Teknologi Del</p>
        </div>
        <a href="{{ route('buku.create') }}" class="btn btn-del shadow-sm">
            ➕ Tambah Buku Baru
        </a>
    </div>

    {{-- BAGIAN 1: 4 KARTU STATISTIK ANALITIK (Fluent Query Builder DB::table) --}}
    {{-- Sesuai Spesifikasi Teknis Wajib Poin 3 Modul Praktikum --}}
    <div class="row g-3 mb-4">
        {{-- Kartu 1: Total Judul Buku --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Total Judul Buku</div>
                    <div class="fs-3 fw-bold text-primary">{{ $stats['total_judul'] }}</div>
                    <small class="text-muted">Koleksi judul unik</small>
                </div>
            </div>
        </div>
        {{-- Kartu 2: Total Stok Fisik Tersedia --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white border-start border-success border-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Total Stok Tersedia</div>
                    <div class="fs-3 fw-bold text-success">{{ $stats['total_stok'] }} eks</div>
                    <small class="text-muted">Eksemplar fisik di rak</small>
                </div>
            </div>
        </div>
        {{-- Kartu 3: Total Buku Sedang Dipinjam --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white border-start border-warning border-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Sedang Dipinjam</div>
                    <div class="fs-3 fw-bold text-warning">{{ $stats['total_sedang_dipinjam'] }}</div>
                    <small class="text-muted">Transaksi aktif mahasiswa</small>
                </div>
            </div>
        </div>
        {{-- Kartu 4: Total Buku Selesai Dikembalikan --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white border-start border-info border-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Total Dikembalikan</div>
                    <div class="fs-3 fw-bold text-info">{{ $stats['total_dikembalikan'] }}</div>
                    <small class="text-muted">Riwayat sirkulasi tuntas</small>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: FORM PENCARIAN & FILTER KATEGORI DINAMIS (Klausa when) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('buku.index') }}" method="GET" class="row g-2 align-items-center">
                {{-- Input kata kunci pencarian --}}
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light">🔍</span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari berdasarkan judul, penulis, atau ISBN..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                {{-- Dropdown pilihan kategori --}}
                <div class="col-md-4">
                    <select name="kategori_id" class="form-select">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Tombol Filter & Reset --}}
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-del w-100">Cari</button>
                    @if(request()->hasAny(['search', 'kategori_id']))
                        <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary" title="Reset Filter">✕</a>
                    @endif
                </div>
            </form>
            {{-- Menampilkan riwayat pencarian terakhir yang tersimpan di Session --}}
            @if(session('last_search_keyword'))
                <div class="mt-2 small text-muted">
                    Pencarian terakhir di sesi Anda: <span class="badge bg-light text-dark border">{{ session('last_search_keyword') }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- BAGIAN 3: TABEL DAFTAR BUKU & RELASI AGREGASI withCount --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-3">No</th>
                            <th scope="col">ISBN</th>
                            <th scope="col">Judul Buku</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Penulis</th>
                            <th scope="col" class="text-center">Stok</th>
                            <th scope="col" class="text-center">Total Dipinjam</th>
                            <th scope="col" class="text-center pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bukus as $index => $buku)
                            <tr>
                                <td class="ps-3">{{ $bukus->firstItem() + $index }}</td>
                                <td><span class="badge bg-secondary font-monospace">{{ $buku->isbn }}</span></td>
                                <td class="fw-semibold">{{ $buku->judul }}</td>
                                <td>
                                    <span class="badge badge-del">{{ $buku->kategori->nama_kategori }}</span>
                                </td>
                                <td>{{ $buku->penulis }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $buku->stok > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $buku->stok }} eks
                                    </span>
                                </td>
                                {{-- Menampilkan hasil withCount('peminjamans') (Bebas N+1 Query) --}}
                                <td class="text-center">
                                    <span class="badge bg-info text-dark font-monospace">
                                        {{ $buku->peminjamans_count }} kali
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Tombol langsung Pinjam Buku ke form sirkulasi --}}
                                        <a href="{{ route('peminjaman.create', ['buku_id' => $buku->id]) }}" class="btn btn-outline-primary" title="Pinjam Buku Ini">
                                            📖 Pinjam
                                        </a>
                                        <a href="{{ route('buku.show', $buku) }}" class="btn btn-outline-info" title="Detail">
                                            👁️
                                        </a>
                                        <a href="{{ route('buku.edit', $buku) }}" class="btn btn-outline-warning" title="Edit">
                                            ✏️
                                        </a>
                                        <form action="{{ route('buku.destroy', $buku) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="fs-1">📖</div>
                                    <p class="mb-0">Tidak ada buku yang sesuai dengan kriteria pencarian Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Paginasi dengan mempertahankan query string pencarian (withQueryString) --}}
        @if($bukus->hasPages())
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-center">
                    {{ $bukus->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</x-layout>