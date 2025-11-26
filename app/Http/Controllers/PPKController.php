<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerjalananDinas;

class PPKController extends Controller
{
    public function detailPelaporan($id)
    {
        $perjalanan = PerjalananDinas::with([
            'pegawai',                // semua pegawai pada perjadin
            'laporanKeuangan.status', // status laporan
            'laporanKeuangan.verifier', // PPK/PIC yang memverifikasi
        ])->findOrFail($id);

        return view('ppk.detailPelaporan', compact('perjalanan'));
    }

    public function index(Request $request)
    {
        // Pilihan A: gunakan paginate (direkomendasikan)
        $perjalanans = PerjalananDinas::with(['laporanKeuangan.status', 'status'])
            ->whereHas('laporanKeuangan')           // hanya yang sudah submit laporan
            ->orderBy('created_at', 'desc')
            ->paginate(9);                          // ubah angka sesuai grid

        // Jika Anda tidak mau paginate, pakai get():
        // $perjalanans = PerjalananDinas::with(['laporanKeuangan.status', 'status'])
        //    ->whereHas('laporanKeuangan')
        //    ->orderBy('created_at','desc')
        //    ->get();

        return view('ppk.pelaporan', compact('perjalanans'));
    }
}
