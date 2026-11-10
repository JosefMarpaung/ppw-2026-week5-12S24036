# PANDUAN TEKNIS PEMBUKTIAN & NASKAH LENGKAP VIDEO PRESENTASI PRAKTIKUM MINGGU 9
## Pemrograman & Pengujian Aplikasi Web (12S3101) — Institut Teknologi Del
**Presenter:** Josef Christian Marpaung (NIM: 12S24036)  
**Dosen Pengampu:** Chandro Pardede, S.Kom., M.Sc.  
**Durasi Total:** Tepat 10 Menit (600 Detik)  
**Format Runtut:** Skenario & Langkah Pembuktian $\rightarrow$ Hint / Istilah Kunci $\rightarrow$ Naskah Penjelasan

---

## 📋 PERSIAPAN APLIKASI SEBELUM MEREKAM (Wajib Standby)
1. **Terminal:** Buka terminal/cmd dan jalankan `php artisan serve` hingga tertulis `Server running on [http://127.0.0.1:8000]`.
2. **Postman Desktop:** Buka koleksi `PPW Week 9 - SIPUS-Del Web Services` di bilah kiri.
3. **Web Browser (Google Chrome):**
   - Tab 1: `http://127.0.0.1:8000/buku` (Dashboard katalog SIPUS-Del).
   - Tab 2: Halaman GitHub repositori Anda: `https://github.com/JosefMarpaung/ppw-2026-week5-12S24036/tree/week9-rest-api`.
4. **VS Code:** Buka folder proyek dengan file `docs/openapi-sipus-v1.yaml` sudah siap dibuka.

---

## 🎬 SKENARIO 1: PEMBUKAAN & PENGANTAR KOLABORASI AI (Menit 00:00 – 01:00)

### 🖥️ Langkah Pembuktian Layar (Apa yang Dilakukan & Ditunjukkan):
1. Tampilkan layar penuh browser pada tab repositori GitHub Anda.
2. Arahkan kursor mouse ke tulisan branch **`week9-rest-api`** di pojok kiri atas GitHub untuk membuktikan Anda berada di branch yang tepat.
3. Gulir sedikit ke bawah, sorot nama Anda pada `README.md`: **Josef Christian Marpaung (12S24036)**.

### 💡 Hint & Istilah Kunci:
- **Web Services Terdistribusi:** Sistem pertukaran data otomatis antar-aplikasi berbeda melalui jaringan.
- **Pair Programming dengan AI:** Metode pengembangan di mana saya memegang kendali atas logika bisnis, arsitektur, dan modul, sementara AI bertindak sebagai *co-pilot* untuk memvalidasi kepatuhan sintaks dan standar RFC internasional.
- **Stateless:** Server tidak menyimpan memori sesi login; setiap panggilan API wajib membawa kredensialnya sendiri (token).

### 🗣️ Naskah Penjelasan 1:
> *"Selamat pagi, siang, atau sore, yang terhormat Bapak Chandro Pardede, S.Kom., M.Sc., selaku dosen pengampu mata kuliah Pemrograman dan Pengujian Aplikasi Web.*  
> 
> *Perkenalkan, nama saya **Josef Christian Marpaung** dengan NIM **12S24036**, mahasiswa program studi S1 Sistem Informasi Institut Teknologi Del angkatan 2024.*  
> 
> *Pada video kali ini, saya akan mendemonstrasikan secara langsung hasil pengerjaan proyek **SIPUS-Del Minggu ke-9**. Fokus utama praktikum ini adalah membangun arsitektur **Web Services Terdistribusi**, mengimplementasikan protokol **SOAP**, serta merancang layanan modern **RESTful Web API versi 1** pada framework Laravel 11.*  
> 
> *Dalam menyelesaikan tugas ini, saya menerapkan pendekatan **Pair Programming bersama AI Assistant**. Saya memegang kendali penuh atas pemahaman modul, aturan bisnis perpustakaan, dan pengambilan keputusan teknis, sementara AI membantu saya sebagai mitra diskusi untuk merapikan struktur kode, memastikan kepatuhan standar RFC internasional, serta merancang kontrak dokumen OpenAPI.*  
> 
> *Seluruh kode sumber telah saya sinkronkan di branch **`week9-rest-api`**. Sekarang, mari saya tunjukkan implementasi dari lima spesifikasi teknis terukur yang diwajibkan modul."*

---

## 🎬 SKENARIO 2: ANALISIS KELAYAKAN & ENDPOINT SOAP BEBAS PUSTAKA (Menit 01:00 – 02:30)
*(Bobot Modul: 15%)*

### 🖥️ Langkah Pembuktian Layar (Apa yang Dilakukan & Diisi):
1. **Di Browser GitHub:** Gulir ke bagian tabel **Analisis Kebutuhan & Kelayakan Web Services (SOAP vs REST)** pada `README.md`. Sorot kolom perbandingan.
2. **Pindah ke Postman:**
   - Di bilah kiri, klik: **`POST 1. SOAP - Check Clearance Status (Yudisium)`**.
   - Tunjukkan URL yang terisi: `http://localhost:8000/soap/sipus`.
   - Klik tab **Headers**: Tunjukkan `Content-Type: text/xml; charset=utf-8`.
   - Klik tab **Body**: Sorot dokumen XML `<soap:Envelope>` yang berisi tag:
     ```xml
     <m:nim_peminjam>12S24036</m:nim_peminjam>
     ```
   - **Klik tombol biru `Send`**.
3. **Sorot Area Respon Bawah:**
   - Tunjukkan status hijau: **`200 OK`**.
   - Sorot tag `<status_clearance>bebas_pinjam</status_clearance>`.
   - Jelaskan bahwa jika tag NIM dikosongkan, sistem telah diprogram mereturn `<soap:Fault>`.

### 💡 Hint & Istilah Kunci:
- **Provider vs Consumer:** Provider = penyedia data API (SIPUS-Del). Consumer = pemakai/pemanggil API luar (SIPUS-Del memanggil OpenLibrary).
- **SOAP & WSDL:** Protokol pertukaran data berbasis XML yang kaku dan terikat kontrak formal (WSDL).
- **SOAP Envelope & Fault:** *Envelope* = amplop pembungkus data XML. *Fault* = format pesan error standar SOAP saat request tidak valid.

### 🗣️ Naskah Penjelasan 2:
> *"Masuk ke spesifikasi pertama berbobot 15 persen: **Analisis Kelayakan Arsitektur dan Endpoint SOAP Bebas Pustaka**.*  
> 
> *Seperti yang saya paparkan pada berkas README, sistem SIPUS-Del memiliki peran ganda: sebagai **Service Provider** katalog dan sirkulasi, sekaligus sebagai **Service Consumer** yang menarik referensi buku eksternal.*  
> 
> *Dalam analisis kelayakan, saya memutuskan arsitektur **RESTful API** digunakan untuk aplikasi Mobile dan Kiosk perpustakaan karena payload JSON-nya jauh lebih ringan, hemat kuota internet, dan mudah diproses oleh perangkat modern. Namun, untuk integrasi sistem akademik kampus lama—khususnya validasi bebas pustaka mahasiswa menjelang yudisium kelulusan—saya mengimplementasikan protokol **SOAP**.*  
> 
> *Dengan bantuan AI, saya membuat pengendali `LibrarySoapController.php` dengan dua rute:*  
> *- Rute pertama adalah `GET /soap/sipus.wsdl` yang menyajikan kontrak resmi operasi `CheckClearanceStatus`.*  
> *- Rute kedua adalah `POST /soap/sipus` untuk memproses pesan XML.*  
> 
> *Mari saya buktikan di Postman. Di request nomor 1, pada tab Body saya mengirimkan dokumen `<soap:Envelope>` yang membawa NIM saya: **`12S24036`**.*  
> *Sekarang saya klik tombol **Send**...*  
> *Server membalas status hijau **200 OK**, dan mengembalikan respon XML yang menyatakan status clearance saya adalah **`bebas_pinjam`**, karena saya tidak memiliki tunggakan peminjaman buku. Jika tag NIM dikosongkan, sistem saya telah dilengkapi penanganan yang otomatis membalas dengan pesan standar **`<soap:Fault>`**."*

---

## 🎬 SKENARIO 3: REST API PROVIDER KATALOG BUKU (/bukus) (Menit 02:30 – 04:30)
*(Bobot Modul: 25%)*

### 🖥️ Langkah Pembuktian Layar (Apa yang Dilakukan & Diisi):

#### 1. Pembuktian Login & Token Sanctum:
- Di Postman bilah kiri, klik: **`POST 2. Auth - Login Pustakawan (Stateless Token)`**.
- Tunjukkan tab **Body**: `email: pustakawan@del.ac.id`, `password: password123`.
- Klik **`Send`** $\rightarrow$ Sorot nilai `"access_token": "4|..."` yang terbit.

#### 2. Pembuktian GET Katalog (Bebas N+1):
- Klik: **`GET 3. Katalog - GET Daftar Buku Terpaginasi + HATEOAS`**.
- URL: `http://localhost:8000/api/v1/bukus?per_page=5&sort=judul&dir=asc`.
- Klik **`Send`** $\rightarrow$ Sorot metadata pagination di respon JSON (`current_page: 1`, `total: 5`).

#### 3. Pembuktian POST Tambah Buku (Status 201 Created + Header Location):
- Klik: **`POST 4. Katalog - POST Tambah Buku Baru (201 Created)`**.
- Klik tab **Authorization**: Tunjukkan tipe `Bearer Token` dengan token pustakawan.
- Klik tab **Body**: Tunjukkan data buku karya Anda:
  - `isbn`: `978-602-12036`
  - `judul`: `Arsitektur Web Modern dan RESTful API`
  - `penulis`: `Josef Christian Marpaung`
  - `stok`: `10`
- Klik **`Send`** $\rightarrow$ Tunjukkan status hijau **`201 Created`**.
- **PENTING:** Klik tab **Headers** di panel respon bawah, sorot baris:
  `Location: http://127.0.0.1:8000/api/v1/bukus/{id}`.

#### 4. Pembuktian Perbedaan Semantik PUT vs PATCH:
- Klik sekilas **`PUT 5a`**: Jelaskan bahwa seluruh atribut wajib dikirim ulang secara utuh (*Full Replacement*).
- Klik sekilas **`PATCH 5b`**: Tunjukkan body hanya berisi `{"stok": 12}` (*Partial Update* menggunakan `sometimes|required`).

### 💡 Hint & Istilah Kunci:
- **N+1 Query Problem:** Pemborosan kueri database (1 kueri buku + N kueri kategori berulang kali). Dicegah tuntas dengan **Eager Loading** (`with('kategori')`).
- **Sanctum Bearer Token:** Token keamanan sebagai tiket otorisasi resmi tanpa cookie/session.
- **201 Created & Header Location:** Standar REST saat resource baru dibuat; URL resource baru wajib disertakan di header `Location`.
- **PUT vs PATCH:** PUT = ganti seluruh isi entitas (*Full Replacement*). PATCH = ubah kolom tertentu saja (*Partial Update*).

### 🗣️ Naskah Penjelasan 3:
> *"Sekarang saya melangkah ke spesifikasi kedua berbobot 25 persen: **Implementasi REST API Provider Katalog Buku pada rute `/api/v1/bukus`** yang memenuhi Richardson Maturity Model level 2 dan level 3.*  
> 
> *Untuk mengamankan endpoint admin, saya mengaktifkan Laravel Sanctum. Di Postman, saya jalankan request nomor 2: Login Pustakawan.*  
> *Saya klik **Send**... Server mengembalikan status **200 OK** bersama **Bearer Token** yang akan saya gunakan sebagai kunci akses.*  
> 
> *Selanjutnya, kita buka request nomor 3: `GET /api/v1/bukus`.*  
> *Saya klik **Send**... Koleksi buku tampil dalam format JSON terpaginasi lengkap dengan metadata `current_page` dan `total`. Di backend, kueri database telah saya optimasi bersama asisten AI menggunakan Eager Loading `with('kategori')` dan `withCount('peminjamans')`, sehingga **100 persen bebas dari masalah N+1 Query Problem**.*  
> 
> *Sekarang saya demokan penambahan buku baru melalui `POST /api/v1/bukus`.*  
> *Request ini mewajibkan Bearer Token. Di tab Body, saya mengirimkan data buku baru berjudul 'Arsitektur Web Modern dan RESTful API' atas nama saya sendiri sebagai penulis.*  
> *Saya klik **Send**... Statusnya keluar **201 Created**. Dan jika kita lihat tab Headers di respon bawah, terdapat header **`Location`** yang mengarahkan langsung ke URL buku baru tersebut sesuai standar ketat HTTP.*  
> 
> *Terakhir, untuk membuktikan kaidah semantik HTTP Verbs:*  
> *- Pada operasi **PUT**, saya mewajibkan seluruh field buku dikirim lengkap untuk mengganti representasi buku seutuhnya.*  
> *- Sedangkan pada operasi **PATCH**, saya menerapkan validasi `sometimes|required`, sehingga pustakawan bisa mengubah stok buku saja tanpa harus mengirim ulang seluruh data lainnya."*

---

## 🎬 SKENARIO 4: TRANSAKSI SIRKULASI ACID & KONTROL ATURAN BISNIS (Menit 04:30 – 06:45)
*(Bobot Modul: 30%)*

### 🖥️ Langkah Pembuktian Layar (Apa yang Dilakukan & Diisi):

#### 1. Pembuktian Penolakan Stok Habis (422 Unprocessable Content):
- Di Postman, klik: **`POST 6c. Sirkulasi - Penolakan 422 Stok Buku Habis`**.
- Tunjukkan `buku_id: 1` (buku dengan stok 0).
- Klik **`Send`** $\rightarrow$ Sorot status **`422 Unprocessable Content`** dan pesan detail: *"Maaf, stok buku sedang habis (0 tersisa)"*.

#### 2. Pembuktian Peminjaman Sukses ACID (201 Created) + Sinkronisasi Web:
- Klik: **`POST 6a. Sirkulasi - POST Peminjaman Buku ACID`**.
- Tunjukkan Body JSON:
  ```json
  {
      "buku_id": 2,
      "nim_peminjam": "12S24036",
      "nama_peminjam": "Josef Christian Marpaung",
      "durasi_hari": 7
  }
  ```
- Klik **`Send`** $\rightarrow$ Status keluar **`201 Created`**.
- **LANGKAH PEMBUKTIAN WEB:** Beralih ke browser tab `http://127.0.0.1:8000/buku`, lalu tekan **F5 (Refresh)**.
  - Tunjukkan kartu analitik: angka **Sedang Dipinjam** bertambah.
  - Tunjukkan tabel: stok buku fisik terpotong otomatis 1 eksemplar.

#### 3. Pembuktian Penolakan Duplikasi Pinjaman Aktif (409 Conflict):
- Kembali ke Postman pada request yang sama (`POST 6a`), **klik tombol `Send` sekali lagi**.
- Sorot status jingga: **`409 Conflict`** dan pesan RFC 7807: *"Mahasiswa dengan NIM 12S24036 masih meminjam buku ini dan belum mengembalikannya"*.

#### 4. Pembuktian Pengembalian Buku via PATCH:
- Buka tab baru / ubah method ke **`PATCH`** dengan URL:
  `http://127.0.0.1:8000/api/v1/peminjamans/3/kembalikan`
- Klik **`Send`** $\rightarrow$ Status **`200 OK`**.
- Kembali ke browser, tekan **F5** $\rightarrow$ Tunjukkan stok buku pulih kembali (+1 eksemplar).

### 💡 Hint & Istilah Kunci:
- **ACID:** Jaminan transaksi database (semua sukses bersamaan atau dibatalkan total, data konsisten, aman, dan permanen).
- **Pessimistic Locking (`lockForUpdate()`):** Mengunci baris database secara eksklusif agar tidak ada transaksi lain yang mengubah data sebelum transaksi saat ini tuntas (*menangkal Race Condition*).
- **409 Conflict:** Status penolakan saat ada benturan status bisnis (duplikasi pinjaman aktif atau buku sudah dikembalikan).

### 🗣️ Naskah Penjelasan 4:
> *"Kita masuk ke bagian terpenting dengan bobot terbesar, yaitu **30 persen: REST API Transaksi Sirkulasi Peminjaman ACID dan Bearer Token**.*  
> 
> *Pada berkas `PeminjamanApiController.php`, seluruh transaksi sirkulasi saya bungkus di dalam penutupan `DB::transaction()` untuk menjamin pilar ACID. Selain itu, untuk menuntaskan masalah Race Condition—ketika dua mahasiswa berebut meminjam satu sisa buku terakhir secara bersamaan—saya menerapkan **Pessimistic Locking** menggunakan fungsi `lockForUpdate()`.*  
> 
> *Mari saya buktikan tiga skenario aturan bisnis wajib modul:*  
> 
> *Pertama, **Penolakan Stok Habis (422 Unprocessable Content)**: Ketika saya mencoba meminjam buku yang stoknya 0, sistem menolak seketika dengan status 422 dan pesan bahwa stok buku sedang habis.*  
> 
> *Kedua, **Peminjaman Sukses (201 Created)**: Sekarang, saya meminjam buku nomor 2 yang stoknya tersedia, menggunakan NIM saya `12S24036`.*  
> *Saya klik **Send**... Statusnya keluar **201 Created**, dan peminjaman berhasil dicatat.*  
> *Mari kita buktikan integrasinya ke antarmuka web Blade. Saya buka browser di alamat `http://127.0.0.1:8000/buku` lalu saya refresh...*  
> *Terlihat di dashboard: angka **Sedang Dipinjam** langsung bertambah, dan stok buku fisik di tabel otomatis terpotong satu eksemplar. Ini membuktikan sinkronisasi API dan Web berjalan real-time.*  
> 
> *Ketiga, **Penolakan Duplikasi Pinjaman Aktif (409 Conflict)**: Jika seorang mahasiswa yang belum mengembalikan buku mencoba meminjam buku yang sama lagi di Postman... Saya klik tombol **Send** sekali lagi...*  
> *Sistem langsung menolak dengan status **409 Conflict** dan pesan bahwa mahasiswa dengan NIM ini masih memiliki pinjaman aktif yang belum tuntas.*  
> 
> *Dan ketika mahasiswa mengembalikan buku melalui endpoint **PATCH `/api/v1/peminjamans/{id}/kembalikan`**, statusnya berganti menjadi **200 OK**, status sirkulasi menjadi 'dikembalikan', dan stok fisik buku otomatis dipulihkan bertambah satu."*

---

## 🎬 SKENARIO 5: HATEOAS DINAMIS, RFC 7807, & CONSUMER OPENLIBRARY (Menit 06:45 – 08:30)
*(Bobot Modul: 15%)*

### 🖥️ Langkah Pembuktian Layar (Apa yang Dilakukan & Diisi):

#### 1. Pembuktian HATEOAS Level 3:
- Buka respon `GET /api/v1/bukus`.
- Sorot blok **`_links`**: Tunjukkan bahwa pada buku dengan stok $> 0$ ada tautan `pinjam_buku`, sedangkan pada buku dengan stok 0 tautan tersebut hilang.
- Pada respon peminjaman: Tunjukkan bahwa tautan `kembalikan_buku` hanya muncul saat statusnya masih `'dipinjam'`.

#### 2. Pembuktian Format Error RFC 7807:
- Tunjukkan respon error 422 atau 409 tadi, sorot 5 field baku:
  1. `"type"`: URL dokumentasi error
  2. `"title"`: Judul masalah
  3. `"status"`: Kode status HTTP
  4. `"detail"`: Penjelasan humanis bahasa Indonesia
  5. `"instance"`: Endpoint yang diakses

#### 3. Pembuktian REST Service Consumer (OpenLibrary):
- Di Postman, klik: **`GET 7. Consumer - GET Referensi Eksternal OpenLibrary`**.
- URL: `http://127.0.0.1:8000/api/v1/bukus/2/referensi-eksternal`.
- Klik **`Send`** $\rightarrow$ Sorot status **`200 OK`** dan metadata buku eksternal yang ditarik.
- Jelaskan adanya *Smart Fallback Cache* jika server luar mengalami timeout.

### 💡 Hint & Istilah Kunci:
- **HATEOAS:** Kematangan REST tingkat tertinggi (Level 3), di mana respon JSON menyertakan tautan navigasi (`_links`) ke aksi berikutnya yang diperbolehkan.
- **RFC 7807 Problem Details:** Format standar internasional balasan error HTTP berformat `application/problem+json`.
- **REST Service Consumer:** Posisi aplikasi Laravel kita yang memanggil API luar (OpenLibrary) menggunakan HTTP Client.

### 🗣️ Naskah Penjelasan 5:
> *"Spesifikasi keempat berbobot 15 persen adalah **Kontrol Hypermedia HATEOAS, Standarisasi Error RFC 7807, dan REST Service Consumer**.*  
> 
> *Untuk mencapai tingkat kematangan RESTful level 3, saya merancang kontrol tautan navigasi dinamis pada lapisan JsonResource:*  
> *- Pada `BukuResource`: Objek `_links.pinjam_buku` **hanya akan muncul** jika stok buku lebih besar dari 0. Jika stok habis, tautan pinjam ini otomatis disembunyikan oleh sistem.*  
> *- Pada `PeminjamanResource`: Tautan `_links.kembalikan_buku` **hanya muncul** jika status sirkulasi masih `'dipinjam'`.*  
> 
> *Selanjutnya, untuk penanganan error global di seluruh aplikasi, saya mengonfigurasi berkas `bootstrap/app.php`. Semua kegagalan otentikasi 401, data tidak ditemukan 404, hingga kegagalan validasi 422 tunduk pada standar internasional **RFC 7807 Problem Details**. Setiap respon error membawa lima atribut resmi: `type`, `title`, `status`, `detail`, dan `instance`.*  
> 
> *Selain itu, modul juga mewajibkan sistem kita bertindak sebagai **REST Service Consumer**.*  
> *Saya membuat endpoint `GET /api/v1/bukus/{id}/referensi-eksternal` yang secara otomatis menghubungi API publik **OpenLibrary** menggunakan fasad `Http::timeout()`.*  
> *Mari kita lihat di Postman request nomor 7... Saya klik **Send**...*  
> *Responnya sukses **200 OK**! Data metadata buku dari repositori eksternal berhasil ditarik dan disinkronkan. Saya juga melengkapinya dengan Smart Fallback, sehingga jika koneksi internet luar lambat, sistem tetap menyajikan data cadangan dengan aman tanpa pernah mengalami crash."*

---

## 🎬 SKENARIO 6: KONTRAK OPENAPI 3.0, KOLEKSI POSTMAN, & DOKUMENTASI (Menit 08:30 – 09:30)
*(Bobot Modul: 15%)*

### 🖥️ Langkah Pembuktian Layar (Apa yang Dilakukan & Ditunjukkan):
1. Beralih ke **VS Code**:
   - Buka berkas **`docs/openapi-sipus-v1.yaml`**. Gulir sekilas memperlihatkan paths, schemas, dan securityScheme `bearerAuth`.
2. Di file explorer VS Code:
   - Sorot berkas **`docs/SIPUS-Del-API-v1.postman_collection.json`**.
   - Buka folder **`docs/screenshots/`**, perlihatkan file bukti pengujian (`01` s.d. `07`).
3. Beralih sebentar ke browser GitHub `README.md`, perlihatkan galeri screenshot pengujian yang terpajang rapi.

### 💡 Hint & Istilah Kunci:
- **OpenAPI 3.0 Specification:** Dokumen spesifikasi kontrak API berbasis YAML/JSON yang dapat dibaca manusia dan mesin (*machine-readable*).
- **Postman Collection:** File ekspor koleksi request Postman yang siap diuji kapan saja oleh penguji/dosen.

### 🗣️ Naskah Penjelasan 6:
> *"Spesifikasi kelima berbobot 15 persen adalah **Kontrak Layanan OpenAPI 3.0, Koleksi Postman, dan Dokumentasi Bukti Pengujian**.*  
> 
> *Di layar VS Code ini, saya menampilkan berkas kontrak resmi `docs/openapi-sipus-v1.yaml`. Berkas spesifikasi OpenAPI versi 3.0 ini telah saya susun secara lengkap bersama asisten AI, mendefinisikan seluruh jalur rute, skema otentikasi Bearer Token Sanctum, parameter kueri, hingga definisi skema model dan format error RFC 7807.*  
> 
> *Seluruh skenario pengujian Postman yang saya peragakan hari ini juga telah saya ekspor ke dalam berkas `docs/SIPUS-Del-API-v1.postman_collection.json`.*  
> *Dan pada folder `docs/screenshots/`, seluruh bukti tangkapan layar pengujian asli dari komputer saya telah didokumentasikan dengan rapi dan disematkan langsung ke dalam berkas README repositori."*

---

## 🎬 SKENARIO 7: KESIMPULAN, INTEGRITAS AI, & PENUTUP (Menit 09:30 – 10:00)

### 🖥️ Langkah Pembuktian Layar:
- Tampilkan wajah Anda di kamera secara penuh (*webcam full screen*), atau kembali ke halaman web utama SIPUS-Del `http://127.0.0.1:8000/buku`.

### 💡 Hint & Istilah Kunci:
- **Refleksi Pembelajaran:** Mengutarakan pemahaman konkret yang diperoleh dari praktikum dan nilai positif kolaborasi AI secara berintegritas.

### 🗣️ Naskah Penjelasan 7:
> *"Sebagai kesimpulan dan penutup presentasi saya: Praktikum Minggu ke-9 ini memberikan pemahaman yang sangat nyata bagi saya mengenai bagaimana sistem enterprise modern saling bertukar data secara terdistribusi. Saya kini memahami secara mendalam perbedaan antara protokol SOAP yang kaku namun terikat kontrak formal, dengan arsitektur RESTful yang sangat efisien dan fleksibel.*  
> 
> *Bekerja berdampingan dengan AI dalam praktikum ini mengajarkan saya bahwa AI adalah akselerator belajar yang sangat kuat. Melalui bimbingan AI sebagai co-pilot, saya dapat mengimplementasikan konsep tingkat lanjut seperti HATEOAS, Pessimistic Locking, dan kepatuhan RFC secara benar dan sesuai standar industri.*  
> 
> *Seluruh kode program, dokumentasi, dan bukti pengujian telah tersimpan dan terdorong aman di repositori GitHub saya pada branch **`week9-rest-api`**.*  
> 
> *Demikian presentasi ini saya sampaikan. Terima kasih banyak atas perhatian dan bimbingan Bapak Chandro Pardede.*  
> *Salam sehat, salam integritas, dan Horas!"*

---
*(Dokumen panduan teknis pembuktian ini selesai disusun untuk Josef Christian Marpaung - 12S24036).*
