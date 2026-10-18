{{-- 1. Menggunakan kerangka layout utama aplikasi bertema Del Purple --}}
<x-layout title="Login Pustakawan — SIPUS-Del">

    {{-- 2. Membuat baris grid Bootstrap untuk memposisikan kotak login di tengah layar --}}
    <div class="row justify-content-center mt-3">

        {{-- 3. Membatasi lebar kotak login menjadi 5 kolom grid (ukuran ideal untuk kartu login) --}}
        <div class="col-md-5">

            {{-- 4. Komponen Card Bootstrap tanpa garis tepi (border-0) dengan bayangan halus (shadow-sm) --}}
            <div class="card border-0 shadow-sm">

                {{-- 5. Bagian kepala kartu dengan latar belakang putih dan teks rata tengah --}}
                <div class="card-header bg-white py-3 text-center border-0">
                    {{-- 6. Ikon gembok besar sebagai penanda visual halaman autentikasi --}}
                    <div class="fs-1">🔐</div>
                    {{-- 7. Judul utama halaman autentikasi pustakawan --}}
                    <h4 class="fw-bold mb-1 text-dark">Login Pustakawan</h4>
                    {{-- 8. Subjudul kecil deskripsi hak akses pustakawan --}}
                    <p class="text-muted small mb-0">Masuk untuk mengelola sirkulasi dan katalog perpustakaan</p>
                </div>

                {{-- 9. Badan kartu yang memuat elemen-elemen formulir input data --}}
                <div class="card-body p-4">

                    {{-- 10. Tag formulir HTML yang mengirimkan data ke route 'login.perform' menggunakan metode POST --}}
                    <form action="{{ route('login.perform') }}" method="POST">

                        {{-- 11. Direktif @csrf untuk menyematkan token rahasia anti-pemalsuan request lintas situs --}}
                        @csrf

                        {{-- 12. Pembungkus input email dengan margin bawah sebesar 3 unit --}}
                        <div class="mb-3">
                            {{-- 13. Label teks untuk kolom input alamat email --}}
                            <label for="email" class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                            
                            {{-- 14. Kolom input teks email dengan pemulihan otomatis nilai dari HTTP Cookie ($lastEmail) --}}
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   placeholder="nama@del.ac.id" 
                                   value="{{ old('email', $lastEmail) }}" required autofocus>
                            
                            {{-- 15. Memeriksa apakah terdapat pesan kesalahan validasi pada kolom email --}}
                            @error('email')
                                {{-- 16. Menampilkan teks pesan error validasi warna merah jika email salah format --}}
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- 17. Menampilkan info hijau jika nilai email berhasil dipulihkan dari HTTP Cookie --}}
                            @if($lastEmail)
                                {{-- 18. Teks konfirmasi bahwa fitur Cookie::queue bekerja membaca email terakhir --}}
                                <div class="form-text text-success small">✓ Email dipulihkan dari HTTP Cookie sesi sebelumnya.</div>
                            @endif
                        </div>

                        {{-- 19. Pembungkus input kata sandi (password) --}}
                        <div class="mb-3">
                            {{-- 20. Label teks untuk kolom input password --}}
                            <label for="password" class="form-label fw-semibold">Kata Sandi (Password) <span class="text-danger">*</span></label>
                            
                            {{-- 21. Kolom input tipe password agar karakter yang diketik disamarkan menjadi bulatan --}}
                            <input type="password" name="password" id="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Masukkan password Anda" required>
                            
                            {{-- 22. Memeriksa apakah terdapat pesan kesalahan validasi pada kolom password --}}
                            @error('password')
                                {{-- 23. Menampilkan teks pesan error jika password kosong atau tidak valid --}}
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 24. Kotak centang opsi Remember Me (mengingat sesi login pengguna) --}}
                        <div class="mb-3 form-check">
                            {{-- 25. Elemen checkbox HTML untuk opsi remember me --}}
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            {{-- 26. Teks label pendamping kotak centang remember me --}}
                            <label class="form-check-label small" for="remember">Ingat Saya di Perangkat Ini</label>
                        </div>

                        {{-- 27. Wadah tombol submit dengan gaya tata letak full-width (d-grid) --}}
                        <div class="d-grid gap-2 mt-4">
                            {{-- 28. Tombol submit utama bertema warna resmi Del Purple untuk memicu login --}}
                            <button type="submit" class="btn btn-del btn-lg shadow-sm">
                                🔓 Masuk ke Sistem
                            </button>
                        </div>

                    {{-- 29. Penutup tag formulir login --}}
                    </form>

                    {{-- 30. Kotak bantuan informasi kredensial akun uji coba pustakawan sesuai modul poin 5 --}}
                    <div class="mt-4 p-3 bg-light rounded text-center small border">
                        {{-- 31. Judul penjelas kredensial seeder pustakawan --}}
                        <strong class="text-dark">Kredensial Akun Pustakawan Uji:</strong><br>
                        {{-- 32. Baris informasi alamat email pustakawan uji --}}
                        Email: <code class="text-primary">pustakawan@del.ac.id</code><br>
                        {{-- 33. Baris informasi password pustakawan uji --}}
                        Password: <code class="text-primary">password123</code>
                    </div>

                </div>

            </div>

        </div>

    </div>

</x-layout>