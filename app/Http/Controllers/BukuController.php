<?php

// Mendeklarasikan namespace controller agar terintegrasi dengan arsitektur controller Laravel
namespace App\Http\Controllers;

// Mengimpor Model Buku untuk manipulasi data katalog buku
use App\Models\Buku;
// Mengimpor Model Kategori untuk memuat opsi dropdown kategori pada formulir buku
use App\Models\Kategori;
// Mengimpor kelas Request untuk memproses data input dari pengguna
use Illuminate\Http\Request;

// Mendefinisikan kelas BukuController yang menangani alur interaksi antarmuka dan data buku
class BukuController extends Controller
{
    /**
     * 1. INDEX: Menampilkan daftar buku dengan teknik Eager Loading dan Pagination.
     */
    public function index()
    {
        // PERINGATAN MODUL: Menggunakan with('kategori') untuk EAGER LOADING guna membasmi N+1 Query Problem
        // Mengambil data buku terbaru dengan paginasi 10 entri per halaman
        $bukus = Buku::with('kategori')->latest()->paginate(10);

        // Merender tampilan 'resources/views/buku/index.blade.php' dan mengoper data $bukus
        return view('buku.index', compact('bukus'));
    }

    /**
     * 2. CREATE: Menampilkan formulir penambahan buku baru dan opsi kategorinya.
     */
    public function create()
    {
        // Mengambil seluruh data kategori yang diurutkan abjad untuk disajikan pada pilihan <select>
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        // Merender tampilan form tambah buku 'buku.create'
        return view('buku.create', compact('kategoris'));
    }

    /**
     * 3. STORE: Menyimpan buku baru ke database dengan validasi server-side yang ketat.
     */
    public function store(Request $request)
    {
        // Validasi ketat sisi server sesuai spesifikasi modul
        $validated = $request->validate([
            'isbn'         => 'required|string|max:20|unique:bukus,isbn',             // ISBN harus unik
            'judul'        => 'required|string|min:5|max:200',                       // Judul minimal 5 karakter
            'penulis'      => 'required|string|max:100',                             // Penulis wajib diisi
            'penerbit'     => 'required|string|max:100',                             // Penerbit wajib diisi
            'tahun_terbit' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 1), // Format tahun 4 digit valid
            'kategori_id'  => 'required|exists:kategoris,id',                        // Kategori harus valid & ada di tabel kategoris
            'stok'         => 'required|integer|min:0',                              // Stok minimal 0
            'sinopsis'     => 'nullable|string',                                     // Sinopsis bersifat opsional
        ]);

        // Menyimpan data buku yang tervalidasi ke dalam tabel bukus via Eloquent
        Buku::create($validated);

        // Mengarahkan kembali ke daftar katalog buku dengan notifikasi sukses
        return redirect()->route('buku.index')->with('success', 'Buku baru berhasil ditambahkan!');
    }

    /**
     * 4. SHOW: Menampilkan informasi detail satu buku beserta relasi kategorinya.
     */
    public function show(Buku $buku)
    {
        // Memuat data relasi kategori induknya secara Eager Loading
        $buku->load('kategori');

        // Merender tampilan detail buku 'buku.show'
        return view('buku.show', compact('buku'));
    }

    /**
     * 5. EDIT: Menampilkan formulir pengubahan data buku yang sudah ada.
     */
    public function edit(Buku $buku)
    {
        // Mengambil daftar seluruh kategori untuk pilihan dropdown saat edit
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        // Merender form ubah buku 'buku.edit' membawa data buku dan kategori terkait
        return view('buku.edit', compact('buku', 'kategoris'));
    }

    /**
     * 6. UPDATE: Memperbarui data buku pada basis data setelah divalidasi.
     */
    public function update(Request $request, Buku $buku)
    {
        // Validasi ketat sisi server: mengecualikan ISBN buku ini sendiri agar tidak dianggap duplikat
        $validated = $request->validate([
            'isbn'         => 'required|string|max:20|unique:bukus,isbn,' . $buku->id, // Abaikan ISBN buku saat ini
            'judul'        => 'required|string|min:5|max:200',
            'penulis'      => 'required|string|max:100',
            'penerbit'     => 'required|string|max:100',
            'tahun_terbit' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 1),
            'kategori_id'  => 'required|exists:kategoris,id',
            'stok'         => 'required|integer|min:0',
            'sinopsis'     => 'nullable|string',
        ]);

        // Memperbarui atribut data buku dengan data yang telah lolos validasi
        $buku->update($validated);

        // Mengarahkan kembali ke katalog buku dengan notifikasi sukses
        return redirect()->route('buku.index')->with('success', 'Data buku berhasil diperbarui!');
    }

    /**
     * 7. DESTROY: Menghapus data buku dari database.
     */
    public function destroy(Buku $buku)
    {
        // Menghapus data buku yang bersangkutan dari basis data
        $buku->delete();

        // Mengarahkan kembali ke daftar katalog buku dengan notifikasi sukses
        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus dari sistem!');
    }
}
