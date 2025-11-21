<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerjalananDinas; // sesuaikan jika model berbeda

class PPKController extends Controller
{
    /**
     * Menampilkan halaman detail pelaporan untuk PPK
     */
    public function detailPelaporan($id)
    {
        // Ambil data perjalanan/laporan sesuai model Anda
        $perjalanan = PerjalananDinas::with(['pegawai', 'laporanKeuangan'])->findOrFail($id);

        return view('ppk.detailPelaporan', compact('perjalanan'));
    }
}
