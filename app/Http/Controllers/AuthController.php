<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * PROSES LOGIN
     * - Validasi input email & password
     * - Auth::attempt() untuk mencocokkan kredensial
     * - Session regenerate untuk mencegah session fixation attack
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Coba login dengan Auth::attempt
        if (Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password
        ])) {
            // Regenerasi session untuk keamanan (session fixation protection)
            $request->session()->regenerate();

            // Redirect ke halaman dashboard
            return redirect()->route('dashboard');
        }

        // Jika gagal, kembali ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password salah.'
        ])->onlyInput('email');
    }

    /**
     * Menampilkan halaman register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * PROSES REGISTRASI
     * - Validasi input (name, email unik, password confirmed)
     * - Hash::make() untuk mengenkripsi password sebelum disimpan
     * - Redirect ke login dengan pesan sukses
     */
    public function register(Request $request)
    {
        // Validasi input ketat
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Buat user baru dengan password yang di-hash
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password) // Hashing password wajib!
        ]);

        // Redirect ke login dengan flash message sukses
        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil! Silakan login.');
    }

    /**
     * HALAMAN DASHBOARD
     * - Dilindungi: hanya user yang sudah login bisa akses
     * - Auth::check() memverifikasi status autentikasi
     */
    public function dashboard()
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('auth.dashboard');
    }

    /**
     * PROSES LOGOUT
     * - Auth::logout() menghapus autentikasi
     * - session invalidate & regenerateToken untuk keamanan
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Hapus semua data session
        $request->session()->invalidate();
        // Regenerasi CSRF token
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
