# NASKAH & SKENARIO VIDEO PRESENTASI PRAKTIKUM MINGGU 9
## Pemrograman & Pengujian Aplikasi Web (12S3101) — Institut Teknologi Del
**Topik:** Web Services Terdistribusi, Protokol SOAP, serta Arsitektur RESTful Web API v1 (SIPUS-Del)  
**Presenter:** Josef Christian Marpaung (NIM: 12S24036)  
**Dosen Pengampu:** Chandro Pardede, S.Kom., M.Sc.  
**Durasi Total:** Tepat 10 Menit (600 Detik)  
**Pendekatan:** Kolaboratif (*Pair Programming: Mahasiswa dengan Bantuan AI Copilot*)

---

## 📋 DAFTAR PERSIAPAN SEBELUM MEREKAM (CHECKLIST LAYAR)
Pastikan 4 jendela berikut sudah terbuka rapi di laptop Anda sebelum menekan tombol *Record*:
1. **Jendela 1 (Browser Google Chrome):**
   - Tab 1: Web SIPUS-Del `http://127.0.0.1:8000/buku` (Dashboard katalog).
   - Tab 2: Repositori GitHub Josef pada branch `week9-rest-api` (buka file `README.md`).
2. **Jendela 2 (Postman Desktop):**
   - Buka koleksi `PPW Week 9 - SIPUS-Del Web Services` di bilah kiri.
3. **Jendela 3 (VS Code / Editor):**
   - Buka berkas `routes/api.php`, `routes/web.php`, dan `bootstrap/app.php`.
4. **Jendela 4 (Terminal):**
   - Pastikan `php artisan serve` sedang berjalan aktif di port 8000.

---

## ⏱️ TABEL RUNDOWN & PEMBAGIAN WAKTU (10 MENIT)

| Menit | Durasi | Agenda / Bagian | Layar yang Ditampilkan | Bobot Modul |
| :--- | :---: | :--- | :--- | :---: |
| **00:00 - 01:00** | 60 dtk | Pembukaan, Identitas, & Peran Kolaborasi AI | Wajah / Slide Judul / GitHub | - |
| **01:00 - 02:30** | 90 dtk | Poin 1: Analisis Kelayakan & Demo SOAP Clearance | GitHub README & Postman (SOAP) | 15% |
| **02:30 - 04:30** | 120 dtk | Poin 2: REST API Provider Katalog Buku (/bukus) | Postman (Login, GET, POST, PUT vs PATCH) | 25% |
| **04:30 - 06:45** | 135 dtk | Poin 3: Transaksi Sirkulasi ACID, Locking & Auth | Postman (POST Peminjamans & Aturan Bisnis) | 30% |
| **06:45 - 08:30** | 105 dtk | Poin 4: HATEOAS Dinamis, RFC 7807, & External API | Postman (_links, 422/409/503, OpenLibrary) | 15% |
| **08:30 - 09:30** | 60 dtk | Poin 5: Kontrak OpenAPI 3.0 & Postman Collection | VS Code (`openapi.yaml`) & File Repo | 15% |
| **09:30 - 10:00** | 30 dtk | Refleksi Pembelajaran, Integritas AI, & Penutup | Wajah / Browser SIPUS-Del Web | - |

---

# 🎬 SKENARIO & TEKS NASKAH VERBAL (KATA PER KATA)

---

### BAGIAN 1: PEMBUKAAN & PENGANTAR KOLABORASI AI (00:00 - 01:00)
> **Tampilan Layar:** Tampilkan wajah Anda atau slide pembuka / halaman depan repositori GitHub Anda: `JosefMarpaung/ppw-2026-week5-12S24036` pada branch `week9-rest-api`.

**Teks yang Anda Ucapkan:**
> "Halo, salam sejahtera untuk kita semua.  
> Yang terhormat Bapak Chandro Pardede, S.Kom., M.Sc., selaku dosen pengampu mata kuliah Pemrograman dan Pengujian Aplikasi Web.  
> 
> Perkenalkan, nama saya **Josef Christian Marpaung**, dengan NIM **12S24036**, mahasiswa program studi S1 Sistem Informasi Institut Teknologi Del angkatan 2024.  
> 
> Pada kesempatan kali ini, saya akan mendemonstrasikan hasil implementasi dan pengujian proyek **SIPUS-Del Minggu ke-9**, yang berfokus pada pembangunan arsitektur **Web Services Terdistribusi**, integrasi protokol lawas **SOAP**, serta perancangan layanan modern **RESTful Web API versi 1** berbasis framework Laravel 11.  
> 
> Dalam pengerjaan praktikum ini, saya bekerja menggunakan pendekatan modern yaitu **Pair Programming bersama AI Assistant**. Saya berperan sebagai *lead developer* yang merancang logika, menganalisis kebutuhan modul, serta memverifikasi alur bisnis, sedangkan AI berperan sebagai *co-pilot* yang membantu merapikan sintaks, mengecek kesesuaian standar RFC, dan menyusun kontrak dokumen.  
> 
> Tanpa berlama-lama lagi, mari kita bedah lima spesifikasi teknis utama yang telah berhasil diselesaikan."

---

### BAGIAN 2: SPESIFIKASI 1 — ANALISIS KELAYAKAN & ENDPOINT SOAP (01:00 - 02:30)
> **Tampilan Layar:** 
> 1. Pindah ke tab GitHub README.md (bagian tabel *Analisis Kelayakan Arsitektur*).
> 2. Pindah ke Postman request `POST 1. SOAP - Check Clearance Status`.

**Teks yang Anda Ucapkan:**
> "Spesifikasi pertama berbobot 15 persen adalah **Analisis Kebutuhan Kelayakan Web Service serta Implementasi Endpoint SOAP Bebas Pustaka**.  
> 
> Seperti yang terlihat pada `README.md` repositori saya, SIPUS-Del memiliki peran ganda:
> 1. Sebagai **Service Provider**, sistem ini menyajikan API katalog dan sirkulasi.
> 2. Sebagai **Service Consumer**, sistem ini mengonsumsi data dari pihak ketiga seperti OpenLibrary.
> 
> Untuk aplikasi Mobile dan Kiosk peminjam, kami memilih arsitektur **RESTful API** karena payload JSON-nya jauh lebih ringan, hemat *bandwidth*, dan *stateless*. Namun, untuk kebutuhan pertukaran data antar-sistem kampus warisan (*legacy*)—khususnya sistem yudisium kelulusan—modul mewajibkan protokol **SOAP** karena membutuhkan kontrak kaku berbasis XML.  
> 
> Di Laravel 11, saya bersama asisten AI merancang pengendali di `app/Http/Controllers/Soap/LibrarySoapController.php` dengan dua rute:
> - Pertama, `GET /soap/sipus.wsdl` yang menyajikan kontrak resmi operasi `CheckClearanceStatus`.
> - Kedua, `POST /soap/sipus` untuk memproses amplop pesan XML.
> 
> Sekarang mari kita buktikan di Postman!  
> *(Arahkan kursor ke Postman request nomor 1)*.  
> Di sini saya mengirimkan dokumen `<soap:Envelope>` yang membawa NIM saya: `12S24036`.  
> Saat tombol **Send** saya klik... *(Klik Send)*...  
> Server membalas dengan status **200 OK**, mengembalikan amplop SOAP berisi `<status_clearance>bebas_pinjam</status_clearance>` karena saya tidak memiliki tunggakan buku.  
> Jika tag NIM dikosongkan atau format XML cacat, sistem telah kami lengkapi dengan penanganan otomatis blok `<soap:Fault>` sesuai spesifikasi modul."

---

### BAGIAN 3: SPESIFIKASI 2 — REST API PROVIDER KATALOG BUKU (02:30 - 04:30)
> **Tampilan Layar:** 
> 1. Buka Postman request `POST 2. Auth - Login Pustakawan`.
> 2. Buka Postman request `GET 3. Katalog - GET Daftar Buku`.
> 3. Buka Postman request `POST 4. Katalog - POST Tambah Buku Baru`.
> 4. Buka Postman request `PUT 5a` dan `PATCH 5b`.

**Teks yang Anda Ucapkan:**
> "Kita melangkah ke spesifikasi kedua berbobot 25 persen: **Implementasi REST API Provider Katalog Buku pada rute `/api/v1/bukus`** yang memenuhi *Richardson Maturity Model* tingkat 2 dan tingkat 3.  
> 
> Pertama-tama, endpoint mutasi data dilindungi otentikasi token. Saya jalankan login pustakawan di Postman... *(Klik Send pada request Login)*...  
> Kita memperoleh respon **200 OK** bersama **Sanctum Bearer Token** sebagai tiket otorisasi.  
> 
> Selanjutnya, kita uji endpoint publik `GET /api/v1/bukus`... *(Klik Send pada request GET Katalog)*...  
> Respon mengembalikan data buku terpaginasi lengkap dengan metadata `current_page` dan `total`. Kueri database di backend telah saya optimasi menggunakan *Eager Loading* `with('kategori')` dan `withCount('peminjamans')`, sehingga dijamin **100 persen bebas dari masalah N+1 Query Problem**.  
> 
> Sekarang, kita uji operasi **POST /api/v1/bukus** untuk menambah buku baru.  
> Di sini saya lampirkan Bearer Token pada Header, serta payload JSON berisi buku baru karya saya berjudul *Arsitektur Web Modern*.  
> Ketika saya klik **Send**... *(Klik Send)*...  
> Server mengembalikan status **201 Created**, dan perhatikan bagian header-nya: terdapat header **`Location: http://127.0.0.1:8000/api/v1/bukus/{id}`** sesuai standar ketat RFC.  
> 
> Untuk membedakan semantik HTTP Verbs:
> - Operasi **PUT** kami implementasikan sebagai *Full Replacement*, di mana seluruh representasi entitas wajib dikirim ulang secara utuh.
> - Sedangkan operasi **PATCH** kami terapkan aturan `sometimes|required`, sehingga klien cukup mengirim atribut yang ingin diubah saja—misalnya hanya menambah atribut `stok`—tanpa merusak atribut buku lainnya."

---

### BAGIAN 4: SPESIFIKASI 3 — SIRKULASI ACID & KONTROL TRANSAKSI (04:30 - 06:45)
> **Tampilan Layar:** 
> 1. Buka Postman request `POST 6a. Sirkulasi - POST Peminjaman Buku`.
> 2. Pindah ke browser tab web SIPUS-Del `http://127.0.0.1:8000/buku` (tunjukkan stok buku bertambah/berkurang).
> 3. Kembali ke Postman, klik Send untuk memperlihatkan penolakan 409 Conflict.

**Teks yang Anda Ucapkan:**
> "Bagian ketiga adalah inti bisnis perpustakaan dengan bobot terbesar, yaitu **30 persen: REST API Transaksi Sirkulasi Peminjaman ACID dan Bearer Token**.  
> 
> Dalam `PeminjamanApiController.php`, setiap mutasi sirkulasi dibungkus di dalam penutupan `DB::transaction()` untuk menjamin empat pilar ACID: *Atomicity, Consistency, Isolation,* dan *Durability*.  
> Selain itu, untuk mencegah terjadinya *Race Condition*—ketika dua mahasiswa berebut meminjam satu buku fisik terakhir secara bersamaan—kami menerapkan teknik **Pessimistic Locking** menggunakan klausa `lockForUpdate()`.  
> 
> Mari kita demokan tiga aturan bisnis wajib di modul:  
> 
> **Aturan Pertama: Stok Habis (422 Unprocessable Content).**  
> Ketika klien mencoba meminjam buku nomor 1 yang stoknya memang sengaja kami set 0, sistem menolak dengan status **422** dan pesan *'Book Stock Exhausted'*.  
> 
> **Aturan Kedua: Peminjaman Berhasil (201 Created).**  
> Sekarang kita coba meminjam buku yang stoknya tersedia, misalnya buku nomor 2 atas nama saya Josef Marpaung.  
> Saat tombol **Send** saya klik... *(Klik Send)*...  
> Respon berstatus **201 Created**. Stok fisik buku otomatis dipotong 1 eksemplar, dan jika kita beralih ke antarmuka web Blade di browser lalu me-refresh... *(Tunjukkan browser)*... angka 'Sedang Dipinjam' bertambah secara *real-time*.  
> 
> **Aturan Ketiga: Pencegahan Duplikasi Pinjaman Aktif (409 Conflict).**  
> Jika saya menekan tombol **Send** sekali lagi untuk meminjam buku yang sama... *(Klik Send lagi di Postman)*...  
> Sistem langsung menolak dengan kode status **409 Conflict** dan pesan bahwa mahasiswa dengan NIM ini masih memiliki pinjaman aktif yang belum dikembalikan.  
> 
> Terakhir, ketika buku dikembalikan melalui rute **PATCH /api/v1/peminjamans/{id}/kembalikan**, status berubah menjadi 'dikembalikan' dan stok buku otomatis dipulihkan kembali bertambah satu."

---

### BAGIAN 5: SPESIFIKASI 4 — HATEOAS, RFC 7807, & OPENLIBRARY (06:45 - 08:30)
> **Tampilan Layar:** 
> 1. Tunjukkan blok `_links` pada respon Postman GET Buku dan Peminjaman.
> 2. Tunjukkan struktur error RFC 7807.
> 3. Buka Postman request `GET 7. Consumer - GET Referensi Eksternal OpenLibrary` dan klik Send.

**Teks yang Anda Ucapkan:**
> "Spesifikasi keempat berbobot 15 persen adalah **Kontrol Hypermedia HATEOAS, Global Exception RFC 7807, dan REST Service Consumer**.  
> 
> Di tingkat kematangan REST tertinggi (*Level 3*), klien tidak perlu menebak rute navigasi selanjutnya. Kami menerapkannya secara dinamis pada *API Resources*:  
> - Pada `BukuResource`: Tautan `_links.pinjam_buku` **hanya akan muncul** apabila stok buku lebih besar dari 0. Jika stok 0, tautan ini otomatis hilang dari respon JSON.  
> - Pada `PeminjamanResource`: Tautan `_links.kembalikan_buku` **hanya tampil** jika status sirkulasi masih `'dipinjam'`.  
> 
> Untuk standarisasi error di seluruh aplikasi, kami mengonfigurasi `bootstrap/app.php` dengan format standar internasional **RFC 7807 (Problem Details for HTTP APIs)** dengan header `Content-Type: application/problem+json`. Setiap kali terjadi error 401, 404, 409, atau 422, respon selalu memiliki struktur baku lima kunci: `type`, `title`, `status`, `detail`, dan `instance`.  
> 
> Selanjutnya, kami juga membuktikan kapabilitas Laravel sebagai **REST Service Consumer**.  
> Pada endpoint `GET /api/v1/bukus/{id}/referensi-eksternal`, aplikasi kita menghubungi web service publik **OpenLibrary REST API** menggunakan fasad `Http::timeout()`.  
> *(Klik Send pada request nomor 7 di Postman)*.  
> Sistem berhasil menarik metadata resmi seperti judul global, jumlah halaman, dan URL pratinjau buku. Kami juga melengkapinya dengan *Smart Fallback Cache*, sehingga jika koneksi internet terputus, sistem tetap menyajikan metadata cadangan secara elegan dengan status **200 OK** tanpa mengalami *crash*."

---

### BAGIAN 6: SPESIFIKASI 5 — KONTRAK OPENAPI 3.0 & POSTMAN (08:30 - 09:30)
> **Tampilan Layar:** 
> 1. Buka VS Code pada file `docs/openapi-sipus-v1.yaml`.
> 2. Buka folder `docs/` memperlihatkan file koleksi Postman dan folder `screenshots/`.

**Teks yang Anda Ucapkan:**
> "Spesifikasi kelima berbobot 15 persen adalah **Kontrak Layanan, Koleksi Postman, dan Dokumentasi Pengujian**.  
> 
> Di era rekayasa perangkat lunak modern, dokumentasi API bukan sekadar catatan teks, melainkan kontrak formal mesin (*Machine-Readable Contract*).  
> Bersama asisten AI, saya telah menyusun spesifikasi OpenAPI 3.0 secara komprehensif pada berkas `docs/openapi-sipus-v1.yaml`. Berkas ini mendefinisikan seluruh skema data, skema keamanan Bearer Token Sanctum, parameter kueri, serta representasi respon sukses maupun kode kesalahan RFC 7807.  
> 
> Selain itu, berkas koleksi Postman yang telah kita demokan tadi telah diekspor dan tersimpan rapi di `docs/SIPUS-Del-API-v1.postman_collection.json`.  
> Seluruh bukti tangkapan layar pengujian asli dari komputer saya juga telah didokumentasikan di folder `docs/screenshots/` dan terpajang rapi pada berkas `README.md` repositori GitHub."

---

### BAGIAN 7: REFLEKSI, INTEGRITAS AI, & PENUTUP (09:30 - 10:00)
> **Tampilan Layar:** Tampilkan wajah Anda atau kembali ke halaman web utama SIPUS-Del.

**Teks yang Anda Ucapkan:**
> "Sebagai penutup dan refleksi: Melalui praktikum Minggu ke-9 ini, saya memperoleh pemahaman yang sangat mendalam mengenai perbedaan nyata antara paradigma SOAP yang kaku berbasis XML dan arsitektur RESTful yang fleksibel berbasis JSON.  
> 
> Kolaborasi saya bersama AI dalam proyek ini membuktikan bahwa kecerdasan buatan dapat menjadi akselerator belajar yang sangat hebat—membantu saya memahami konsep abstrak seperti HATEOAS, transaksi ACID dengan *pessimistic locking*, hingga standarisasi RFC 7807 secara langsung melalui kode yang nyata dan teruji.  
> 
> Seluruh kode sumber, dokumentasi, dan bukti pengujian telah berhasil di-commit dan di-push ke branch **`week9-rest-api`** di repositori GitHub saya.  
> 
> Demikian presentasi hasil praktikum Minggu 9 SIPUS-Del ini saya sampaikan. Terima kasih banyak atas bimbingan dan arahan Bapak Chandro Pardede.  
> Salam sehat, dan Tuhan memberkati. Horas!"

---
*Dokumen skenario ini disusun khusus untuk Josef Christian Marpaung (12S24036) — Institut Teknologi Del (2026).*
