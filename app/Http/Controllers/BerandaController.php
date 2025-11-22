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
        
        $daftarPerjadin = DB::table('perjalanandinas')
            ->join('pegawaiperjadin', 'perjalanandinas.id', '=', 'pegawaiperjadin.id_perjadin')
            ->where('pegawaiperjadin.id_user', $user->nip)
            ->select(
                'perjalanandinas.*',
                // Ambil file dari tabel induk
                'perjalanandinas.pdf_keuangan'
            )
            ->orderBy('perjalanandinas.tgl_mulai', 'desc')
            ->get();

        $perjalanan_list = $daftarPerjadin->map(function($item) {
            $today = Carbon::now()->startOfDay();
            $mulai = Carbon::parse($item->tgl_mulai)->startOfDay();
            $selesai = Carbon::parse($item->tgl_selesai)->endOfDay();
            
            // LOGIKA SEDERHANA:
            // Jika file di induk ada = SELESAI (Hijau)
            // Jika tidak ada = Cek Tanggal
            
            $isSubmitted = !empty($item->pdf_keuangan); 
            
            if ($isSubmitted) {
                $status = 'Selesai';
                $status_color = 'green';
                $catatan = 'Laporan tim lengkap';
            } 
            else {
                if ($today->lt($mulai)) {
                    $status = 'Akan Datang';
                    $status_color = 'blue';
                    $catatan = 'Menunggu keberangkatan';
                } 
                elseif ($today->between($mulai, $selesai)) {
                    $status = 'Sedang Berlangsung';
                    $status_color = 'yellow';
                    $catatan = 'Selamat bertugas';
                } 
                else {
                    $status = 'Belum Lengkap';
                    $status_color = 'red';
                    $catatan = 'Ketua/Anggota harap upload laporan gabungan';
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