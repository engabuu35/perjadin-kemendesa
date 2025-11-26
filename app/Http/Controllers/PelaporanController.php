<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerjalananDinas;
use Illuminate\Support\Facades\DB;

class PelaporanController extends Controller
{
    /**
     * BERANDA PELAPORAN
     * Menampilkan daftar perjalanan dinas yang statusnya "Menunggu Verifikasi" atau "Selesai".
     */
    public function index(Request $request)
    {
        $query = PerjalananDinas::query();

        // Join ke tabel status biar bisa filter
        $query->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
              ->select('perjalanandinas.*', 'statusperjadin.nama_status');

        // Filter: Hanya tampilkan yang statusnya SUDAH diajukan (Menunggu Verifikasi atau Selesai)
        // Kita exclude 'Draft' dan 'Sedang Berlangsung'
        $query->whereIn('statusperjadin.nama_status', ['Menunggu Verifikasi Laporan', 'Selesai', 'Ditolak']);

        // Search
        if ($request->has('q')) {
            $q = $request->q;
            $query->where('nomor_surat', 'like', "%$q%")
                  ->orWhere('tujuan', 'like', "%$q%");
        }

        $laporanList = $query->orderBy('updated_at', 'desc')->paginate(10);

        return view('pic.pelaporan.index', compact('laporanList'));
    }

    /**
     * DETAIL TABEL KEUANGAN (LS RAMPUNG VIEW)
     */
    public function show($id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        
        // Ambil status text
        $statusText = DB::table('statusperjadin')->where('id', $perjalanan->id_status)->value('nama_status');

        // Ambil semua peserta + Data Keuangannya
        $pesertaList = DB::table('pegawaiperjadin')
            ->join('users', 'users.nip', '=', 'pegawaiperjadin.id_user')
            ->where('pegawaiperjadin.id_perjadin', $id)
            ->select('users.nip', 'users.nama', 'pegawaiperjadin.role_perjadin')
            ->orderBy('pegawaiperjadin.is_lead', 'desc')
            ->get();

        // Proses Data untuk Tabel
        // Kita loop peserta, lalu cari bukti laporannya, lalu kita kelompokkan by kategori
        $rekapData = [];
        
        foreach($pesertaList as $p) {
            // Ambil bukti dari tabel bukti_laporan via laporan_perjadin
            $buktis = DB::table('laporan_perjadin')
                ->join('bukti_laporan', 'laporan_perjadin.id', '=', 'bukti_laporan.id_laporan')
                ->where('laporan_perjadin.id_perjadin', $id)
                ->where('laporan_perjadin.id_user', $p->nip)
                ->select('bukti_laporan.kategori', 'bukti_laporan.nominal')
                ->get();

            // Inisialisasi biaya 0
            $biaya = [
                'Tiket' => 0, 'Uang Harian' => 0, 'Penginapan' => 0, 
                'Uang Representasi' => 0, 'Transport' => 0, 'Sewa Kendaraan' => 0, 
                'Pengeluaran Riil' => 0, 'SSPB' => 0, 'Total' => 0
            ];

            // Sum nominal berdasarkan kategori
            foreach($buktis as $b) {
                if(isset($biaya[$b->kategori])) {
                    $biaya[$b->kategori] += $b->nominal;
                }
                // Hitung Total (SSPB biasanya pengurang, tapi disini kita anggap positif dulu atau sesuaikan logika)
                if($b->kategori != 'SSPB') {
                    $biaya['Total'] += $b->nominal;
                }
            }

            $rekapData[] = [
                'nip' => $p->nip,
                'nama' => $p->nama,
                'biaya' => $biaya
            ];
        }

        return view('pic.pelaporan.detail', compact('perjalanan', 'rekapData', 'statusText'));
    }
}