<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerjalananDinas;
use App\Models\LaporanKeuangan;
use App\Models\BuktiLaporan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PPKController extends Controller
{
    public function index(Request $request)
    {
        $query = PerjalananDinas::query();
        $query->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
              ->select('perjalanandinas.*', 'statusperjadin.nama_status');

        $query->whereIn('statusperjadin.nama_status', ['Menunggu Validasi PPK', 'Selesai']);

        if ($request->has('q')) {
            $q = $request->q;
            $query->where('nomor_surat', 'like', "%$q%")->orWhere('tujuan', 'like', "%$q%");
        }

        $listVerifikasi = $query->orderBy('updated_at', 'desc')->paginate(10);
        return view('ppk.verifikasi.index', compact('listVerifikasi'));
    }

    public function show($id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        $statusText = DB::table('statusperjadin')->where('id', $perjalanan->id_status)->value('nama_status');
        $isSelesai = ($statusText == 'Selesai');
        $laporanKeuangan = LaporanKeuangan::where('id_perjadin', $id)->first();

        // Ambil Peserta
        $pesertaList = DB::table('pegawaiperjadin')
            ->join('users', 'users.nip', '=', 'pegawaiperjadin.id_user')
            ->where('pegawaiperjadin.id_perjadin', $id)
            ->select('users.nip', 'users.nama', 'pegawaiperjadin.role_perjadin')
            ->orderBy('pegawaiperjadin.is_lead', 'desc')
            ->get();

        $rekapData = [];
        $totalSeluruhnya = 0;

        foreach($pesertaList as $p) {
            // Ambil semua bukti laporan pegawai ini
            // Kita select 'keterangan' juga sekarang
            $buktis = DB::table('laporan_perjadin')
                ->join('bukti_laporan', 'laporan_perjadin.id', '=', 'bukti_laporan.id_laporan')
                ->where('laporan_perjadin.id_perjadin', $id)
                ->where('laporan_perjadin.id_user', $p->nip)
                ->select('bukti_laporan.kategori', 'bukti_laporan.nominal', 'bukti_laporan.keterangan')
                ->get();

            // Init Biaya
            $biaya = [
                'Tiket' => 0, 'Uang Harian' => 0, 'Penginapan' => 0, 
                'Uang Representasi' => 0, 'Transport' => 0, 'Sewa Kendaraan' => 0, 
                'Pengeluaran Riil' => 0, 'SSPB' => 0, 'Total' => 0
            ];

            // Init Info Teks
            $info = [
                'Nama Penginapan' => '-', 
                'Kota' => '-', 
                'Kode Tiket' => '-', 
                'Maskapai' => '-'
            ];

            foreach($buktis as $b) {
                // 1. Jika ada nominal, masukkan ke array biaya
                if ($b->nominal > 0) {
                    if(isset($biaya[$b->kategori])) {
                        $biaya[$b->kategori] += $b->nominal;
                    }
                    if($b->kategori != 'SSPB') {
                        $biaya['Total'] += $b->nominal;
                    }
                } 
                // 2. Jika nominal 0, cek apakah ini data info teks?
                // Sesuai Seeder/Inputan PIC: kategorinya adalah 'Maskapai', 'Kota', dll.
                else {
                    if (array_key_exists($b->kategori, $info)) {
                        $info[$b->kategori] = $b->keterangan;
                    }
                }
            }

            $totalSeluruhnya += $biaya['Total'];

            $rekapData[] = [
                'nip' => $p->nip,
                'nama' => $p->nama,
                'biaya' => $biaya,
                'info' => $info // Masukkan info teks ke array data
            ];
        }

        return view('ppk.verifikasi.detail', compact('perjalanan', 'rekapData', 'statusText', 'isSelesai', 'totalSeluruhnya', 'laporanKeuangan'));
    }

    public function storeVerifikasi(Request $request, $id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);

        $request->validate([
            'nomor_spm' => 'required|string|max:100',
            'nomor_sp2d' => 'required|string|max:100',
            'tanggal_spm' => 'required|date',
            'tanggal_sp2d' => 'required|date',
            'total_biaya_rampung' => 'required|numeric'
        ]);

        LaporanKeuangan::updateOrCreate(
            ['id_perjadin' => $id],
            [
                'id_status' => 1, 
                'verified_by' => Auth::user()->nip,
                'verified_at' => now(),
                'nomor_spm' => $request->nomor_spm,
                'tanggal_spm' => $request->tanggal_spm,
                'nomor_sp2d' => $request->nomor_sp2d,
                'tanggal_sp2d' => $request->tanggal_sp2d,
                'biaya_rampung' => $request->total_biaya_rampung
            ]
        );

        $idSelesai = DB::table('statusperjadin')->where('nama_status', 'Selesai')->value('id');
        $perjalanan->update(['id_status' => $idSelesai]);

        return redirect()->route('ppk.verifikasi.index')->with('success', 'Verifikasi Berhasil! Data SP2D telah disimpan.');
    }
}