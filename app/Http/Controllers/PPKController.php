<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerjalananDinas;
use App\Models\LaporanKeuangan;
use App\Models\BuktiLaporan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\RekapPerjadinExport;
use Maatwebsite\Excel\Facades\Excel;

class PPKController extends Controller
{
    public function index(Request $request)
    {
        $query = PerjalananDinas::query();
        $query->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
              ->select('perjalanandinas.*', 'statusperjadin.nama_status');

        // Filter Status (Sesuaikan dengan Database Anda)
        $query->whereIn('statusperjadin.nama_status', [
            'Menunggu Validasi PPK', 
            'Menunggu Verifikasi' 
        ]);

        if ($request->has('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q){
                $sub->where('nomor_surat', 'like', "%$q%")
                    ->orWhere('tujuan', 'like', "%$q%");
            });
        }

        $listVerifikasi = $query->orderBy('updated_at', 'desc')->paginate(10);
        
        foreach($listVerifikasi as $item) {
            $item->custom_status = 'Butuh Validasi';
            $item->status_color = 'blue';
        }

        return view('ppk.verifikasi.index', compact('listVerifikasi'));
    }

    public function show($id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        $statusText = DB::table('statusperjadin')->where('id', $perjalanan->id_status)->value('nama_status');
        $isSelesai = ($statusText === 'Selesai');
        $laporanKeuangan = LaporanKeuangan::where('id_perjadin', $id)->first();

        $pesertaList = DB::table('pegawaiperjadin')
            ->join('users', 'users.nip', '=', 'pegawaiperjadin.id_user')
            ->where('pegawaiperjadin.id_perjadin', $id)
            ->select('users.nip', 'users.nama', 'pegawaiperjadin.role_perjadin')
            ->orderBy('pegawaiperjadin.is_lead', 'desc')
            ->get();

        $rekapData = [];
        $totalSeluruhnya = 0;

        foreach($pesertaList as $p) {
            $buktis = DB::table('laporan_perjadin')
                ->join('bukti_laporan', 'laporan_perjadin.id', '=', 'bukti_laporan.id_laporan')
                ->where('laporan_perjadin.id_perjadin', $id)
                ->where('laporan_perjadin.id_user', $p->nip)
                ->select('bukti_laporan.kategori', 'bukti_laporan.nominal', 'bukti_laporan.keterangan')
                ->get();

            $biaya = ['Tiket'=>0,'Uang Harian'=>0,'Penginapan'=>0,'Uang Representasi'=>0,'Transport'=>0,'Sewa Kendaraan'=>0,'Pengeluaran Riil'=>0,'SSPB'=>0,'Total'=>0];
            $info = ['Nama Penginapan'=>'-','Kota'=>'-','Kode Tiket'=>'-','Maskapai'=>'-'];

            foreach($buktis as $b) {
                if ($b->nominal > 0) {
                    if(isset($biaya[$b->kategori])) $biaya[$b->kategori] += $b->nominal;
                    if($b->kategori != 'SSPB') $biaya['Total'] += $b->nominal;
                } else {
                    if (array_key_exists($b->kategori, $info)) $info[$b->kategori] = $b->keterangan;
                }
            }
            $totalSeluruhnya += $biaya['Total'];
            $rekapData[] = ['nip' => $p->nip, 'nama' => $p->nama, 'biaya' => $biaya, 'info' => $info];
        }

        return view('ppk.verifikasi.detail', compact('perjalanan', 'rekapData', 'statusText', 'isSelesai', 'totalSeluruhnya', 'laporanKeuangan'));    
    }

    public function approve(Request $request, $id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);

        // Status Laporan (Keuangan) -> 'Selesai'
        // Gunakan fallback '5' jika ID tidak ditemukan
        $idLapSelesai = DB::table('statuslaporan')->where('nama_status', 'Selesai')->value('id') ?? 5;
        
        LaporanKeuangan::updateOrCreate(['id_perjadin' => $id], [
                'id_status' => $idLapSelesai,
                'verified_by' => Auth::user()->nip,
                'verified_at' => now(),
                'nomor_spm' => $request->nomor_spm,
                'tanggal_spm' => $request->tanggal_spm,
                'nomor_sp2d' => $request->nomor_sp2d,
                'tanggal_sp2d' => $request->tanggal_sp2d,
                'biaya_rampung' => $request->total_biaya_rampung
        ]);

        // Status Perjadin (Surat) -> 'Selesai'
        $idSelesai = DB::table('statusperjadin')->where('nama_status', 'Selesai')->value('id');
        $perjalanan->update(['id_status' => $idSelesai]);

        return redirect()->route('ppk.verifikasi.index')->with('success', 'Pembayaran disetujui. Data masuk Riwayat.');
    }

    public function reject(Request $request, $id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        
        // Status Perjadin -> 'Perlu Revisi'
        $idRevisi = DB::table('statusperjadin')->where('nama_status', 'Perlu Revisi')->value('id');
        if (!$idRevisi) {
            $idRevisi = DB::table('statusperjadin')->insertGetId(['nama_status' => 'Perlu Revisi']);
        }

        $perjalanan->update([
            'id_status' => $idRevisi,
            'catatan_penolakan' => $request->alasan_penolakan // Pastikan kolom ini ada di DB dan $fillable model
        ]);

        // Status Laporan Keuangan -> 'Perlu Revisi'
        $idLapRevisi = DB::table('statuslaporan')->where('nama_status', 'Perlu Revisi')->value('id');
        if (!$idLapRevisi) {
            $idLapRevisi = DB::table('statuslaporan')->insertGetId(['nama_status' => 'Perlu Revisi']);
        }
        
        LaporanKeuangan::updateOrCreate(['id_perjadin' => $id], ['id_status' => $idLapRevisi]);

        return redirect()->route('ppk.verifikasi.index')->with('warning', 'Laporan dikembalikan ke PIC untuk perbaikan.');
    }

    public function tabelRekap(Request $request) {
        // --- LOGIKA FILTERING RANGE BULAN ---
        
        $bulanMulai = $request->input('bulan_mulai');
        $bulanSelesai = $request->input('bulan_selesai');
        $tahun = $request->input('tahun');

        // Query Utama
        $query = DB::table('laporankeuangan')
            ->join('perjalanandinas', 'laporankeuangan.id_perjadin', '=', 'perjalanandinas.id')
            ->join('pegawaiperjadin', 'pegawaiperjadin.id_perjadin', '=', 'perjalanandinas.id')
            ->join('users', 'pegawaiperjadin.id_user', '=', 'users.nip')
            ->leftJoin('pangkatgolongan', 'users.pangkat_gol_id', '=', 'pangkatgolongan.id')
            ->leftJoin('unitkerja', 'users.id_uke', '=', 'unitkerja.id')
            ->leftJoin('statuslaporan', 'laporankeuangan.id_status', '=', 'statuslaporan.id')
            ->select('laporankeuangan.*', 'perjalanandinas.tujuan', 'perjalanandinas.tgl_mulai', 'perjalanandinas.tgl_selesai', 'users.nama as nama_pegawai', 'users.nip', 'pangkatgolongan.nama_pangkat', 'unitkerja.nama_uke', 'statuslaporan.nama_status as status_laporan')
            ->where('statuslaporan.nama_status', 'Selesai'); 

        // Filter Tahun (Wajib ada jika mau filter bulan)
        if ($tahun) {
            $query->whereYear('perjalanandinas.tgl_mulai', $tahun);
        }

        // Filter Range Bulan
        if ($bulanMulai && $bulanSelesai) {
            $query->whereMonth('perjalanandinas.tgl_mulai', '>=', $bulanMulai)
                  ->whereMonth('perjalanandinas.tgl_mulai', '<=', $bulanSelesai);
        }

        $rekap = $query->orderBy('perjalanandinas.tgl_mulai')->orderBy('users.nama')->get();
        $totalDibayarkan = $rekap->sum('biaya_rampung');

        return view('ppk.tabelRekap', compact(
            'rekap', 'totalDibayarkan', 
            'bulanMulai', 'bulanSelesai', 'tahun'
        ));
    }

    public function exportRekap(Request $request) {
        return Excel::download(new RekapPerjadinExport($request->tahun, $request->bulan_mulai, $request->bulan_selesai), 'rekap.xlsx');
    }
}