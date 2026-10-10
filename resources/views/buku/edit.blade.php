{{-- Mewarisi kerangka Master Shell Layout Del Purple --}}
<x-layout title="Ubah Data Buku — SIPUS-Del">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Tombol navigasi kembali ke katalog buku --}}
            <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary mb-3">
                ← Kembali ke Katalog
            </a>

            {{-- Kartu pembungkus formulir edit buku --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h4 class="fw-bold mb-0 text-dark">Formulir Ubah Data Buku</h4>
                </div>
                <div class="card-body p-4">
                    {{-- Formulir pengiriman pembaruan data buku menuju route update dengan parameter $buku --}}
                    <form action="{{ route('buku.update', $buku) }}" method="POST">
                        {{-- Token CSRF untuk validasi keamanan request --}}
                        @csrf
                        {{-- Method Spoofing PUT: Laravel mengenali ini sebagai permintaan HTTP PUT/PATCH untuk pembaruan data --}}
                        @method('PUT')

                        <div class="row g-3">
                            {{-- Input Nomor ISBN --}}
                            <div class="col-md-6">
                                <label for="isbn" class="form-label fw-semibold">Nomor ISBN <span class="text-danger">*</span></label>
                                <input type="text" name="isbn" id="isbn" 
                                       class="form-control @error('isbn') is-invalid @enderror" 
                                       value="{{ old('isbn', $buku->isbn) }}" required>
                                @error('isbn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Pilihan Dropdown Kategori Buku --}}
                            <div class="col-md-6">
                                <label for="kategori_id" class="form-label fw-semibold">Kategori Buku <span class="text-danger">*</span></label>
                                <select name="kategori_id" id="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror" required>
                                    @foreach($kategoris as $kategori)
                                        {{-- Memilih kategori saat ini sebagai opsi aktif terpilih --}}
                                        <option value="{{ $kategori->id }}" {{ old('kategori_id', $buku->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama_kategori }} ({{ $kategori->kode_kategori }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Judul Buku --}}
                            <div class="col-12">
                                <label for="judul" class="form-label fw-semibold">Judul Buku <span class="text-danger">*</span></label>
                                <input type="text" name="judul" id="judul" 
                                       class="form-control @error('judul') is-invalid @enderror" 
                                       value="{{ old('judul', $buku->judul) }}" required>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Penulis --}}
                            <div class="col-md-6">
                                <label for="penulis" class="form-label fw-semibold">Penulis / Pengarang <span class="text-danger">*</span></label>
                                <input type="text" name="penulis" id="penulis" 
                                       class="form-control @error('penulis') is-invalid @enderror" 
                                       value="{{ old('penulis', $buku->penulis) }}" required>
                                @error('penulis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Penerbit --}}
                            <div class="col-md-6">
                                <label for="penerbit" class="form-label fw-semibold">Penerbit <span class="text-danger">*</span></label>
                                <input type="text" name="penerbit" id="penerbit" 
                                       class="form-control @error('penerbit') is-invalid @enderror" 
                                       value="{{ old('penerbit', $buku->penerbit) }}" required>
                                @error('penerbit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Tahun Terbit --}}
                            <div class="col-md-6">
                                <label for="tahun_terbit" class="form-label fw-semibold">Tahun Terbit <span class="text-danger">*</span></label>
                                <input type="number" name="tahun_terbit" id="tahun_terbit" 
                                       class="form-control @error('tahun_terbit') is-invalid @enderror" 
                                       value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" min="1900" max="{{ date('Y') + 1 }}" required>
                                @error('tahun_terbit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Jumlah Stok --}}
                            <div class="col-md-6">
                                <label for="stok" class="form-label fw-semibold">Jumlah Stok Fisik <span class="text-danger">*</span></label>
                                <input type="number" name="stok" id="stok" 
                                       class="form-control @error('stok') is-invalid @enderror" 
                                       value="{{ old('stok', $buku->stok) }}" min="0" required>
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Sinopsis Buku --}}
                            <div class="col-12">
                                <label for="sinopsis" class="form-label fw-semibold">Sinopsis / Ringkasan Buku</label>
                                <textarea name="sinopsis" id="sinopsis" rows="4" 
                                          class="form-control @error('sinopsis') is-invalid @enderror">{{ old('sinopsis', $buku->sinopsis) }}</textarea>
                                @error('sinopsis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tombol Perbarui Data --}}
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-del w-100 py-2 fs-5 shadow-sm">
                                    🔄 Perbarui Informasi Buku
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
