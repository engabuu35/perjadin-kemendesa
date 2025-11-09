<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // =====================================================
        // HIERARKI ROLE
        // =====================================================
        // PIC bisa mengakses semua role (superuser)
        if ($user->hasRole('PIC')) {
            return $next($request);
        }

        // Jika route tidak punya batasan role, lanjutkan
        if (empty($roles)) {
            return $next($request);
        }

        // Hierarki tambahan:
        // - PIMPINAN dan PPK juga bisa akses halaman yang butuh PEGAWAI
        // - PEGAWAI hanya akses halaman untuk PEGAWAI
        $expandedRoles = [];

        foreach ($roles as $role) {
            $expandedRoles[] = $role;

            if ($role === 'PEGAWAI') {
                // Semua yang punya PEGAWAI atau level lebih tinggi tetap bisa akses
                $expandedRoles = array_merge($expandedRoles, ['PIMPINAN', 'PPK']);
            }

            if (in_array($role, ['PIMPINAN', 'PPK'])) {
                // Tambahkan hak akses untuk role PEGAWAI (turunan)
                $expandedRoles[] = 'PEGAWAI';
            }
        }

        // Hilangkan duplikat role agar query tidak berlebihan
        $expandedRoles = array_unique($expandedRoles);

        // Jika user punya salah satu role yang diperluas, lanjut
        if ($user->hasAnyRole($expandedRoles)) {
            return $next($request);
        }

        // Jika tidak punya hak akses
        abort(403, 'Akses ditolak.');
    }
}
