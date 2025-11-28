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

        // PERBAIKAN: Hapus 'Selesai' dari sini.
        // PPK hanya melihat yang statusnya "Menunggu Verifikasi".
        // Data yang 'Selesai' akan hilang dari dashboard ini dan masuk ke menu Riwayat/Rekap.
        $query->where('statusperjadin.nama_status', 'Menunggu Verifikasi');

        if ($request->has('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q){
                $sub->where('nomor_surat', 'like', "%$q%")
                    ->orWhere('tujuan', 'like', "%$q%");
            });
        }

        $listVerifikasi = $query->orderBy('updated_at', 'desc')->paginate(10);
        
        // Inject warna status untuk tampilan (opsional, karena sekarang isinya pasti biru/menunggu)
        foreach($listVerifikasi as $item) {
            $item->custom_status = 'Butuh Validasi';
            $item->status_color = 'blue';
        }

        return view('ppk.verifikasi.index', compact('listVerifikasi'));
    }

    public function show($id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        
        // Cek status saat ini
        $statusText = DB::table('statusperjadin')->where('id', $perjalanan->id_status)->value('nama_status');
        
        // Logic Lock: Jika status Selesai, form akan dikunci
        $isSelesai = ($statusText === 'Selesai');

        $laporanKeuangan = LaporanKeuangan::where('id_perjadin', $id)->first();

        // Ambil Peserta & Rincian Biaya
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

            $biaya = [
                'Tiket' => 0, 'Uang Harian' => 0, 'Penginapan' => 0, 
                'Uang Representasi' => 0, 'Transport' => 0, 'Sewa Kendaraan' => 0, 
                'Pengeluaran Riil' => 0, 'SSPB' => 0, 'Total' => 0
            ];
            
            // Inisialisasi data teks
            $info = [
                'Nama Penginapan' => '-', 
                'Kota' => '-', 
                'Kode Tiket' => '-', 
                'Maskapai' => '-'
            ];

            foreach($buktis as $b) {
                if ($b->nominal > 0) {
                    if(isset($biaya[$b->kategori])) {
                        $biaya[$b->kategori] += $b->nominal;
                    }
                    // Hitung total (kecuali SSPB pengembalian)
                    if($b->kategori != 'SSPB') {
                        $biaya['Total'] += $b->nominal;
                    }
                } else {
                    // Masukkan data teks ke array info
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
                'info' => $info
            ];
        }

        return view('ppk.verifikasi.detail', compact(
            'perjalanan', 'rekapData', 'statusText', 'isSelesai', 'totalSeluruhnya', 'laporanKeuangan'
        ));    
    }

    public function storeVerifikasi(Request $request, $id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);

        // Update Laporan Keuangan -> Selesai
        // Pastikan ID status 6 ('Selesai Dibayar' sesuai seed/dump sql Anda)
        $idLapSelesai = DB::table('statuslaporan')->where('nama_status', 'Selesai Dibayar')->value('id') ?? 6;
        
        LaporanKeuangan::updateOrCreate(
            ['id_perjadin' => $id],
            [
                'id_status' => $idLapSelesai,
                'verified_by' => Auth::user()->nip,
                'verified_at' => now(),
                'nomor_spm' => $request->nomor_spm,
                'tanggal_spm' => $request->tanggal_spm,
                'nomor_sp2d' => $request->nomor_sp2d,
                'tanggal_sp2d' => $request->tanggal_sp2d,
                'biaya_rampung' => $request->total_biaya_rampung
            ]
        );

        // Update Status Perjadin -> Selesai
        $idSelesai = DB::table('statusperjadin')->where('nama_status', 'Selesai')->value('id');
        $perjalanan->update(['id_status' => $idSelesai]);

        return redirect()->route('ppk.verifikasi.index')->with('success', 'Pembayaran disetujui. Data dipindahkan ke Riwayat.');
    }

    public function reject(Request $request, $id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        
        // Update Status Perjadin -> Perlu Revisi
        $idRevisi = DB::table('statusperjadin')->where('nama_status', 'Perlu Revisi')->value('id');
        $perjalanan->update([
            'id_status' => $idRevisi,
            'catatan_penolakan' => $request->alasan_penolakan
        ]);

        // Update Status Laporan Keuangan -> Perlu Revisi
        $idLapRevisi = DB::table('statuslaporan')->where('nama_status', 'Perlu Revisi')->value('id');
        LaporanKeuangan::updateOrCreate(
            ['id_perjadin' => $id],
            ['id_status' => $idLapRevisi]
        );

        return redirect()->route('ppk.verifikasi.index')->with('warning', 'Laporan dikembalikan ke PIC untuk direvisi.');
    }

    // --- (Fungsi tabelRekap dan exportRekap biarkan sama seperti sebelumnya) ---
    public function tabelRekap(Request $request) {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        $query = DB::table('laporankeuangan')
            ->join('perjalanandinas', 'laporankeuangan.id_perjadin', '=', 'perjalanandinas.id')
            ->join('pegawaiperjadin', 'pegawaiperjadin.id_perjadin', '=', 'perjalanandinas.id')
            ->join('users', 'pegawaiperjadin.id_user', '=', 'users.nip')
            ->leftJoin('pangkatgolongan', 'users.pangkat_gol_id', '=', 'pangkatgolongan.id')
            ->leftJoin('unitkerja', 'users.id_uke', '=', 'unitkerja.id')
            ->leftJoin('statuslaporan', 'laporankeuangan.id_status', '=', 'statuslaporan.id')
            ->select(
                'laporankeuangan.id as id_laporan',
                'laporankeuangan.nomor_spm',
                'laporankeuangan.tanggal_spm',
                'laporankeuangan.nomor_sp2d',
                'laporankeuangan.tanggal_sp2d',
                'laporankeuangan.biaya_rampung',
                'perjalanandinas.id as id_perjadin',
                'perjalanandinas.tujuan',
                'perjalanandinas.tgl_mulai',
                'perjalanandinas.tgl_selesai',
                'users.nama as nama_pegawai',
                'users.nip',
                'pangkatgolongan.nama_pangkat as pangkat_golongan',
                'unitkerja.nama_uke as unit_kerja',
                'statuslaporan.nama_status as status_laporan'
            )
            // Hanya ambil yang sudah Selesai Dibayar
            ->where('statuslaporan.nama_status', 'Selesai Dibayar'); 

        if ($tahun) $query->whereYear('perjalanandinas.tgl_mulai', $tahun);
        if ($bulan) $query->whereMonth('perjalanandinas.tgl_mulai', $bulan);

        $rekap = $query->orderBy('perjalanandinas.tgl_mulai')->orderBy('users.nama')->get();
        $totalDibayarkan = $rekap->sum('biaya_rampung');

        return view('ppk.tabelRekap', compact('rekap', 'totalDibayarkan', 'tahun', 'bulan'));
    }

    public function exportRekap(Request $request) {
        // ... (Kode sama) ...
        return Excel::download(new RekapPerjadinExport($request->tahun, $request->bulan), 'rekap.xlsx');
    }
}