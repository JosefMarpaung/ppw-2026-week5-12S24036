{{-- Mewarisi kerangka Master Layout bertema Del Purple --}}
<x-layout title="Catat Peminjaman Buku — SIPUS-Del">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Tombol Kembali --}}
            <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
                ← Kembali ke Daftar Peminjaman
            </a>

            {{-- Kartu Formulir Peminjaman --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-dark">📋 Formulir Peminjaman Buku (Transaksi Atomik ACID)</h5>
                </div>
                <div class="card-body p-4">
                    {{-- Formulir pengiriman data transaksi peminjaman --}}
                    <form action="{{ route('peminjaman.store') }}" method="POST">
                        @csrf

                        {{-- 1. Pilihan Buku yang Dipinjam --}}
                        <div class="mb-3">
                            <label for="buku_id" class="form-label fw-semibold">Pilih Buku <span class="text-danger">*</span></label>
                            <select name="buku_id" id="buku_id" class="form-select @error('buku_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Judul Buku --</option>
                                @foreach($bukus as $b)
                                    <option value="{{ $b->id }}" {{ (old('buku_id', $selectedBukuId) == $b->id) ? 'selected' : '' }}>
                                        {{ $b->judul }} (Stok Tersedia: {{ $b->stok }} eks)
                                    </option>
                                @endforeach
                            </select>
                            @error('buku_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 2. NIM Mahasiswa Peminjam --}}
                        <div class="mb-3">
                            <label for="nim_peminjam" class="form-label fw-semibold">NIM Mahasiswa <span class="text-danger">*</span></label>
                            <input type="text" name="nim_peminjam" id="nim_peminjam" 
                                   class="form-control font-monospace @error('nim_peminjam') is-invalid @enderror" 
                                   placeholder="Contoh: 12S24036" 
                                   value="{{ old('nim_peminjam') }}" required>
                            <div class="form-text">Sistem akan menolak jika NIM ini masih meminjam buku yang sama dan belum mengembalikannya.</div>
                            @error('nim_peminjam')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 3. Nama Lengkap Mahasiswa Peminjam --}}
                        <div class="mb-3">
                            <label for="nama_peminjam" class="form-label fw-semibold">Nama Lengkap Mahasiswa <span class="text-danger">*</span></label>
                            <input type="text" name="nama_peminjam" id="nama_peminjam" 
                                   class="form-control @error('nama_peminjam') is-invalid @enderror" 
                                   placeholder="Nama lengkap peminjam" 
                                   value="{{ old('nama_peminjam') }}" required>
                            @error('nama_peminjam')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            {{-- 4. Tanggal Pinjam --}}
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_pinjam" class="form-label fw-semibold">Tanggal Pinjam <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" 
                                       class="form-control @error('tanggal_pinjam') is-invalid @enderror" 
                                       value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
                                @error('tanggal_pinjam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 5. Tanggal Jatuh Tempo (Default 7 hari ke depan) --}}
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_jatuh_tempo" class="form-label fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" 
                                       class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" 
                                       value="{{ old('tanggal_jatuh_tempo', date('Y-m-d', strtotime('+7 days'))) }}" required>
                                @error('tanggal_jatuh_tempo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Tombol Submit Transaksi ACID --}}
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-del btn-lg shadow-sm">
                                🔒 Konfirmasi Peminjaman (Proses ACID & Lock)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layout>