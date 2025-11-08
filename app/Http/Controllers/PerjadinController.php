<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Perjalanan; // Dikomentari karena Model belum ada
use Illuminate\Support\Facades\Log; // Untuk debugging
use Illuminate\Support\Facades\Validator; 

class PerjadinController extends Controller
{
    /**
     * Menampilkan halaman detail perjalanan dinas.
     * (Dipanggil oleh rute 'perjalanan.detail')
     */
    public function show($id)
    {
        // 1. (Untuk Tes) Buat data dummy jika belum ada database
        $perjalanan = (object) [
            'id' => $id,
            'nomor_surat' => '489/PRC.03.01/2024',
            'tanggal' => '30 Desember 2024 - 1 Januari 2025'
        ];

        // 2. Tampilkan view dan kirim data 'perjalanan' ke dalamnya
        // Menggunakan path yang benar: resources/views/pages/detailperjadin.blade.php
        return view('pages.detailperjadin', [
            'perjalanan' => $perjalanan
        ]);
    }

    /**
     * Menyimpan data laporan (Uraian & Biaya).
     * (Dipanggil oleh rute 'perjalanan.storeLaporan')
     */
    public function storeLaporan(Request $request, $id)
    {
        // Untuk debugging, kita lihat dulu data yang dikirim
        // dd($request->all()); 
        
        Log::info('Data Laporan Diterima:', $request->all());

        // TODO:
        // 1. Validasi request (uraian, kategori, bukti)
        // 2. Simpan uraian ke database
        // 3. Simpan file bukti ke storage
        // 4. Simpan data biaya (kategori & path bukti) ke database

        // 4. Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Laporan berhasil dikirim!');
    }

    /**
     * Menandai kehadiran (via AJAX) dan menyimpan lokasi geotag.
     * (Dipanggil oleh rute 'perjalanan.hadir')
     */
    public function tandaiKehadiran(Request $request, $id)
    {
        // 1. Validasi data yang masuk (dari LocationController)
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data lokasi tidak valid.'
            ], 422);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        
        // TODO:
        // 2. Simpan data kehadiran (user_id, perjalanan_id, latitude, longitude)
        //    Contoh:
        //    Kehadiran::create([
        //        'user_id' => auth()->id(),
        //        'perjalanan_id' => $id,
        //        'latitude' => $latitude,
        //        'longitude' => $longitude,
        //    ]);
        
        Log::info("Kehadiran ditandai untuk perjalanan id: $id di Lat: $latitude, Lon: $longitude");

        // 3. Kirim respon JSON kembali ke JavaScript
        return response()->json([
            'status' => 'success',
            'message' => "Kehadiran berhasil ditandai di ($latitude, $longitude)!"
        ]);
    }
}