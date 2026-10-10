{{-- Mewarisi kerangka Master Shell Layout Del Purple --}}
<x-layout title="Tambah Kategori Baru — SIPUS-Del">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            {{-- Tombol navigasi kembali ke daftar kategori --}}
            <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary mb-3">
                ← Kembali ke Kategori
            </a>

            {{-- Kartu formulir penambahan kategori --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h4 class="fw-bold mb-0 text-dark">Formulir Tambah Kategori</h4>
                </div>
                <div class="card-body p-4">
                    {{-- Form HTTP POST menuju aksi KategoriController@store --}}
                    <form action="{{ route('kategori.store') }}" method="POST">
                        {{-- Token CSRF keamanan formulir --}}
                        @csrf

                        <div class="mb-3">
                            <label for="kode_kategori" class="form-label fw-semibold">Kode Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="kode_kategori" id="kode_kategori" 
                                   class="form-control @error('kode_kategori') is-invalid @enderror" 
                                   value="{{ old('kode_kategori') }}" placeholder="Contoh: TEK-01" required>
                            @error('kode_kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Kode harus unik dan maksimal 20 karakter.</div>
                        </div>

                        <div class="mb-4">
                            <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" id="nama_kategori" 
                                   class="form-control @error('nama_kategori') is-invalid @enderror" 
                                   value="{{ old('nama_kategori') }}" placeholder="Contoh: Rekayasa Perangkat Lunak" required>
                            @error('nama_kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-del w-100 py-2 fs-5 shadow-sm">
                            💾 Simpan Kategori
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
