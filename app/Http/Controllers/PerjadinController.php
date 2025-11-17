<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Gunakan model yang sesuai dengan migrasi Anda
use App\Models\Geotagging; 
use App\Models\PerjalananDinas; // Asumsi untuk method 'show'
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Diperlukan untuk mengambil NIP user

class PerjadinController extends Controller
{
    /**
     * Menampilkan halaman detail perjalanan dinas.
     * (Dipanggil oleh rute 'perjalanan.detail')
     */
    public function show($id)
    {
        // 1. Ambil data asli dari database, bukan dummy
        $perjalanan = PerjalananDinas::find($id);

        if (!$perjalanan) {
            abort(404, 'Perjalanan dinas tidak ditemukan');
        }

        // 2. Tampilkan view dan kirim data 'perjalanan' ke dalamnya
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
     *
     * PERBAIKAN:
     * Logika ini diubah dari 'Kehadiran::create' (yang tidak ada)
     * menjadi 'Geotagging::create' (sesuai migrasi Anda).
     */
    public function tandaiKehadiran(Request $request, $id)
    {
        // 1. Validasi data yang masuk
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'id_tipe' => 'required|integer|exists:tipegeotagging,id', // Tipe (misal: 1=Berangkat, 2=Tiba)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data lokasi atau tipe geotagging tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // 2. Simpan data geotagging
            $geotag = Geotagging::create([
                'id_perjadin' => $id,
                'id_user'     => Auth::user()->nip, // Sesuai PK users.nip
                'id_tipe'     => $request->id_tipe,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
            ]);
            
            Log::info("Geotagging berhasil untuk perjadin id: $id oleh user: " . $geotag->id_user);

            // 3. Kirim respon JSON kembali ke JavaScript
            return response()->json([
                'status' => 'success',
                'message' => "Kehadiran (Geotag) berhasil ditandai!",
                'data' => $geotag
            ]);

        } catch (\Exception $e) {
            Log::error("Gagal simpan geotagging: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data ke server.'
            ], 500);
        }
    }
}