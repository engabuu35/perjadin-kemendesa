<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
            'password' => 'required|string',
        ]);

        $nip = $request->input('nip');
        $password = $request->input('password');

        // Cari user berdasarkan NIP/NIK
        $user = User::where('nip', $nip)->first();

        if (! $user) {
            return back()
                ->withErrors(['nip' => 'NIP/NIK tidak ditemukan.'])
                ->onlyInput('nip');
        }

        if (! $user->is_aktif) {
            return back()
                ->withErrors(['nip' => 'Akun tidak aktif.'])
                ->onlyInput('nip');
        }

        // Cek password
        if (! Hash::check($password, $user->getAuthPassword())) {
            return back()
                ->withErrors(['nip' => 'NIP/NIK atau password salah.'])
                ->onlyInput('nip');
        }

        // Login user dan regenerate session
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        // Redirect ke single beranda (jika ada intended url, pakai intended)
        return redirect()->intended(route('pages.beranda'));
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Tampilkan form konfirmasi password.
     */
    public function showConfirmForm()
    {
        return view('auth.confirm-password');
    }

    /**
     * Proses konfirmasi password.
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (! Hash::check($request->password, $user->getAuthPassword())) {
            return back()->withErrors(['password' => 'Password tidak cocok.']);
        }

        // Tandai password telah dikonfirmasi (timestamp)
        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('pages.beranda'));
    }
}
