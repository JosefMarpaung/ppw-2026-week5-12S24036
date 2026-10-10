<!DOCTYPE html>
{{-- Mendefinisikan dokumen HTML dengan bahasa Indonesia --}}
<html lang="id">
<head>
    {{-- Menentukan set karakter UTF-8 untuk mendukung berbagai simbol teks --}}
    <meta charset="UTF-8">
    {{-- Mengatur viewport agar tampilan responsif di layar ponsel maupun desktop --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Judul halaman dinamis dengan nilai bawaan 'SIPUS-Del' jika variabel $title tidak diisi --}}
    <title>{{ $title ?? 'SIPUS-Del' }} — Institut Teknologi Del</title>
    {{-- Memuat framework styling Bootstrap 5.3 via CDN resmi sesuai modul praktikum --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Menyematkan gaya CSS kustom untuk identitas warna resmi Del Royal Purple (#4C1D95) --}}
    <style>
        /* Warna latar navbar ungu khas Institut Teknologi Del */
        .navbar-del { background-color: #4C1D95; }
        /* Warna tombol utama Del Purple */
        .btn-del { background-color: #4C1D95; color: #ffffff; border: none; }
        /* Efek hover pada tombol Del Purple agar sedikit lebih gelap */
        .btn-del:hover { background-color: #3B0764; color: #ffffff; }
        /* Teks badge ungu Del */
        .badge-del { background-color: #4C1D95; color: #ffffff; }
    </style>
</head>
<body class="bg-light">
    {{-- Bilah Navigasi Utama (Navbar) --}}
    <nav class="navbar navbar-expand-lg navbar-dark navbar-del shadow-sm mb-4">
        <div class="container">
            {{-- Brand logo dan nama sistem perpustakaan --}}
            <a class="navbar-brand fw-bold" href="{{ route('buku.index') }}">📚 SIPUS-Del</a>
            {{-- Tombol toggle untuk menu responsif pada layar kecil/smartphone --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            {{-- Daftar tautan navigasi aplikasi --}}
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto">
                    {{-- Menu navigasi menuju katalog buku --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('buku*') ? 'active fw-bold' : '' }}" href="{{ route('buku.index') }}">Katalog Buku</a>
                    </li>
                    {{-- Menu navigasi menuju manajemen kategori --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('kategori*') ? 'active fw-bold' : '' }}" href="{{ route('kategori.index') }}">Kategori Buku</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Kontainer Utama Konten Aplikasi --}}
    <main class="container pb-5">
        {{-- Menampilkan notifikasi sukses (Flash Session Message) jika aksi Controller berhasil --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{-- Menampilkan pesan teks yang dikirimkan melalui ->with('success', ...) --}}
                <strong>Berhasil!</strong> {{ session('success') }}
                {{-- Tombol close untuk menutup alert notifikasi --}}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Slot dinamis: tempat konten halaman anak (views) akan disuntikkan secara otomatis --}}
        {{ $slot }}
    </main>

    {{-- Memuat pustaka JavaScript Bootstrap untuk fungsi interaktif seperti modal dan alert dismiss --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
