{{-- Mewarisi kerangka Master Layout bertema Del Purple --}}
<x-layout title="Sirkulasi Peminjaman — SIPUS-Del">

    {{-- Baris Header: Judul dan Tombol Transaksi Peminjaman Baru --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Sirkulasi Peminjaman Buku</h2>
            <p class="text-muted mb-0">Riwayat transaksi peminjaman dan pengembalian buku berbasis transaksi ACID</p>
        </div>
        {{-- Tombol untuk membuka formulir peminjaman buku baru --}}
        <a href="{{ route('peminjaman.create') }}" class="btn btn-del shadow-sm">
            ➕ Catat Peminjaman Baru
        </a>
    </div>

    {{-- Kartu Pembungkus Tabel Riwayat Transaksi --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-3">No</th>
                            <th scope="col">Kode Pinjam</th>
                            <th scope="col">Judul Buku</th>
                            <th scope="col">Peminjam</th>
                            <th scope="col">Tgl Pinjam</th>
                            <th scope="col">Jatuh Tempo</th>
                            <th scope="col">Tgl Kembali</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjamans as $index => $item)
                            <tr>
                                {{-- Nomor Urut Paginasi --}}
                                <td class="ps-3">{{ $peminjamans->firstItem() + $index }}</td>
                                {{-- Kode Unik Transaksi --}}
                                <td><span class="badge bg-secondary font-monospace">{{ $item->kode_pinjam }}</span></td>
                                {{-- Judul Buku (diambil lewat relasi BelongsTo $item->buku->judul) --}}
                                <td class="fw-semibold">{{ $item->buku->judul }}</td>
                                {{-- Identitas Mahasiswa --}}
                                <td>
                                    <div>{{ $item->nama_peminjam }}</div>
                                    <small class="text-muted font-monospace">{{ $item->nim_peminjam }}</small>
                                </td>
                                {{-- Tanggal Pinjam & Jatuh Tempo --}}
                                <td>{{ date('d M Y', strtotime($item->tanggal_pinjam)) }}</td>
                                <td>{{ date('d M Y', strtotime($item->tanggal_jatuh_tempo)) }}</td>
                                {{-- Tanggal Aktual Pengembalian --}}
                                <td>
                                    @if($item->tanggal_kembali)
                                        <span class="text-success">{{ date('d M Y', strtotime($item->tanggal_kembali)) }}</span>
                                    @else
                                        <span class="text-muted fst-italic">Belum kembali</span>
                                    @endif
                                </td>
                                {{-- Status Peminjaman --}}
                                <td class="text-center">
                                    @if($item->status === 'dipinjam')
                                        <span class="badge bg-warning text-dark">Sedang Dipinjam</span>
                                    @else
                                        <span class="badge bg-success">Dikembalikan</span>
                                    @endif
                                </td>
                                {{-- Tombol Aksi Pengembalian Buku --}}
                                <td class="text-center pe-3">
                                    @if($item->status === 'dipinjam')
                                        {{-- Formulir tombol pengembalian buku dengan transaksi ACID + lockForUpdate --}}
                                        <form action="{{ route('peminjaman.kembalikan', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Proses pengembalian buku ini dan pulihkan stok secara atomik?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                🔄 Kembalikan Buku
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">✓ Transaksi Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <div class="fs-1">📋</div>
                                    <p class="mb-0">Belum ada riwayat transaksi peminjaman buku.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Paginasi data --}}
        @if($peminjamans->hasPages())
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-center">
                    {{ $peminjamans->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</x-layout>