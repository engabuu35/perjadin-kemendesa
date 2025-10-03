<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Menampilkan halaman utama dengan semua lokasi yang tersimpan.
     */
    public function index()
    {
        // Ambil semua data lokasi, urutkan dari yang terbaru
        $locations = Location::latest()->get();

        // Kirim data ke view
        return view('geotag', ['locations' => $locations]);
    }

    /**
     * Menyimpan data lokasi baru yang dikirim dari frontend.
     */
    public function store(Request $request)
    {
        // Validasi data yang masuk
        $validatedData = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // Simpan ke database
        Location::create($validatedData);

        // Kirim respon sukses dalam format JSON
        return response()->json([
            'success' => true,
            'message' => 'Lokasi Anda berhasil disimpan!'
        ]);
    }
}
