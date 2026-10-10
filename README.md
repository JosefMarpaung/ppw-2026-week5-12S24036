# SIPUS-Del — Sistem Informasi Perpustakaan Kampus Del

**Mata Kuliah:** Pemrograman & Pengujian Web (12S3101)  
**Institusi:** Institut Teknologi Del — Fakultas Informatika dan Teknik Elektro  
**Tahun Akademik:** Ganjil 2026/2027  
**NIM Pengembang:** 12S24036  

---

## 📌 Deskripsi Proyek
**SIPUS-Del** adalah aplikasi pengelolaan katalog dan sirkulasi koleksi pustaka berbasis web server-side rendering menggunakan framework **Laravel 11.x**. Proyek ini menerapkan arsitektur **Model-View-Controller (MVC)** yang bersih, perlindungan keamanan data, optimasi performa kueri basis data Eloquent, dan antarmuka bertema **Del Royal Purple (`#4C1D95`)**.

---

## 🚀 Fitur dan Spesifikasi Teknis
1. **Pola Arsitektur MVC & Separation of Concerns:**
   - **Model (`app/Models`):** `Kategori` dan `Buku` mengimplementasikan Active Record dengan proteksi Mass Assignment (`$fillable`).
   - **Controller (`app/Http/Controllers`):** `KategoriController` dan `BukuController` menangani 7 aksi RESTful baku secara terpisah dari logika antarmuka dan basis data.
   - **View (`resources/views`):** Memanfaatkan Master Shell Layout `<x-layout>`, Blade components, Bootstrap 5, pagination, dan perulangan aman `@forelse`.
2. **Relasi Data Eloquent (One-to-Many):**
   - Satu Kategori menaungi banyak Buku (`hasMany(Buku::class)`).
   - Satu Buku dimiliki oleh satu Kategori (`belongsTo(Kategori::class)`).
3. **Validasi Masukan Server-Side & Keamanan Web:**
   - Validasi ketat via `$request->validate()` (ISBN unik, judul minimal 5 karakter, stok minimal 0, integritas kunci asing).
   - Seluruh formulir terproteksi token CSRF (`@csrf`) dan Method Spoofing (`@method('PUT')`, `@method('DELETE')`).
4. **Otomasi Mock Data (Seeders & Factories):**
   - Tersedia Seeder dan Factory memanfaatkan pustaka **Faker** yang menghasilkan 5 Kategori pokok dan 20 data Buku realistis.
5. **Mitigasi N+1 Query Problem via Eager Loading:**
   - Menggunakan `Buku::with('kategori')->latest()->paginate(10)` pada halaman utama katalog untuk memangkas kueri SQL menjadi tepat 2 kueri.

---

## ⚙️ Panduan Instalasi & Menjalankan Proyek

Ikuti langkah-langkah berikut untuk menjalankan aplikasi pada lingkungan lokal:

```bash
# 1. Kloning repositori proyek
git clone https://github.com/JosefMarpaung/ppw-2026-week5-12S24036.git
cd ppw-2026-week5-12S24036

# 2. Pasang dependensi PHP (jika belum terpasang)
composer install

# 3. Konfigurasi berkas environment
cp .env.example .env
php artisan key:generate

# 4. Eksekusi migrasi skema dan suntikkan mock data seeder
php artisan migrate:fresh --seed

# 5. Jalankan server pengembangan lokal Laravel
php artisan serve
```
Akses aplikasi melalui peramban web di: `http://localhost:8000`

---

## 🔍 Pembuktian Audit Kinerja SQL (Mitigasi N+1 Problem)

Untuk membuktikan efektivitas **Eager Loading** (`with('kategori')`) dalam meniadakan lonjakan kueri SQL (N+1 Problem), listener `DB::listen` dipasang pada `app/Providers/AppServiceProvider.php` yang mencatat eksekusi query ke `storage/logs/laravel.log`.

### Bukti Cuplikan Log Kueri SQL:
```log
[2026-10-10 06:47:17] local.INFO: [SQL AUDIT] (3.93 ms) select count(*) as "aggregate" from "bukus"
[2026-10-10 06:47:17] local.INFO: [SQL AUDIT] (0.45 ms) select * from "bukus" order by "created_at" desc limit 10 offset 0
[2026-10-10 06:47:17] local.INFO: [SQL AUDIT] (0.44 ms) select * from "kategoris" where "kategoris"."id" in (1, 4, 5)
```

### Analisis Kinerja:
* **Tanpa Eager Loading (Lazy Loading):** Sistem akan mengeksekusi 1 kueri untuk mengambil buku + 10 kueri tambahan untuk setiap kategori individual = **11 kueri SQL**.
* **Dengan Eager Loading (`with('kategori')`):** Sistem hanya mengeksekusi **tepat 2 kueri terpadu** (mengambil koleksi buku dan kueri `WHERE "id" IN (...)`), mereduksi beban I/O basis data secara optimal.

---

## 📜 Riwayat Commit Git (Conventional Commits)
Repositori ini dikelola dengan standar *Conventional Commits* bertahap:
1. `feat(migration): create kategoris and bukus tables schema`
2. `feat(model): define relationships and fillable attributes`
3. `feat(controller): implement restful actions and server validation`
4. `feat(views): construct blade component layout and crud templates`
5. `perf(eager-loading): optimize sql queries to prevent n+1 problem`
