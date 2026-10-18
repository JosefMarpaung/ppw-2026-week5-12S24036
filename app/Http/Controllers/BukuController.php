<?php

namespace App\Http\Controllers;

// Mengimpor Model Buku untuk manipulasi data katalog buku
use App\Models\Buku;
// Mengimpor Model Kategori untuk opsi dropdown kategori pada form & filter
use App\Models\Kategori;
// Mengimpor kelas Request untuk memproses data input dari form / URL query
use Illuminate\Http\Request;
// [KRITIS] Mengimpor Facade DB untuk menjalankan Fluent Query Builder (DB::table)
use Illuminate\Support\Facades\DB;

class BukuController extends Controller
{
    /**
     * 1. INDEX: Menampilkan Katalog Buku dengan withCount, Filter Dinamis when(), dan Statistik DB::table()
     * Sesuai Spesifikasi Teknis Wajib Poin 3 Modul Praktikum
     */
    public function index(Request $request)
    {
        // Menyimpan kata kunci pencarian terakhir ke dalam Session (Manajemen Sesi)
        if ($request->filled('search')) {
            session(['last_search_keyword' => $request->search]);
        }

        // 1. Eloquent Eager Loading relasi kategori + withCount('peminjamans') untuk membasmi N+1 Query
        // 2. Filter Dinamis dengan when() untuk kata kunci (judul/penulis/ISBN) dan kategori
        $bukus = Buku::with('kategori')
            ->withCount('peminjamans') // Menghitung relasi peminjaman secara efisien
            ->when($request->search, function ($query, $search) {
                // Mencari berdasarkan judul, penulis, ATAU nomor ISBN
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%")
                      ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->when($request->kategori_id, function ($query, $kategoriId) {
                // Filter berdasarkan kategori yang dipilih
                $query->where('kategori_id', $kategoriId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // [KRITIS] Mempertahankan parameter filter saat berpindah halaman paginasi

        // 3. Rekapitulasi Kartu Statistik Perpustakaan Menggunakan Fluent Query Builder (DB::table)
        $stats = [
            'total_judul'           => DB::table('bukus')->count(),
            'total_stok'            => (int) DB::table('bukus')->sum('stok'),
            'total_sedang_dipinjam' => DB::table('peminjamans')->where('status', 'dipinjam')->count(),
            'total_dikembalikan'    => DB::table('peminjamans')->where('status', 'dikembalikan')->count(),
        ];

        // Mengambil daftar seluruh kategori untuk opsi dropdown filter pencarian
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        // Mengirimkan data bukus, stats analitik, dan kategoris ke tampilan
        return view('buku.index', compact('bukus', 'stats', 'kategoris'));
    }

    /**
     * 2. CREATE: Menampilkan formulir penambahan buku baru
     */
    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        return view('buku.create', compact('kategoris'));
    }

    /**
     * 3. STORE: Menyimpan buku baru ke database dengan validasi
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn'         => 'required|string|max:20|unique:bukus,isbn',
            'judul'        => 'required|string|min:5|max:200',
            'penulis'      => 'required|string|max:100',
            'penerbit'     => 'required|string|max:100',
            'tahun_terbit' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 1),
            'kategori_id'  => 'required|exists:kategoris,id',
            'stok'         => 'required|integer|min:0',
            'sinopsis'     => 'nullable|string',
        ]);

        Buku::create($validated);

        return redirect()->route('buku.index')->with('success', 'Buku baru berhasil ditambahkan!');
    }

    /**
     * 4. SHOW: Menampilkan informasi detail buku
     */
    public function show(Buku $buku)
    {
        $buku->load('kategori');
        return view('buku.show', compact('buku'));
    }

    /**
     * 5. EDIT: Menampilkan formulir ubah data buku
     */
    public function edit(Buku $buku)
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        return view('buku.edit', compact('buku', 'kategoris'));
    }

    /**
     * 6. UPDATE: Memperbarui data buku
     */
    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'isbn'         => 'required|string|max:20|unique:bukus,isbn,' . $buku->id,
            'judul'        => 'required|string|min:5|max:200',
            'penulis'      => 'required|string|max:100',
            'penerbit'     => 'required|string|max:100',
            'tahun_terbit' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 1),
            'kategori_id'  => 'required|exists:kategoris,id',
            'stok'         => 'required|integer|min:0',
            'sinopsis'     => 'nullable|string',
        ]);

        $buku->update($validated);

        return redirect()->route('buku.index')->with('success', 'Data buku berhasil diperbarui!');
    }

    /**
     * 7. DESTROY: Menghapus data buku
     */
    public function destroy(Buku $buku)
    {
        $buku->delete();
        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus dari sistem!');
    }
}