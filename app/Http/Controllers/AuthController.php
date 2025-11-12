<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
            'password' => 'required|string',
        ]);

        $nip = $request->nip;
        $password = $request->password;

        $user = User::where('nip', $nip)->first();

        if (! $user) {
            return back()->withErrors(['nip' => 'NIP/NIK tidak ditemukan.'])->onlyInput('nip');
        }

        if (! $user->is_aktif) {
            return back()->withErrors(['nip' => 'Akun tidak aktif.'])->onlyInput('nip');
        }

        // Cek password manual dulu
        if (! Hash::check($password, $user->getAuthPassword())) {
            return back()->withErrors(['nip' => 'NIP/NIK atau password salah.'])->onlyInput('nip');
        }

        // Login user
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        $user = $request->user();  

        // pilih prioritas redirect berdasarkan kode role: NANTI DIUBAH!
        if ($user->hasRole('PIC')) {
            return redirect()->intended('/pic/beranda');
        }

        if ($user->hasRole('PIMPINAN')) {
            return redirect()->intended('/pimpinan/beranda');
        }

        if ($user->hasRole('PPK')) {
            return redirect()->intended('/ppk/beranda');
        }

        return redirect()->intended('/beranda'); // default pegawai

        }

    //logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    //confirm password
    public function showConfirmForm()
    {
        return view('auth.confirm-password');
    }

    public function confirm(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $user = $request->user();

        if (! Hash::check($request->password, $user->getAuthPassword())) {
            return back()->withErrors(['password' => 'Password tidak cocok.']);
        }

        // tandai password sudah dikonfirmasi (timestamp)
        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended('/');
    }
}
