<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PimpinanController extends Controller
{
    // Method untuk halaman beranda/dashboard
    public function index()
    {
        // Ambil ID status "Sedang Berlangsung"
        $statusOnProgress = DB::table('statusperjadin')
            ->where('nama_status', 'Sedang Berlangsung')
            ->value('id');
        
        // Hitung pegawai yang sedang dalam perjalanan dinas
        $pegawaiOnProgress = DB::table('perjalanandinas')
            ->where('id_status', $statusOnProgress)
            ->count();
        
        // Ambil data perjalanan dinas yang sedang berlangsung
        $perjalanandinas = DB::table('perjalanandinas')
            ->where('id_status', $statusOnProgress)
            ->orderBy('tgl_mulai', 'desc')
            ->get();
        
        // ===== DATA UNTUK BAR CHART (Jumlah Pegawai per Bulan) =====
        $barChartData = [];
        $totalSebulanTerakhir = 0;
        $sebulanLalu = Carbon::now()->subMonth();
        
        for ($i = 11; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            
            // Hitung jumlah perjalanan dinas per bulan
            $count = DB::table('perjalanandinas')
                ->whereYear('tgl_mulai', $bulan->year)
                ->whereMonth('tgl_mulai', $bulan->month)
                ->count();
            
            $barChartData[] = $count;
            
            // Hitung total sebulan terakhir
            if ($bulan->month == $sebulanLalu->month && $bulan->year == $sebulanLalu->year) {
                $totalSebulanTerakhir = $count;
            }
        }
        
        // ===== DATA UNTUK LINE CHART (Total Anggaran per Bulan) =====
        $lineChartData = [];
        $anggaranSebulanTerakhir = 0;
        
        for ($i = 11; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            
            // Hitung total anggaran dari laporan keuangan per bulan
            $totalAnggaran = DB::table('laporankeuangan')
                ->join('perjalanandinas', 'laporankeuangan.id_perjadin', '=', 'perjalanandinas.id')
                ->whereYear('perjalanandinas.tgl_mulai', $bulan->year)
                ->whereMonth('perjalanandinas.tgl_mulai', $bulan->month)
                ->whereNotNull('laporankeuangan.biaya_rampung')
                ->sum('laporankeuangan.biaya_rampung');
            
            $lineChartData[] = (int)$totalAnggaran;
            
            // Hitung anggaran sebulan terakhir
            if ($bulan->month == $sebulanLalu->month && $bulan->year == $sebulanLalu->year) {
                $anggaranSebulanTerakhir = (int)$totalAnggaran;
            }
        }
        
        // Return ke view 'pimpinan.monitoringPegawai'
        return view('pimpinan.monitoringPegawai', compact(
            'pegawaiOnProgress',
            'perjalanandinas',
            'barChartData',
            'lineChartData',
            'totalSebulanTerakhir',
            'anggaranSebulanTerakhir'
        ));
    }
    
    // Method untuk halaman detail
    public function detail($id)
    {
        // Ambil detail perjalanan dinas
        $perjadin = DB::table('perjalanandinas')
            ->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
            ->where('perjalanandinas.id', $id)
            ->select('perjalanandinas.*', 'statusperjadin.nama_status')
            ->first();
        
        if (!$perjadin) {
            abort(404, 'Data tidak ditemukan');
        }
        
        // Ambil data pembuat
        $pembuat = DB::table('users')
            ->where('nip', $perjadin->id_pembuat)
            ->first();
        
        // Ambil data yang menyetujui
        $approver = DB::table('users')
            ->where('nip', $perjadin->approved_by)
            ->first();
        
        // Ambil pegawai yang terlibat dalam perjalanan dinas
        $pegawai = DB::table('pegawaiperjadin')
            ->join('users', 'pegawaiperjadin.id_user', '=', 'users.nip')
            ->where('pegawaiperjadin.id_perjadin', $id)
            ->select('pegawaiperjadin.*', 'users.nama', 'users.email')
            ->get();
        
        // Ambil laporan keuangan
        $laporan = DB::table('laporankeuangan')
            ->leftJoin('statuslaporan', 'laporankeuangan.id_status', '=', 'statuslaporan.id')
            ->where('laporankeuangan.id_perjadin', $id)
            ->select('laporankeuangan.*', 'statuslaporan.nama_status as status_laporan')
            ->first();
        
        // Ambil rincian anggaran jika ada laporan
        $rincian = [];
        if ($laporan) {
            $rincian = DB::table('rinciananggaran')
                ->join('kategoribiaya', 'rinciananggaran.id_kategori', '=', 'kategoribiaya.id')
                ->where('rinciananggaran.id_laporan', $laporan->id)
                ->select('rinciananggaran.*', 'kategoribiaya.nama_kategori')
                ->get();
        }
        
        // Ambil data geotagging
        $geotagging = DB::table('geotagging')
            ->join('users', 'geotagging.id_user', '=', 'users.nip')
            ->leftJoin('tipegeotagging', 'geotagging.id_tipe', '=', 'tipegeotagging.id')
            ->where('geotagging.id_perjadin', $id)
            ->select('geotagging.*', 'users.nama', 'tipegeotagging.nama_tipe')
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