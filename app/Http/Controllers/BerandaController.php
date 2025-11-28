<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BerandaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ambil Data Perjadin milik User Login
        // Filter: TIDAK MUNCULKAN yang statusnya sudah 'Selesai'
        // Karena 'Selesai' akan masuk ke Riwayat
        $daftarPerjadin = DB::table('perjalanandinas')
            ->join('pegawaiperjadin', 'perjalanandinas.id', '=', 'pegawaiperjadin.id_perjadin')
            ->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
            ->where('pegawaiperjadin.id_user', $user->nip)
            ->where('statusperjadin.nama_status', '!=', 'Selesai') // Filter Selesai
            ->select(
                'perjalanandinas.*',
                'statusperjadin.nama_status as status_db'
            )
            ->orderBy('perjalanandinas.tgl_mulai', 'asc')
            ->get();

        $perjalanan_list = $daftarPerjadin->map(function($item) {
            $today = Carbon::now()->startOfDay();
            $mulai = Carbon::parse($item->tgl_mulai)->startOfDay();
            $selesai = Carbon::parse($item->tgl_selesai)->endOfDay();
            
            // LOGIKA TAMPILAN STATUS DI BERANDA PEGAWAI
            
            // 1. Cek Status Database (Prioritas Utama untuk Flow Sistem)
            if ($item->status_db == 'Menunggu Verifikasi Laporan' || 
                $item->status_db == 'Menunggu Verifikasi' || 
                $item->status_db == 'Perlu Revisi') {
                
                $status = 'Menunggu Proses';
                $status_color = 'orange'; // Menunggu PIC/PPK
                $catatan = 'Laporan sedang diverifikasi admin.';
            }
            // 2. Jika Status Database masih Draft/Belum/Sedang, mainkan Logika Tanggal
            else {
                if ($today->lt($mulai)) {
                    $status = 'Belum Berlangsung';
                    $status_color = 'blue';
                    $catatan = 'Menunggu tanggal keberangkatan';
                } 
                elseif ($today->between($mulai, $selesai)) {
                    $status = 'Sedang Berlangsung';
                    $status_color = 'green';
                    $catatan = 'Selamat bertugas, jangan lupa absen.';
                } 
                else {
                    // Tanggal sudah lewat, tapi status DB belum berubah (artinya pegawai belum klik Selesai)
                    $status = 'Perlu Tindakan';
                    $status_color = 'red';
                    $catatan = 'Harap klik tombol Selesai di detail.';
                }
            }

            $tglString = Carbon::parse($item->tgl_mulai)->translatedFormat('d M') . ' - ' . 
                         Carbon::parse($item->tgl_selesai)->translatedFormat('d M Y');

            return (object) [
                'id' => $item->id,
                'nomor_surat' => $item->nomor_surat,
                'lokasi' => $item->tujuan,
                'tanggal' => $tglString,
                'status' => $status,
                'status_color' => $status_color,
                'catatan' => $catatan
            ];
        });

        return view('pages.beranda', compact('perjalanan_list'));
    }
}