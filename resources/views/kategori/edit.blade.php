{{-- Mewarisi kerangka Master Shell Layout Del Purple --}}
<x-layout title="Ubah Kategori — SIPUS-Del">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            {{-- Tombol navigasi kembali ke daftar kategori --}}
            <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary mb-3">
                ← Kembali ke Kategori
            </a>

            {{-- Kartu formulir edit kategori --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h4 class="fw-bold mb-0 text-dark">Formulir Ubah Kategori</h4>
                </div>
                <div class="card-body p-4">
                    {{-- Form HTTP POST dengan spoofing PUT ke route update --}}
                    <form action="{{ route('kategori.update', $kategori) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="kode_kategori" class="form-label fw-semibold">Kode Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="kode_kategori" id="kode_kategori" 
                                   class="form-control @error('kode_kategori') is-invalid @enderror" 
                                   value="{{ old('kode_kategori', $kategori->kode_kategori) }}" required>
                            @error('kode_kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" id="nama_kategori" 
                                   class="form-control @error('nama_kategori') is-invalid @enderror" 
                                   value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
                            @error('nama_kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-del w-100 py-2 fs-5 shadow-sm">
                            🔄 Perbarui Kategori
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
