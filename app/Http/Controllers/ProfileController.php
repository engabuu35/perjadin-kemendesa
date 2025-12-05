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
        $user = Auth::user();
        return view('pages.lamanprofile', compact('user'));
    }

    /**
     * Proses update data profil
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'password' => 'nullable|min:6|confirmed', 
            'foto' => 'nullable|image|max:2048', 
        ]);

        /** @var \App\Models\User $user */
        
        $user->nama = $request->nama;
        $user->no_telp = $request->no_telp;

        $passwordChanged = false;
        if ($request->filled('password')) {
            $user->password_hash = Hash::make($request->password);
            $passwordChanged = true;
        }

        if ($request->hasFile('foto')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $path = $request->file('foto')->store('profile_photos', 'public');
            $user->foto_profil = $path;
        }

        $user->save();

        if ($passwordChanged) {
            app(NotificationController::class)->sendFromTemplate(
                'perubahan_password',
                [$user->nip],
                [
                    'waktu' => now()->format('d M Y H:i'),
                    'ip' => $request->ip()
                ]
            );
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
