{{-- 1. Mendeklarasikan tipe dokumen HTML5 modern --}}
<!DOCTYPE html>
{{-- 2. Menentukan bahasa dokumen adalah Bahasa Indonesia --}}
<html lang="id">
<head>
    {{-- 3. Set karakter UTF-8 untuk mendukung representasi karakter dan simbol internasional --}}
    <meta charset="UTF-8">
    {{-- 4. Pengaturan viewport dinamis agar tampilan responsif di layar ponsel dan laptop --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- 5. Judul halaman dinamis dengan fallback teks 'SIPUS-Del' jika $title tidak diisi --}}
    <title>{{ $title ?? 'SIPUS-Del' }} — Institut Teknologi Del</title>
    {{-- 6. Memuat lembar gaya Bootstrap 5.3 via CDN resmi sesuai spesifikasi modul praktikum --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- 7. Menambahkan styling CSS kustom untuk identitas warna resmi Del Royal Purple --}}
    <style>
        /* 8. Warna latar ungu pekat khas Del untuk bilah navigasi utama */
        .navbar-del { background-color: #4C1D95; }
        /* 9. Warna ungu khas Del untuk tombol aksi utama */
        .btn-del { background-color: #4C1D95; color: #ffffff; border: none; }
        /* 10. Warna ungu lebih pekat saat kursor diarahkan ke tombol Del */
        .btn-del:hover { background-color: #3B0764; color: #ffffff; }
        /* 11. Warna latar ungu khas Del untuk lencana badge */
        .badge-del { background-color: #4C1D95; color: #ffffff; }
    </style>
</head>
{{-- 12. Tag pembuka badan dokumen dengan warna latar abu-abu terang (bg-light) --}}
<body class="bg-light">

    {{-- 13. Komponen bilah navigasi utama (Navbar) dengan bayangan halus --}}
    <nav class="navbar navbar-expand-lg navbar-dark navbar-del shadow-sm mb-4">
        {{-- 14. Kontainer pembatas lebar isi navbar agar sejajar rapi dengan konten utama --}}
        <div class="container">
            {{-- 15. Logo teks dan brand nama sistem perpustakaan kampus --}}
            <a class="navbar-brand fw-bold" href="{{ route('buku.index') }}">📚 SIPUS-Del</a>
            
            {{-- 16. Tombol hamburger responsif untuk perangkat mobile berlayar kecil --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                {{-- 17. Ikon bawaan Bootstrap untuk menu hamburger --}}
                <span class="navbar-toggler-icon"></span>
            </button>
            
            {{-- 18. Kontainer menu navigasi yang bisa menciut pada tampilan mobile --}}
            <div class="collapse navbar-collapse" id="navMenu">
                {{-- 19. Daftar menu navigasi di sisi kiri --}}
                <ul class="navbar-nav me-auto">
                    {{-- 20. Menu menuju katalog koleksi buku (selalu terbuka untuk semua pengunjung) --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('buku*') ? 'active fw-bold' : '' }}" href="{{ route('buku.index') }}">Katalog Buku</a>
                    </li>
                    
                    {{-- 21. Memeriksa apakah pengguna saat ini sudah terautentikasi (login) --}}
                    @auth
                        {{-- 22. Menu sirkulasi peminjaman hanya muncul bagi pengguna yang sudah login --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('peminjaman*') ? 'active fw-bold' : '' }}" href="{{ route('peminjaman.index') }}">Sirkulasi Peminjaman</a>
                        </li>
                        {{-- 23. Menu kategori buku hanya muncul bagi pengguna yang sudah login --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('kategori*') ? 'active fw-bold' : '' }}" href="{{ route('kategori.index') }}">Kategori Buku</a>
                        </li>
                    @endauth
                </ul>

                {{-- 24. Daftar item status akun di sisi kanan navbar --}}
                <ul class="navbar-nav ms-auto align-items-center">
                    {{-- 25. Kondisi jika pengguna SUDAH login sebagai Pustakawan --}}
                    @auth
                        {{-- 26. Menampilkan lencana identitas nama pustakawan yang aktif --}}
                        <li class="nav-item me-2">
                            <span class="badge bg-light text-dark py-2 px-3 shadow-sm">
                                👤 {{ Auth::user()->name }}
                            </span>
                        </li>
                        
                        {{-- 27. Memeriksa apakah state waktu login tersimpan di Session server --}}
                        @if(session('login_timestamp'))
                            {{-- 28. Menampilkan lencana bukti waktu aktif sesi (bukti penilaian modul poin 4) --}}
                            <li class="nav-item me-3 d-none d-lg-block">
                                <span class="badge bg-purple text-white border border-light py-2 px-2 small">
                                    🕒 Sesi: {{ session('login_timestamp') }}
                                </span>
                            </li>
                        @endif

                        {{-- 29. Formulir tombol logout pustakawan --}}
                        <li class="nav-item">
                            {{-- 30. Tag formulir untuk aksi logout dengan metode HTTP POST --}}
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                {{-- 31. Token CSRF wajib untuk keamanan pengiriman form logout --}}
                                @csrf
                                {{-- 32. Tombol logout berwarna merah lembut dengan konfirmasi aksi --}}
                                <button type="submit" class="btn btn-sm btn-outline-light" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
                                    🚪 Logout
                                </button>
                            </form>
                        </li>
                    
                    {{-- 33. Kondisi jika pengguna BELUM login (Tamu/Guest) --}}
                    @else
                        {{-- 34. Tombol menuju ke formulir autentikasi login pustakawan --}}
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="btn btn-sm btn-light fw-bold px-3 shadow-sm">
                                🔐 Login Pustakawan
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- 35. Kontainer utama tempat seluruh isi konten halaman anak ditampilkan --}}
    <main class="container pb-5">
        
        {{-- 36. Memeriksa apakah ada pesan kilat (flash session) sukses dari controller --}}
        @if(session('success'))
            {{-- 37. Kotak notifikasi warna hijau untuk keberhasilan aksi --}}
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <strong>Berhasil!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 38. Memeriksa apakah ada pesan kilat (flash session) error dari controller --}}
        @if(session('error'))
            {{-- 39. Kotak notifikasi warna merah untuk penolakan transaksi ACID atau kesalahan sistem --}}
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <strong>Peringatan Sistem:</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 40. Memeriksa apakah ada pesan kegagalan validasi dari formulir --}}
        @if($errors->any())
            {{-- 41. Kotak notifikasi warna kuning peringatan validasi input --}}
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                <strong>Perhatian:</strong> Mohon periksa kembali input formulir Anda.
                <ul class="mb-0 mt-2">
                    {{-- 42. Perulangan untuk menampilkan setiap rincian teks kesalahan input --}}
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 43. Slot dinamis Laravel Blade tempat konten halaman spesifik disuntikkan --}}
        {{ $slot }}
    </main>

    {{-- 44. Memuat pustaka JavaScript Bootstrap 5.3 via CDN resmi --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>