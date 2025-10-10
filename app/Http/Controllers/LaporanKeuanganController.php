<?php

namespace App\Http\Controllers;

use App\Models\LaporanKeuangan;
use App\Exports\LaporanKeuanganExport; // Import class export baru
use Maatwebsite\Excel\Facades\Excel; // Import facade Excel

class LaporanKeuanganController extends Controller
{
    /**
     * Menampilkan daftar laporan keuangan.
     */
    public function index()
    {
        $laporan = LaporanKeuangan::all();
        return view('laporan.index', ['laporan' => $laporan]);
    }

    /**
     * Membuat dan mengunduh laporan dalam format Excel.
     */
    public function generateExcel()
    {
        // Menggunakan class export untuk membuat file Excel
        return Excel::download(new LaporanKeuanganExport, 'laporan-keuangan.xlsx');
    }
}

