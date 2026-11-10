# PANDUAN LENGKAP & NASKAH PRESENTASI VIDEO PRAKTIKUM MINGGU 9 (10 MENIT)
## Pemrograman & Pengujian Aplikasi Web (12S3101) — Institut Teknologi Del
**Presenter:** Josef Christian Marpaung (NIM: 12S24036)  
**Dosen Pengampu:** Chandro Pardede, S.Kom., M.Sc.  
**Format:** Skenario Layar $\rightarrow$ Hint / Glosarium Istilah $\rightarrow$ Naskah Penjelasan (*Sudut Pandang: "Saya dengan Bantuan AI"*)

---

## 🎬 SKENARIO 1: PEMBUKAAN & PENGANTAR KOLABORASI AI (Menit 00:00 – 01:00)

### 🖥️ Aksi Layar & Visual:
- **Layar yang ditampilkan:** Buka web browser di halaman repositori GitHub Anda:  
  `https://github.com/JosefMarpaung/ppw-2026-week5-12S24036/tree/week9-rest-api`
- **Arah kursor:** Sorot nama repositori, branch `week9-rest-api`, dan nama Anda di README.

### 💡 Hint & Istilah Kunci (Pelajari ini agar percaya diri saat bicara):
- **Web Services Terdistribusi:** Sistem perangkat lunak yang dirancang untuk mendukung interaksi dan pertukaran data antar-aplikasi yang berbeda melalui jaringan internet/intranet.
- **Pair Programming dengan AI:** Metode pengembangan perangkat lunak modern di mana saya bertindak sebagai pengemudi (*driver/decision maker*) yang mengarahkan logika arsitektur dan bisnis modul, sementara AI bertindak sebagai navigator (*co-pilot*) yang membantu memvalidasi standar sintaks dan kepatuhan RFC.
- **Stateless:** Sifat arsitektur di mana server tidak menyimpan status sesi (*session*) pengguna di memori server; setiap permintaan (*request*) yang datang harus membawa kredensialnya sendiri (token).

### 🗣️ Naskah Penjelasan 1 (Ucapkan ini):
> "Selamat pagi, siang, atau sore, yang terhormat Bapak Chandro Pardede, S.Kom., M.Sc., selaku dosen pengampu mata kuliah Pemrograman dan Pengujian Aplikasi Web.  
> 
> Perkenalkan, nama saya **Josef Christian Marpaung** dengan NIM **12S24036**, mahasiswa program studi S1 Sistem Informasi Institut Teknologi Del angkatan 2024.  
> 
> Pada kesempatan video kali ini, saya akan mendemonstrasikan secara langsung hasil pengerjaan proyek **SIPUS-Del Minggu ke-9**. Fokus utama praktikum ini adalah membangun arsitektur **Web Services Terdistribusi**, mengimplementasikan protokol **SOAP**, serta merancang layanan modern **RESTful Web API versi 1** pada framework Laravel 11.  
> 
> Dalam menyelesaikan tugas ini, saya menerapkan pendekatan **Pair Programming bersama AI Assistant**. Saya memegang kendali penuh atas pemahaman modul, aturan bisnis perpustakaan, dan pengambilan keputusan teknis, sementara AI membantu saya sebagai mitra diskusi untuk merapikan struktur kode, memastikan kepatuhan standar RFC internasional, serta merancang kontrak dokumen OpenAPI.  
> 
> Seluruh kode sumber telah saya sinkronkan di branch **`week9-rest-api`**. Sekarang, mari saya tunjukkan implementasi dari lima spesifikasi teknis terukur yang diwajibkan modul."

---

## 🎬 SKENARIO 2: ANALISIS KELAYAKAN & ENDPOINT SOAP BEBAS PUSTAKA (Menit 01:00 – 02:30)
*(Bobot Modul: 15%)*

### 🖥️ Aksi Layar & Visual:
1. Di browser GitHub, gulir ke bawah ke bagian **Analisis Kebutuhan & Kelayakan Web Services (SOAP vs REST)** pada `README.md`.
2. Beralih ke jendela **Postman Desktop**, buka koleksi di bilah kiri:  
   👉 Klik request **`POST 1. SOAP - Check Clearance Status (Yudisium)`**.
3. Tunjukkan tab **Body** yang berisi XML `<soap:Envelope>`.
4. Klik tombol biru **`Send`**, lalu sorot respon XML di area bawah yang berstatus **`200 OK`**.

### 💡 Hint & Istilah Kunci:
- **Provider vs Consumer:** Provider adalah pihak yang menyediakan/menyajikan API (SIPUS-Del menyediakan data buku). Consumer adalah pihak yang memanggil/mengonsumsi API pihak lain (SIPUS-Del memanggil API luar OpenLibrary).
- **SOAP (Simple Object Access Protocol):** Protokol pertukaran pesan berbasis XML yang sangat ketat, kaku, dan memiliki kontrak formal (WSDL). Cocok untuk sistem korporat/kampus lama (*legacy*).
- **WSDL (Web Services Description Language):** Dokumen XML yang berfungsi sebagai 'buku panduan resmi' atau kontrak yang menjelaskan fungsi apa saja yang tersedia pada web service SOAP.
- **SOAP Envelope & Fault:** *Envelope* adalah amplop pembungkus data XML. *Fault* adalah blok pesan error standar SOAP jika XML yang dikirim cacat atau tidak lengkap.

### 🗣️ Naskah Penjelasan 2 (Ucapkan ini):
> "Masuk ke spesifikasi pertama berbobot 15 persen: **Analisis Kelayakan Arsitektur dan Endpoint SOAP Bebas Pustaka**.  
> 
> Seperti yang saya paparkan pada berkas `README.md`, sistem SIPUS-Del memiliki peran ganda: sebagai **Service Provider** katalog dan sirkulasi, sekaligus sebagai **Service Consumer** yang menarik referensi buku eksternal.  
> 
> Dalam analisis kelayakan, saya memutuskan arsitektur **RESTful API** digunakan untuk aplikasi Mobile dan Kiosk perpustakaan karena payload JSON-nya jauh lebih ringan, hemat kuota internet, dan mudah diproses oleh perangkat modern. Namun, untuk integrasi sistem administrasi akademik kampus lama—khususnya validasi bebas pustaka mahasiswa menjelang yudisium kelulusan—saya mengimplementasikan protokol **SOAP**.  
> 
> Dengan bantuan AI, saya membuat pengendali `LibrarySoapController.php` dengan dua rute:
> - Rute pertama adalah `GET /soap/sipus.wsdl` yang menyajikan kontrak resmi operasi `CheckClearanceStatus`.
> - Rute kedua adalah `POST /soap/sipus` untuk menerima pesan XML.
> 
> Mari kita buktikan di Postman!  
> Di layar Postman ini, saya membuka request nomor 1. Di tab Body, saya mengirimkan dokumen `<soap:Envelope>` yang membawa NIM saya: **`12S24036`**.  
> Sekarang saya klik tombol **Send**...  
> Server membalas dengan status hijau **200 OK**, dan mengembalikan respon XML yang menyatakan status clearance saya adalah **`bebas_pinjam`**, karena saya tidak memiliki tunggakan peminjaman buku. Jika tag NIM dikosongkan, sistem saya telah dilengkapi penanganan yang otomatis membalas dengan pesan standar **`<soap:Fault>`**."

---

## 🎬 SKENARIO 3: REST API PROVIDER KATALOG BUKU (/bukus) (Menit 02:30 – 04:30)
*(Bobot Modul: 25%)*

### 🖥️ Aksi Layar & Visual:
1. Di Postman, klik request **`POST 2. Auth - Login Pustakawan (Stateless Token)`** $\rightarrow$ Klik **`Send`** (tunjukkan respon token).
2. Klik request **`GET 3. Katalog - GET Daftar Buku Terpaginasi + HATEOAS`** $\rightarrow$ Klik **`Send`** (tunjukkan pagination meta).
3. Klik request **`POST 4. Katalog - POST Tambah Buku Baru`** $\rightarrow$ Tunjukkan Bearer Token di tab *Authorization*, lalu klik **`Send`** (tunjukkan status `201 Created` dan tab *Headers* yang berisi `Location`).
4. Klik sekilas request **`PUT 5a`** dan **`PATCH 5b`** untuk menjelaskan perbedaannya.

### 💡 Hint & Istilah Kunci:
- **N+1 Query Problem:** Masalah performa fatal di mana aplikasi mengeksekusi 1 kueri untuk mengambil daftar buku, ditambah N kueri berulang untuk mengambil data kategori masing-masing buku. Kita atasi dengan **Eager Loading** (`with('kategori')`).
- **Sanctum Bearer Token:** Token string terenkripsi yang dibawa klien pada header otorisasi sebagai tanda pengenal resmi tanpa memerlukan session cookies.
- **201 Created & Header Location:** Standar baku REST di mana ketika data berhasil dibuat, server merespons kode 201 dan melampirkan alamat URL data baru tersebut di header `Location`.
- **Semantik PUT vs PATCH:** **PUT** mengganti seluruh isi entitas secara utuh (*Full Replacement*). **PATCH** hanya memodifikasi sebagian atribut yang dikirimkan saja (*Partial Update*).

### 🗣️ Naskah Penjelasan 3 (Ucapkan ini):
> "Sekarang saya melangkah ke spesifikasi kedua berbobot 25 persen: **Implementasi REST API Provider Katalog Buku pada rute `/api/v1/bukus`** yang memenuhi *Richardson Maturity Model* level 2 dan level 3.  
> 
> Untuk mengamankan endpoint admin, saya mengaktifkan Laravel Sanctum. Di Postman, saya jalankan request nomor 2: Login Pustakawan.  
> Saya klik **Send**... Server mengembalikan status **200 OK** bersama **Bearer Token** yang akan saya gunakan sebagai kunci akses.  
> 
> Selanjutnya, kita buka request nomor 3: `GET /api/v1/bukus`.  
> Saya klik **Send**... Koleksi buku tampil dalam format JSON terpaginasi lengkap dengan metadata `current_page` dan `total`. Di backend, kueri database telah saya optimasi bersama asisten AI menggunakan Eager Loading `with('kategori')` dan `withCount('peminjamans')`, sehingga **100 persen bebas dari masalah N+1 Query Problem**.  
> 
> Sekarang saya demokan penambahan buku baru melalui `POST /api/v1/bukus`.  
> Request ini mewajibkan Bearer Token. Di tab Body, saya mengirimkan data buku baru berjudul *Arsitektur Web Modern dan RESTful API* atas nama saya sendiri sebagai penulis.  
> Saya klik **Send**... Statusnya keluar **201 Created**. Dan jika kita lihat tab Headers di respon bawah, terdapat header **`Location`** yang mengarahkan langsung ke URL buku baru tersebut sesuai standar ketat HTTP.  
> 
> Terakhir, untuk membuktikan kaidah semantik HTTP Verbs:
> - Pada operasi **PUT**, saya mewajibkan seluruh field buku dikirim lengkap untuk mengganti representasi buku seutuhnya.
> - Sedangkan pada operasi **PATCH**, saya menerapkan validasi `sometimes|required`, sehingga pustakawan bisa mengubah stok buku saja tanpa harus mengirim ulang seluruh data lainnya."

---

## 🎬 SKENARIO 4: TRANSAKSI SIRKULASI ACID & KONTROL ATURAN BISNIS (Menit 04:30 – 06:45)
*(Bobot Modul: 30%)*

### 🖥️ Aksi Layar & Visual:
1. Di Postman, buka request **`POST 6a. Sirkulasi - POST Peminjaman Buku ACID`**.
2. Tunjukkan Body JSON (`buku_id: 2`, `nim_peminjam: 12S24036`).
3. Klik **`Send`** $\rightarrow$ Status **`201 Created`**.
4. Pindah ke browser tab web SIPUS-Del `http://127.0.0.1:8000/buku`, tekan **`F5 (Refresh)`** $\rightarrow$ Perlihatkan angka *Sedang Dipinjam* bertambah dan stok buku berkurang.
5. Kembali ke Postman, klik tombol **`Send`** sekali lagi $\rightarrow$ Perlihatkan status **`409 Conflict`**.
6. Buka request pengembalian buku $\rightarrow$ Ubah method ke **`PATCH`** pada URL `/api/v1/peminjamans/{id}/kembalikan` $\rightarrow$ Klik **`Send`** (Status `200 OK`).

### 💡 Hint & Istilah Kunci:
- **ACID (Atomicity, Consistency, Isolation, Durability):** Jaminan transaksi database di mana semua langkah harus berhasil bersamaan (atomik), data tetap konsisten, transaksi terisolasi aman, dan tersimpan permanen.
- **Pessimistic Locking (`lockForUpdate()`):** Mengunci baris database secara eksklusif selama transaksi berlangsung agar tidak ada pengguna lain yang bisa mengubah data tersebut di saat yang sama (*mencegah Race Condition*).
- **Race Condition:** Situasi bentrok di mana dua pengguna berebut meminjam 1 eksemplar buku terakhir di detik yang persis sama.
- **409 Conflict:** Kode status HTTP yang menandakan permintaan ditolak karena bertentangan dengan aturan kondisi bisnis sistem saat ini (misal: duplikasi pinjaman aktif).

### 🗣️ Naskah Penjelasan 4 (Ucapkan ini):
> "Kita masuk ke bagian terpenting dengan bobot terbesar, yaitu **30 persen: REST API Transaksi Sirkulasi Peminjaman ACID dan Bearer Token**.  
> 
> Pada berkas `PeminjamanApiController.php`, seluruh transaksi sirkulasi saya bungkus di dalam penutupan `DB::transaction()` untuk menjamin pilar ACID. Selain itu, untuk menuntaskan masalah *Race Condition*—ketika dua mahasiswa berebut meminjam satu sisa buku terakhir secara bersamaan—saya menerapkan **Pessimistic Locking** menggunakan fungsi `lockForUpdate()`.  
> 
> Mari saya buktikan tiga skenario aturan bisnis wajib modul:  
> 
> **Skenario 1: Penolakan Stok Habis (422 Unprocessable Content).**  
> Pada pengujian sebelumnya, ketika saya mencoba meminjam buku yang stoknya 0, sistem menolak seketika dengan status **422** dan pesan bahwa stok buku sedang habis.  
> 
> **Skenario 2: Peminjaman Sukses (201 Created).**  
> Sekarang, saya meminjam buku nomor 2 yang stoknya tersedia, menggunakan NIM saya `12S24036`.  
> Saya klik **Send**... Statusnya keluar **201 Created**, dan peminjaman berhasil dicatat.  
> Mari kita buktikan integrasinya ke antarmuka web Blade. Saya buka browser di alamat `http://127.0.0.1:8000/buku` lalu saya refresh...  
> Terlihat kartu analitik di dashboard: angka **Sedang Dipinjam** langsung bertambah, dan stok buku fisik di tabel otomatis terpotong satu eksemplar. Ini membuktikan sinkronisasi API dan Web berjalan *real-time*.  
> 
> **Skenario 3: Penolakan Duplikasi Pinjaman Aktif (409 Conflict).**  
> Jika seorang mahasiswa yang belum mengembalikan buku mencoba meminjam buku yang sama lagi di Postman... Saya klik tombol **Send** sekali lagi...  
> Sistem langsung menolak dengan status **409 Conflict** dan pesan bahwa mahasiswa dengan NIM ini masih memiliki pinjaman aktif yang belum tuntas.  
> 
> Dan ketika mahasiswa mengembalikan buku melalui endpoint **PATCH `/api/v1/peminjamans/{id}/kembalikan`**, statusnya berganti menjadi **200 OK**, status sirkulasi menjadi 'dikembalikan', dan stok fisik buku otomatis dipulihkan bertambah satu."

---

## 🎬 SKENARIO 5: HATEOAS DINAMIS, RFC 7807, & OPENLIBRARY CONSUMER (Menit 06:45 – 08:30)
*(Bobot Modul: 15%)*

### 🖥️ Aksi Layar & Visual:
1. Di Postman, buka respon `GET /api/v1/bukus` atau `GET /api/v1/peminjamans`.
2. Arahkan kursor dan perbesar (*zoom*) bagian JSON yang bertuliskan **`_links`**.
3. Tunjukkan respon error 422 atau 409 yang memiliki atribut `type`, `title`, `status`, `detail`, dan `instance`.
4. Buka request **`GET 7. Consumer - GET Referensi Eksternal OpenLibrary`** $\rightarrow$ Klik **`Send`** $\rightarrow$ Tunjukkan status **`200 OK`**.

### 💡 Hint & Istilah Kunci:
- **HATEOAS (Hypermedia As The Engine Of Application State):** Tingkat kematangan REST paling tinggi (Level 3), di mana API menyertakan tautan navigasi (`_links`) ke aksi berikutnya yang diizinkan sesuai kondisi status data saat ini.
- **RFC 7807 (Problem Details for HTTP APIs):** Standar internasional format JSON khusus pesan error dengan Content-Type `application/problem+json` yang berisi 5 field baku: `type`, `title`, `status`, `detail`, dan `instance`.
- **REST Service Consumer:** Posisi aplikasi Laravel kita yang bertindak sebagai klien yang memanggil API pihak luar (eksternal) menggunakan HTTP Client facade (`Http::timeout()`).

### 🗣️ Naskah Penjelasan 5 (Ucapkan ini):
> "Spesifikasi keempat berbobot 15 persen adalah **Kontrol Hypermedia HATEOAS, Standarisasi Error RFC 7807, dan REST Service Consumer**.  
> 
> Untuk mencapai tingkat kematangan RESTful level 3, saya merancang kontrol tautan navigasi dinamis pada lapisan *JsonResource*:  
> - Pada `BukuResource`: Objek `_links.pinjam_buku` **hanya akan muncul** jika stok buku lebih besar dari 0. Jika stok habis, tautan pinjam ini otomatis disembunyikan oleh sistem.  
> - Pada `PeminjamanResource`: Tautan `_links.kembalikan_buku` **hanya muncul** jika status sirkulasi masih `'dipinjam'`.  
> 
> Selanjutnya, untuk penanganan error global di seluruh aplikasi, saya mengonfigurasi berkas `bootstrap/app.php`. Semua kegagalan otentikasi 401, data tidak ditemukan 404, hingga kegagalan validasi 422 tidak lagi menampilkan format acak, melainkan tunduk pada standar baku internasional **RFC 7807 Problem Details**. Setiap respon error membawa lima atribut resmi: `type`, `title`, `status`, `detail`, dan `instance`.  
> 
> Selain itu, modul juga mewajibkan sistem kita bertindak sebagai **REST Service Consumer**.  
> Saya membuat endpoint `GET /api/v1/bukus/{id}/referensi-eksternal` yang secara otomatis menghubungi API publik **OpenLibrary** via internet menggunakan fasad `Http::timeout()`.  
> Mari kita lihat di Postman request nomor 7... Saya klik **Send**...  
> Responnya sukses **200 OK**! Data metadata buku dari repositori eksternal berhasil ditarik dan disinkronkan. Saya juga melengkapinya dengan *Smart Fallback*, sehingga jika koneksi internet luar lambat atau terputus, sistem tetap menyajikan data cadangan dengan aman tanpa pernah mengalami *crash*."

---

## 🎬 SKENARIO 6: KONTRAK OPENAPI 3.0, KOLEKSI POSTMAN, & DOKUMENTASI (Menit 08:30 – 09:30)
*(Bobot Modul: 15%)*

### 🖥️ Aksi Layar & Visual:
1. Buka jendela **VS Code / Editor**.
2. Buka berkas **`docs/openapi-sipus-v1.yaml`** $\rightarrow$ Gulir sekilas memperlihatkan paths, schemas, dan securityScheme Bearer.
3. Buka folder explorer di VS Code, perlihatkan berkas **`docs/SIPUS-Del-API-v1.postman_collection.json`** dan folder **`docs/screenshots/`**.

### 💡 Hint & Istilah Kunci:
- **OpenAPI 3.0 Specification:** Standar deskripsi format terbuka (berbasis YAML/JSON) yang mendokumentasikan seluruh kontrak antarmuka REST API agar dapat dibaca oleh manusia maupun mesin (*machine-readable*).
- **Postman Collection:** Kumpulan request API siap pakai yang memuat method, URL, headers, variabel environment, dan body data pengujian yang dapat dibagikan (*shareable*).

### 🗣️ Naskah Penjelasan 6 (Ucapkan ini):
> "Spesifikasi kelima berbobot 15 persen adalah **Kontrak Layanan OpenAPI 3.0, Koleksi Postman, dan Dokumentasi Bukti Pengujian**.  
> 
> Di layar VS Code ini, saya menampilkan berkas kontrak resmi `docs/openapi-sipus-v1.yaml`. Berkas spesifikasi OpenAPI versi 3.0 ini telah saya susun secara lengkap bersama asisten AI, mendefinisikan seluruh jalur rute, skema otentikasi Bearer Token Sanctum, parameter kueri, hingga definisi skema model dan format error RFC 7807.  
> 
> Seluruh skenario pengujian Postman yang saya peragakan hari ini juga telah saya ekspor ke dalam berkas `docs/SIPUS-Del-API-v1.postman_collection.json`.  
> Dan pada folder `docs/screenshots/`, seluruh bukti tangkapan layar pengujian asli dari komputer saya telah didokumentasikan dengan rapi dan disematkan langsung ke dalam berkas `README.md` repositori."

---

## 🎬 SKENARIO 7: KESIMPULAN, INTEGRITAS AI, & PENUTUP (Menit 09:30 – 10:00)

### 🖥️ Aksi Layar & Visual:
- Beralih kembali menampilkan wajah Anda di kamera secara penuh, atau kembali ke halaman web utama SIPUS-Del `http://127.0.0.1:8000/buku`.

### 💡 Hint & Istilah Kunci:
- **Refleksi Pembelajaran:** Penarikan kesimpulan personal mengenai apa yang dipelajari selama praktikum (perbedaan konkret SOAP vs REST, manajemen transaksi ACID, dan etika kolaborasi AI).

### 🗣️ Naskah Penjelasan 7 (Ucapkan ini):
> "Sebagai kesimpulan dan penutup presentasi saya: Praktikum Minggu ke-9 ini memberikan pemahaman yang sangat nyata bagi saya mengenai bagaimana sistem enterprise modern saling bertukar data secara terdistribusi. Saya kini memahami secara mendalam perbedaan antara protokol SOAP yang kaku namun terikat kontrak formal, dengan arsitektur RESTful yang sangat efisien dan fleksibel.  
> 
> Bekerja berdampingan dengan AI dalam praktikum ini mengajarkan saya bahwa AI adalah akselerator belajar yang sangat kuat. Melalui bimbingan AI sebagai *co-pilot*, saya dapat mengimplementasikan konsep tingkat lanjut seperti HATEOAS, Pessimistic Locking, dan kepatuhan RFC secara benar dan sesuai standar industri.  
> 
> Seluruh kode program, dokumentasi, dan bukti pengujian telah tersimpan dan terdorong aman di repositori GitHub saya pada branch **`week9-rest-api`**.  
> 
> Demikian presentasi ini saya sampaikan. Terima kasih banyak atas perhatian dan bimbingan Bapak Chandro Pardede.  
> Salam sehat, salam integritas, dan Horas!"

---
*(Naskah ini siap digunakan untuk rekaman video. Cetak atau buka di layar kedua sambil Anda melakukan demo di laptop!).*
