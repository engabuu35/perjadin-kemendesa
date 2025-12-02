<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    /**
     * Menampilkan halaman riwayat perjalanan dinas.
     * - Untuk PIMPINAN: tab Pribadi & Pegawai (view pimpinan.riwayatAllUsers)
     * - Untuk role lain: riwayat pribadi seperti sebelumnya (view pages.riwayat)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil role berdasarkan NIP
        $roleKode = DB::table('penugasanperan')
            ->join('roles', 'penugasanperan.role_id', '=', 'roles.id')
            ->where('penugasanperan.user_id', $user->nip)
            ->value('roles.kode');

        $roleKode = $roleKode ? strtoupper($roleKode) : null;

        $search = $request->input('search');

        // ID status "Selesai"
        $idSelesai = DB::table('statusperjadin')
            ->where('nama_status', 'Selesai')
            ->value('id');

        // ============================================================
        //  CASE 1: PIMPINAN  →  gunakan view pimpinan.riwayatAllUsers
        // ============================================================
        if ($roleKode === 'PIMPINAN') {

            // Base query: semua perjadin yang sudah selesai, 1 tahun terakhir
            $baseQuery = function () use ($idSelesai) {
                return DB::table('perjalanandinas')
                    ->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
                    ->where('perjalanandinas.id_status', $idSelesai)
                    ->where('perjalanandinas.created_at', '>=', Carbon::now()->subYear())
                    ->select(
                        'perjalanandinas.*',
                        'statusperjadin.nama_status'
                    );
            };

            // --- PRIBADI: perjadin yang diikuti pimpinan ---
            $qPribadi = $baseQuery()
                ->join('pegawaiperjadin', 'perjalanandinas.id', '=', 'pegawaiperjadin.id_perjadin')
                ->where('pegawaiperjadin.id_user', $user->nip);

            // --- PEGAWAI: semua perjadin selesai (tanpa filter pegawai) ---
            $qPegawai = $baseQuery();

            // Filter search untuk kedua query
            if ($search) {
                $applySearch = function ($q) use ($search) {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('perjalanandinas.nomor_surat', 'LIKE', "%{$search}%")
                           ->orWhere('perjalanandinas.tujuan', 'LIKE', "%{$search}%");
                    });
                };
                $applySearch($qPribadi);
                $applySearch($qPegawai);
            }


            $formatter = function ($item) {
            $mulai   = Carbon::parse($item->tgl_mulai);
            $selesai = Carbon::parse($item->tgl_selesai);

            $item->lokasi  = $item->tujuan;
            $item->tanggal = $mulai->translatedFormat('d F Y') . ' - ' . $selesai->translatedFormat('d F Y');
            $item->status  = $item->nama_status ?? 'Selesai';

            return $item;
        };

            $riwayatPribadi = $qPribadi
                ->orderBy('perjalanandinas.tgl_mulai', 'desc')
                ->paginate(10, ['*'], 'pribadi_page')
                ->through($formatter);

            $riwayatPegawai = $qPegawai
                ->orderBy('perjalanandinas.tgl_mulai', 'desc')
                ->paginate(10, ['*'], 'pegawai_page')
                ->through($formatter);

            // Tambahkan field bantu (lokasi, tanggal, status) untuk dipakai di view
            $formatter = function ($item) {
                $mulai   = Carbon::parse($item->tgl_mulai);
                $selesai = Carbon::parse($item->tgl_selesai);

                $item->lokasi  = $item->tujuan;
                $item->tanggal = $mulai->translatedFormat('d F Y') . ' - ' . $selesai->translatedFormat('d F Y');
                $item->status  = $item->nama_status ?? 'Selesai';

                return $item;
            };

            $riwayatPribadi = $riwayatPribadi->through($formatter);
            $riwayatPegawai = $riwayatPegawai->through($formatter);

            return view('pimpinan.riwayatAllUsers', [
                'riwayatPribadi' => $riwayatPribadi,
                'riwayatPegawai' => $riwayatPegawai,
                'search'         => $search,
            ]);
        }

        // ============================================================
        //  CASE 2: NON-PIMPINAN  →  logika lama (Pribadi saja)
        // ============================================================

        $query = DB::table('perjalanandinas')
            ->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
            ->where('perjalanandinas.id_status', $idSelesai) // hanya selesai
            ->where('perjalanandinas.created_at', '>=', Carbon::now()->subYear())
            ->select(
                'perjalanandinas.*',
                'statusperjadin.nama_status'
            );

        // Jika dia PEGAWAI biasa → filter hanya perjadin yang diikutinya
        if ($roleKode === 'PEGAWAI') {
            $query->join('pegawaiperjadin', 'perjalanandinas.id', '=', 'pegawaiperjadin.id_perjadin')
                  ->where('pegawaiperjadin.id_user', $user->nip);
        }

        // Filter pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('perjalanandinas.nomor_surat', 'LIKE', "%{$search}%")
                    ->orWhere('perjalanandinas.tujuan', 'LIKE', "%{$search}%");
            });
        }

        $riwayat_list = $query
            ->orderBy('perjalanandinas.tgl_mulai', 'desc')
            ->paginate(10, ['*'], 'riwayat_page');

        // Tambahkan property status agar view lama tetap jalan
        $riwayat_list = $riwayat_list->through(function ($item) {
            $item->status        = 'Selesai';
            $item->nama_status   = 'Selesai';
            $item->custom_status = 'Selesai';
            $item->status_color  = 'green';
            return $item;
        });

        return view('pages.riwayat', compact('riwayat_list'));
    }

    /**
     * Detail perjalanan dinas (untuk route lama /riwayat/{id} kalau dipakai).
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
