<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    /**
     * Menampilkan halaman riwayat perjalanan dinas.
     * Jika user adalah PIMPINAN -> diarahkan ke halaman ALL USERS.
     */
    public function index(Request $request)
    {
        // Ambil data user login
        $user = Auth::user();

        // Ambil role berdasarkan NIP
        $role = DB::table('penugasanperan')
            ->join('roles', 'penugasanperan.role_id', '=', 'roles.id')
            ->where('penugasanperan.user_id', $user->nip) 
            ->value('roles.kode'); 

        // Jika role PIMPINAN -> tampilkan halaman seluruh user
        if ($role !== null && strtoupper($role) === 'PIMPINAN') {
            return $this->allUsers($request);
        }

        // ============================================================
        //          RIWAYAT UNTUK ROLE BIASA (PIC, PPK, PEGAWAI)
        // ============================================================

        $search = $request->input('search');

        // Cari ID Status 'Selesai' (Pastikan ID ini benar ada di DB)
        $idSelesai = DB::table('statusperjadin')->where('nama_status', 'Selesai')->value('id');

        $query = DB::table('perjalanandinas')
            // PERBAIKAN 1: Lakukan Join ke tabel status agar nama status terbaca
            ->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
            ->where('perjalanandinas.id_status', $idSelesai) // Hanya ambil yang Selesai
            ->where('perjalanandinas.created_at', '>=', Carbon::now()->subYear()) // 1 tahun terakhir
            ->select(
                'perjalanandinas.*',
                'statusperjadin.nama_status' // Ambil kolom nama status
            );

        // Filter berdasarkan User jika dia Pegawai Biasa
        if (strtoupper($role) == 'PEGAWAI') {
            $query->join('pegawaiperjadin', 'perjalanandinas.id', '=', 'pegawaiperjadin.id_perjadin')
                  ->where('pegawaiperjadin.id_user', $user->nip);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('perjalanandinas.nomor_surat', 'LIKE', "%{$search}%")
                  ->orWhere('perjalanandinas.tujuan', 'LIKE', "%{$search}%");
            });
        }

        $riwayat_list = $query->orderBy('perjalanandinas.tgl_mulai', 'desc')->get();

        // PERBAIKAN 2: Pastikan properti status tersedia untuk View
        // Kita inject manual agar komponen x-riwayat-card merender warna hijau/selesai
        $riwayat_list->transform(function($item) {
            $item->status = 'Selesai';        // Untuk view yang pakai $item->status
            $item->nama_status = 'Selesai';   // Untuk view yang pakai $item->nama_status
            $item->custom_status = 'Selesai'; // Cadangan
            $item->status_color = 'green';    // Cadangan jika view support warna dinamis
            return $item;
        });

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