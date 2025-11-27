<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman edit profil
     */
    public function edit()
    {
        // Ambil user yang sedang login
        $user = Auth::user();
        
        // Return view dengan data user
        // Pastikan nama view sesuai dengan lokasi file blade kamu
        return view('pages.lamanprofile', compact('user'));
    }

    /**
     * Proses update data profil
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            // Validasi password (opsional)
            'password' => 'nullable|min:6|confirmed', 
            // Validasi foto (Security Layer): Max 2MB (2048 KB)
            // Meskipun di frontend sudah dicek JS, di backend WAJIB tetap ada.
            'foto' => 'nullable|image|max:2048', 
        ]);

        /** @var \App\Models\User $user */
        
        // Update data dasar
        $user->nama = $request->nama;
        $user->no_telp = $request->no_telp;

        // Cek jika user input password baru
        if ($request->filled('password')) {
            $user->password_hash = Hash::make($request->password);
        }

        // --- LOGIKA UPLOAD FOTO (Disiapkan) ---
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            // Simpan foto baru
            $path = $request->file('foto')->store('profile_photos', 'public');
            $user->foto_profil = $path;
        }

        // Simpan ke database
        // Karena model User kamu menggunakan primary key 'nip' (string), pastikan save() berjalan aman.
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}