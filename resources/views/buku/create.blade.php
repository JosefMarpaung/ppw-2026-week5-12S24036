{{-- Mewarisi kerangka Master Shell Layout Del Purple --}}
<x-layout title="Tambah Buku Baru — SIPUS-Del">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Tombol navigasi kembali ke katalog buku --}}
            <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary mb-3">
                ← Kembali ke Katalog
            </a>

            {{-- Kartu pembungkus formulir penambahan buku --}}
            <div class="card border-0 shadow-sm">
                {{-- Header kartu formulir --}}
                <div class="card-header bg-white py-3 border-0">
                    <h4 class="fw-bold mb-0 text-dark">Formulir Tambah Buku Baru</h4>
                </div>
                <div class="card-body p-4">
                    {{-- Formulir pengiriman data buku baru via HTTP POST --}}
                    <form action="{{ route('buku.store') }}" method="POST">
                        {{-- Token CSRF mutlak wajib disertakan untuk menangkal serangan Cross-Site Request Forgery --}}
                        @csrf

                        <div class="row g-3">
                            {{-- Input Nomor ISBN --}}
                            <div class="col-md-6">
                                <label for="isbn" class="form-label fw-semibold">Nomor ISBN <span class="text-danger">*</span></label>
                                <input type="text" name="isbn" id="isbn" 
                                       class="form-control @error('isbn') is-invalid @enderror" 
                                       value="{{ old('isbn') }}" placeholder="Contoh: 978-602-03-8594-5" required>
                                {{-- Menampilkan pesan galat validasi server jika ISBN tidak valid atau kembar --}}
                                @error('isbn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Pilihan Dropdown Kategori Buku --}}
                            <div class="col-md-6">
                                <label for="kategori_id" class="form-label fw-semibold">Kategori Buku <span class="text-danger">*</span></label>
                                <select name="kategori_id" id="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    {{-- Mengulang opsi kategori yang dikirim dari controller --}}
                                    @foreach($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama_kategori }} ({{ $kategori->kode_kategori }})
                                        </option>
                                    @endforeach
                                </select>
                                {{-- Menampilkan pesan galat validasi jika kategori belum dipilih --}}
                                @error('kategori_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Judul Buku --}}
                            <div class="col-12">
                                <label for="judul" class="form-label fw-semibold">Judul Buku <span class="text-danger">*</span></label>
                                <input type="text" name="judul" id="judul" 
                                       class="form-control @error('judul') is-invalid @enderror" 
                                       value="{{ old('judul') }}" placeholder="Masukkan judul lengkap buku" required>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Nama Penulis --}}
                            <div class="col-md-6">
                                <label for="penulis" class="form-label fw-semibold">Penulis / Pengarang <span class="text-danger">*</span></label>
                                <input type="text" name="penulis" id="penulis" 
                                       class="form-control @error('penulis') is-invalid @enderror" 
                                       value="{{ old('penulis') }}" placeholder="Contoh: Robert C. Martin" required>
                                @error('penulis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Nama Penerbit --}}
                            <div class="col-md-6">
                                <label for="penerbit" class="form-label fw-semibold">Penerbit <span class="text-danger">*</span></label>
                                <input type="text" name="penerbit" id="penerbit" 
                                       class="form-control @error('penerbit') is-invalid @enderror" 
                                       value="{{ old('penerbit') }}" placeholder="Contoh: Prentice Hall" required>
                                @error('penerbit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Tahun Terbit --}}
                            <div class="col-md-6">
                                <label for="tahun_terbit" class="form-label fw-semibold">Tahun Terbit <span class="text-danger">*</span></label>
                                <input type="number" name="tahun_terbit" id="tahun_terbit" 
                                       class="form-control @error('tahun_terbit') is-invalid @enderror" 
                                       value="{{ old('tahun_terbit', date('Y')) }}" min="1900" max="{{ date('Y') + 1 }}" required>
                                @error('tahun_terbit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Jumlah Stok Fisik --}}
                            <div class="col-md-6">
                                <label for="stok" class="form-label fw-semibold">Jumlah Stok Fisik <span class="text-danger">*</span></label>
                                <input type="number" name="stok" id="stok" 
                                       class="form-control @error('stok') is-invalid @enderror" 
                                       value="{{ old('stok', 0) }}" min="0" required>
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Sinopsis Buku (Opsional) --}}
                            <div class="col-12">
                                <label for="sinopsis" class="form-label fw-semibold">Sinopsis / Ringkasan Buku</label>
                                <textarea name="sinopsis" id="sinopsis" rows="4" 
                                          class="form-control @error('sinopsis') is-invalid @enderror" 
                                          placeholder="Tuliskan gambaran ringkas isi buku (opsional)">{{ old('sinopsis') }}</textarea>
                                @error('sinopsis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tombol Simpan Form --}}
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-del w-100 py-2 fs-5 shadow-sm">
                                    💾 Simpan Data Buku
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
