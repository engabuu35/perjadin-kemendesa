<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PimpinanController extends Controller
{
    public function index()
    {
        $statusOnProgress = DB::table('statusperjadin')
            ->where('nama_status', 'Sedang Berlangsung')
            ->value('id');
        
        $pegawaiOnProgress = DB::table('perjalanandinas')
            ->where('id_status', $statusOnProgress)
            ->count();
        
        $perjalanandinas = DB::table('perjalanandinas')
            ->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
            ->leftJoin('users as pembuat', 'perjalanandinas.id_pembuat', '=', 'pembuat.nip')
            ->where('perjalanandinas.id_status', $statusOnProgress)
            ->select(
                'perjalanandinas.*', 
                'statusperjadin.nama_status',
                'pembuat.nama as nama_pembuat',
                'pembuat.email as email_pembuat'
            )
            ->orderBy('perjalanandinas.tgl_mulai', 'desc')
            ->get();
        
        $barChartData = [];
        $tahunSekarang = Carbon::now()->year;
        
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $count = DB::table('perjalanandinas')
                ->whereYear('tgl_mulai', $tahunSekarang)
                ->whereMonth('tgl_mulai', $bulan)
                ->count();
            
            $barChartData[] = $count;
        }
        
        $totalSebulanTerakhir = DB::table('perjalanandinas')
            ->where('tgl_mulai', '>=', Carbon::now()->subDays(30))
            ->where('tgl_mulai', '<=', Carbon::now())
            ->count();
        
        $lineChartData = [];
        
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $totalAnggaran = DB::table('laporankeuangan')
                ->join('perjalanandinas', 'laporankeuangan.id_perjadin', '=', 'perjalanandinas.id')
                ->whereYear('perjalanandinas.tgl_mulai', $tahunSekarang)
                ->whereMonth('perjalanandinas.tgl_mulai', $bulan)
                ->whereNotNull('laporankeuangan.biaya_rampung')
                ->sum('laporankeuangan.biaya_rampung');
            
            $lineChartData[] = (int)$totalAnggaran;
        }
        
        $anggaranSebulanTerakhir = (int)DB::table('laporankeuangan')
            ->join('perjalanandinas', 'laporankeuangan.id_perjadin', '=', 'perjalanandinas.id')
            ->where('perjalanandinas.tgl_mulai', '>=', Carbon::now()->subDays(30))
            ->where('perjalanandinas.tgl_mulai', '<=', Carbon::now())
            ->whereNotNull('laporankeuangan.biaya_rampung')
            ->sum('laporankeuangan.biaya_rampung');
        
        return view('pimpinan.monitoringPegawai', compact(
            'pegawaiOnProgress',
            'perjalanandinas',
            'barChartData',
            'lineChartData',
            'totalSebulanTerakhir',
            'anggaranSebulanTerakhir'
        ));
    }
    
    public function detail($id)
    {
        $perjadin = DB::table('perjalanandinas')
            ->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
            ->where('perjalanandinas.id', $id)
            ->select('perjalanandinas.*', 'statusperjadin.nama_status')
            ->first();
        
        if (!$perjadin) {
            abort(404, 'Data perjalanan dinas tidak ditemukan');
        }
        
        $pembuat = DB::table('users')
            ->where('nip', $perjadin->id_pembuat)
            ->first();
        
        $approver = null;
        if ($perjadin->approved_by) {
            $approver = DB::table('users')
                ->where('nip', $perjadin->approved_by)
                ->first();
        }
        
        $pegawai = DB::table('pegawaiperjadin')
            ->join('users', 'pegawaiperjadin.id_user', '=', 'users.nip')
            ->where('pegawaiperjadin.id_perjadin', $id)
            ->select(
                'pegawaiperjadin.*', 
                'users.nama', 
                'users.email',
                'users.nip'
            )
            ->get();
        
        $laporan = DB::table('laporankeuangan')
            ->leftJoin('statuslaporan', 'laporankeuangan.id_status', '=', 'statuslaporan.id')
            ->where('laporankeuangan.id_perjadin', $id)
            ->select('laporankeuangan.*', 'statuslaporan.nama_status as status_laporan')
            ->first();
        
        $rincian = [];
        if ($laporan) {
            $rincian = DB::table('rinciananggaran')
                ->join('kategoribiaya', 'rinciananggaran.id_kategori', '=', 'kategoribiaya.id')
                ->where('rinciananggaran.id_laporan', $laporan->id)
                ->select(
                    'rinciananggaran.*', 
                    'kategoribiaya.nama_kategori',
                    'kategoribiaya.id as id_kategori_biaya'
                )
                ->get();
        }
        
        $geotagging = DB::table('geotagging')
            ->join('users', 'geotagging.id_user', '=', 'users.nip')
            ->leftJoin('tipegeotagging', 'geotagging.id_tipe', '=', 'tipegeotagging.id')
            ->where('geotagging.id_perjadin', $id)
            ->select(
                'geotagging.*', 
                'users.nama', 
                'users.nip',
                'tipegeotagging.nama_tipe'
            )
            ->orderBy('geotagging.created_at', 'desc')
            ->get();
        
        return view('pimpinan.detail', compact(
            'perjadin',
            'pembuat',
            'approver',
            'pegawai',
            'laporan',
            'rincian',
            'geotagging'
        ));
    }
}
