<?php

namespace App\Http\Controllers;

// Mengimpor kelas Request untuk memproses data dari formulir login
use Illuminate\Http\Request;
// Mengimpor Facade Auth untuk sistem autentikasi bawaan Laravel
use Illuminate\Support\Facades\Auth;
// Mengimpor Facade Cookie untuk manajemen cookie aman di browser
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    /**
     * 1. FORM LOGIN: Menampilkan halaman login pustakawan
     */
    public function loginForm()
    {
        // Membaca HTTP Cookie email terakhir jika sebelumnya pustakawan sudah pernah login
        $lastEmail = Cookie::get('del_last_user_email');

        // Merender tampilan form login di 'resources/views/auth/login.blade.php'
        return view('auth.login', compact('lastEmail'));
    }

    /**
     * 2. PROSES LOGIN: Autentikasi Kredensial, Regenerasi Sesi, dan Set Cookie
     * Sesuai Spesifikasi Teknis Wajib Poin 4 Modul Praktikum
     */
    public function login(Request $request)
    {
        // Validasi input email dan password
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Memeriksa kredensial pengguna ke database (tabel users)
        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            // [KRITIS MODUL] Wajib regenerasi Session ID untuk menangkal serangan Session Fixation
            $request->session()->regenerate();

            // Menyimpan state waktu login pustakawan ke dalam Session server
            session(['login_timestamp' => now()->format('d M Y, H:i:s') . ' WIB']);

            // [KRITIS MODUL] Menyimpan minimal 1 HTTP Cookie aman berisi email terakhir (berlaku 7 hari / 10080 menit)
            Cookie::queue('del_last_user_email', $request->email, 10080);

            // Mengarahkan ke halaman tujuan sebelumnya (intended) atau kembali ke katalog buku
            return redirect()->intended(route('buku.index'))->with('success', 'Selamat datang, Pustakawan! Sesi Anda telah aktif.');
        }

        // Jika kombinasi email atau password salah, kembali dengan pesan error
        return back()->withInput()->with('error', 'Kredensial login tidak valid. Silakan periksa email dan password Anda.');
    }

    /**
     * 3. LOGOUT: Mengakhiri Sesi, Membersihkan State Server, dan Regenerasi Token
     * Sesuai Spesifikasi Teknis Wajib Poin 4 Modul Praktikum
     */
    public function logout(Request $request)
    {
        // Menghapus status autentikasi pengguna
        Auth::logout();

        // [KRITIS MODUL] Menghancurkan seluruh data sesi yang tersimpan di server
        $request->session()->invalidate();

        // [KRITIS MODUL] Membuat ulang token CSRF baru demi keamanan request berikutnya
        $request->session()->regenerateToken();

        // Mengarahkan kembali ke halaman login dengan notifikasi sukses
        return redirect()->route('login')->with('success', 'Sesi server Anda telah dihapus dan ditutup dengan aman.');
    }
}