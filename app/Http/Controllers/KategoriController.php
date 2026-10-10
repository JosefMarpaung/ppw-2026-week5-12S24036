<?php

// Mendeklarasikan namespace controller agar terorganisir di dalam direktori Http/Controllers
namespace App\Http\Controllers;

// Mengimpor Model Kategori untuk berinteraksi dengan tabel kategoris di basis data
use App\Models\Kategori;
// Mengimpor kelas Request untuk menangkap payload data yang dikirimkan oleh pengguna
use Illuminate\Http\Request;

// Mendefinisikan kelas KategoriController yang mewarisi sifat dari kelas Controller induk
class KategoriController extends Controller
{
    /**
     * 1. INDEX: Menampilkan daftar seluruh kategori dengan pagination.
     */
    public function index()
    {
        // Mengambil seluruh data kategori yang diurutkan dari yang terbaru, dipaginasi 10 data per halaman
        $kategoris = Kategori::latest()->paginate(10);

        // Mengirimkan variabel $kategoris ke file tampilan 'resources/views/kategori/index.blade.php'
        return view('kategori.index', compact('kategoris'));
    }

    /**
     * 2. CREATE: Menampilkan formulir input untuk menambah kategori baru.
     */
    public function create()
    {
        // Merender view form penambahan kategori
        return view('kategori.create');
    }

    /**
     * 3. STORE: Menyimpan data kategori baru ke basis data setelah divalidasi.
     */
    public function store(Request $request)
    {
        // Validasi ketat sisi server: kode_kategori wajib unik dan nama_kategori wajib diisi
        $validated = $request->validate([
            'kode_kategori' => 'required|string|max:20|unique:kategoris,kode_kategori', // Wajib diisi, maksimal 20 huruf, dan tidak boleh kembar
            'nama_kategori' => 'required|string|max:100',                              // Wajib diisi berupa string maksimal 100 karakter
        ]);

        // Menyimpan data yang sudah lolos validasi ke tabel kategoris via Eloquent Mass Assignment
        Kategori::create($validated);

        // Mengarahkan kembali pengguna ke halaman daftar kategori disertai pesan sukses (Flash Message)
        return redirect()->route('kategori.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    /**
     * 4. SHOW: Menampilkan detail satu kategori beserta daftar buku yang dimilikinya.
     */
    public function show(Kategori $kategori)
    {
        // Memuat relasi bukus secara Eager Loading untuk mencegah N+1 problem saat melihat detail kategori
        $kategori->load('bukus');

        // Mengirimkan data kategori ke view detail 'kategori.show'
        return view('kategori.show', compact('kategori'));
    }

    /**
     * 5. EDIT: Menampilkan formulir untuk mengubah data kategori yang sudah ada.
     */
    public function edit(Kategori $kategori)
    {
        // Mengirimkan objek $kategori ke view form edit 'kategori.edit' melalui Route Model Binding
        return view('kategori.edit', compact('kategori'));
    }

    /**
     * 6. UPDATE: Memperbarui data kategori di basis data berdasarkan masukan pengguna.
     */
    public function update(Request $request, Kategori $kategori)
    {
        // Validasi ketat sisi server: kode_kategori unik kecuali untuk ID kategori yang sedang diedit ini
        $validated = $request->validate([
            'kode_kategori' => 'required|string|max:20|unique:kategoris,kode_kategori,' . $kategori->id, // Mengecualikan ID saat ini dari cek unique
            'nama_kategori' => 'required|string|max:100',
        ]);

        // Memperbarui atribut data kategori dengan nilai yang telah tervalidasi
        $kategori->update($validated);

        // Mengarahkan kembali ke halaman index dengan notifikasi sukses
        return redirect()->route('kategori.index')->with('success', 'Data kategori berhasil diperbarui!');
    }

    /**
     * 7. DESTROY: Menghapus data kategori dari basis data.
     */
    public function destroy(Kategori $kategori)
    {
        // Menghapus data kategori (dan otomatis menghapus buku terkait karena aturan cascadeOnDelete di migrasi)
        $kategori->delete();

        // Mengarahkan kembali ke halaman index dengan notifikasi sukses
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
