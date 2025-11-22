<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPerjadin;
use App\Models\User; // Asumsi model user ada

class LSRampungController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil daftar Pegawai yang sudah mengirim laporan (Final) untuk Dropdown
        // Kita ambil ID Laporan, Nama User, dan NIP
        $daftarLaporan = LaporanPerjadin::with('user')
            ->where('is_final', true)
            ->get();

        $selectedLaporan = null;
        $rekapBiaya = [
            'Tiket' => 0,
            'Uang Harian' => 0,
            'Penginapan' => 0,
            'Uang Representasi' => 0,
            'Transport' => 0, // Asumsi kategori lain masuk sini atau kategori khusus
            'Sewa Kendaraan' => 0,
            'Pengeluaran Riil' => 0,
            'SSPB' => 0,
            'Total' => 0
        ];

        // 2. Jika User memilih salah satu pegawai dari Dropdown
        if ($request->has('laporan_id') && $request->laporan_id != '') {
            $selectedLaporan = LaporanPerjadin::with(['user', 'bukti', 'perjalanan'])->find($request->laporan_id);

            if ($selectedLaporan) {
                // 3. Logika Penjumlahan (Pivot) berdasarkan Kategori
                foreach ($selectedLaporan->bukti as $bukti) {
                    $kategori = $bukti->kategori;
                    $nominal = $bukti->nominal ?? 0;

                    // Masukkan ke rekap biaya jika kategorinya dikenali
                    if (isset($rekapBiaya[$kategori])) {
                        $rekapBiaya[$kategori] += $nominal;
                    } else {
                        // Jika ada kategori lain yang tidak standar, mungkin masuk ke Lain-lain atau Transport
                        // Disini saya asumsikan kategori sesuai inputan dropdown di detailperjadin
                        // Jika kategori tidak ada di key array, kita abaikan atau buat logic lain
                    }
                    
                    // Hitung Total Keseluruhan (Kecuali SSPB biasanya pengembalian, tapi tergantung aturan)
                    // Disini saya asumsi Total = Jumlah semua pengeluaran riil
                    if ($kategori !== 'SSPB') {
                        $rekapBiaya['Total'] += $nominal;
                    }
                }
            }
        }

        return view('pic.lsrampung', compact('daftarLaporan', 'selectedLaporan', 'rekapBiaya'));
    }
}