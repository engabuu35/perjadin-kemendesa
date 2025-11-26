<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    /**
     * Menampilkan halaman riwayat perjalanan dinas.
     * Jika user adalah PIMPINAN -> diarahkan ke halaman ALL USERS.
     */
    public function index(Request $request)
    {
        // Ambil data user login
        $user = auth()->user();

        // Ambil role berdasarkan NIP (karena users PK = nip)
        $role = DB::table('penugasanperan')
            ->join('roles', 'penugasanperan.role_id', '=', 'roles.id')
            ->where('penugasanperan.user_id', $user->nip) // USER_ID = NIP
            ->value('roles.kode'); // hasil: PIMPINAN, PIC, PPK, PEGAWAI

        // Jika role PIMPINAN -> tampilkan halaman seluruh user
        if ($role !== null && strtoupper($role) === 'PIMPINAN') {
            return $this->allUsers($request);
        }

        // ============================================================
        //          RIWAYAT UNTUK ROLE BIASA (PIC, PPK, PEGAWAI)
        // ============================================================

        $search = $request->input('search');

        $query = DB::table('perjalanandinas')
            ->where('id_status', 4) // selesai
            ->where('created_at', '>=', Carbon::now()->subYear()); // 1 tahun terakhir

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'LIKE', "%{$search}%")
                  ->orWhere('tujuan', 'LIKE', "%{$search}%");
            });
        }

        $riwayat_list = $query->orderBy('created_at', 'desc')->get();

        return view('pages.riwayat', compact('riwayat_list'));
    }

    /**
     * Halaman riwayat ALL USERS khusus PIMPINAN.
     */
    private function allUsers(Request $request)
    {
        $users = DB::table('users')
            ->select('nip', 'nama', 'email', 'no_telp', 'created_at')
            ->orderBy('nama', 'asc')
            ->get();

        return view('pimpinan.riwayatAllUsers', compact('users'));
    }

    /**
     * Menampilkan detail perjalanan dinas.
     */
    public function show($id)
    {
        $perjalanan = DB::table('perjalanandinas')
            ->where('id', $id)
            ->first();

        if (!$perjalanan) {
            return redirect()->route('riwayat')
                ->with('error', 'Data perjalanan dinas tidak ditemukan.');
        }

        return view('pages.detail', compact('perjalanan'));
    }
}
