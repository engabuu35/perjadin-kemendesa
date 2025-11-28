<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    /**
     * Halaman utama riwayat perjalanan dinas.
     * - Semua role melihat tab "Pribadi"
     * - Khusus PIMPINAN, tab "Pegawai" berisi semua perjadin selesai.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil role user (kode: PIMPINAN, PIC, PPK, PEGAWAI, dst.)
        $roleKode = DB::table('penugasanperan')
            ->join('roles', 'penugasanperan.role_id', '=', 'roles.id')
            ->where('penugasanperan.user_id', $user->nip)
            ->value('roles.kode');

        $roleKode = strtoupper((string) $roleKode);

        // Tab aktif: 'pribadi' (default) atau 'pegawai'
        $tab    = $request->input('tab', 'pribadi');
        $search = $request->input('search');

        // ID status "Selesai"
        $idSelesai = DB::table('statusperjadin')
            ->where('nama_status', 'Selesai')
            ->value('id');

        // ============================
        // TAB PRIBADI (default semua role)
        // ============================
        if ($tab === 'pribadi') {

            $query = DB::table('perjalanandinas')
                ->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
                ->where('perjalanandinas.id_status', $idSelesai)
                ->where('perjalanandinas.created_at', '>=', Carbon::now()->subYear())
                ->select('perjalanandinas.*', 'statusperjadin.nama_status');

            // Jika role PEGAWAI → filter per pegawai
            if ($roleKode === 'PEGAWAI') {
                $query->join('pegawaiperjadin', 'perjalanandinas.id', '=', 'pegawaiperjadin.id_perjadin')
                      ->where('pegawaiperjadin.id_user', $user->nip);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('perjalanandinas.nomor_surat', 'LIKE', "%{$search}%")
                      ->orWhere('perjalanandinas.tujuan', 'LIKE', "%{$search}%");
                });
            }

            $riwayat_list = $query
                ->orderBy('perjalanandinas.tgl_mulai', 'desc')
                ->get();
        }

        // =====================================
        // TAB PEGAWAI (KHUSUS PIMPINAN)
        //  → riwayat semua perjadin yang selesai
        // =====================================
        elseif ($tab === 'pegawai' && $roleKode === 'PIMPINAN') {

            $query = DB::table('perjalanandinas')
                ->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
                ->where('perjalanandinas.id_status', $idSelesai)
                ->where('perjalanandinas.created_at', '>=', Carbon::now()->subYear())
                ->select('perjalanandinas.*', 'statusperjadin.nama_status');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('perjalanandinas.nomor_surat', 'LIKE', "%{$search}%")
                      ->orWhere('perjalanandinas.tujuan', 'LIKE', "%{$search}%");
                });
            }

            $riwayat_list = $query
                ->orderBy('perjalanandinas.tgl_mulai', 'desc')
                ->get();
        }

        // Jika tab=pegawai tapi bukan pimpinan → fallback ke pribadi
        else {
            return redirect()->route('riwayat', ['tab' => 'pribadi']);
        }

        // Normalisasi properti status untuk kebutuhan view
        $riwayat_list->transform(function ($item) {
            $item->status        = 'Selesai';
            $item->nama_status   = $item->nama_status ?? 'Selesai';
            $item->status_color  = 'green';
            return $item;
        });

        return view('pages.riwayat', [
            'riwayat_list' => $riwayat_list,
            'tab'          => $tab,
            'role'         => $roleKode,
            'search'       => $search,
        ]);
    }

    /**
     * Detail perjalanan dinas (dipakai dari versi lama, jika masih digunakan).
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
