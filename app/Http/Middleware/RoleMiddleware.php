<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // 1. Cek apakah user login
        if (! $user) {
            return redirect()->route('login');
        }

        // 2. Ambil semua role yang dimiliki user dari database
        // Kita ambil array kode-nya saja, misal: ['PIMPINAN', 'PEGAWAI']
        $userRoles = DB::table('penugasanperan')
            ->join('roles', 'penugasanperan.role_id', '=', 'roles.id')
            ->where('penugasanperan.user_id', $user->nip) // Menggunakan NIP sesuai tabel users Anda
            ->pluck('roles.kode')
            ->toArray();

        // =====================================================
        // LOGIKA HIERARKI & SUPERUSER
        // =====================================================
        
        // A. PIC adalah Superuser (Bisa akses segalanya)
        if (in_array('PIC', $userRoles)) {
            return $next($request);
        }

        // B. Jika route tidak membatasi role khusus, izinkan lewat
        if (empty($roles)) {
            return $next($request);
        }

        // C. Logika Hierarki Akses
        // Kita tentukan role apa saja yang DIPERBOLEHKAN mengakses route ini
        $allowedRoles = $roles; // Role asli yang diminta route (misal: 'PEGAWAI')

        foreach ($roles as $role) {
            // Jika route butuh 'PEGAWAI', maka PIMPINAN dan PPK juga boleh masuk
            // (Karena atasan boleh akses menu bawahan/umum)
            if ($role === 'PEGAWAI') {
                $allowedRoles[] = 'PIMPINAN';
                $allowedRoles[] = 'PPK';
            }
            
            // CATATAN: Bagian logika "in_array($role, ['PIMPINAN', 'PPK'])" yang lama
            // saya hapus karena itu justru membuat PEGAWAI bisa mengakses halaman PIMPINAN.
            // Kita hanya ingin HIERARKI TURUN (Atasan bisa akses menu bawahan), bukan sebaliknya.
        }

        // D. Cek Interseksi (Irisan)
        // Apakah ada irisan antara "Role yang dimiliki User" dengan "Role yang diperbolehkan"?
        // array_intersect akan menghasilkan array kosong jika tidak ada yang cocok
        $hasAccess = !empty(array_intersect($userRoles, $allowedRoles));

        if ($hasAccess) {
            return $next($request);
        }

        // Jika tidak punya hak akses
        abort(403, 'ANDA TIDAK MEMILIKI HAK AKSES KE HALAMAN INI.');
    }
}