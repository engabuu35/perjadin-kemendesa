<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Trip;
use App\Models\TripMember;

class TripController extends Controller
{
    /**
     * Menyimpan data perjalanan dinas baru.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'nomor_surat' => 'required|string|unique:trips',
            'tujuan' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            // Validasi peserta: harus array dan minimal ada 1 peserta
            'members' => 'required|array|min:1',
            // Validasi setiap item di dalam array members
            'members.*.user_id' => 'required|exists:users,id',
            'members.*.jabatan_saat_perdin' => 'required|string',
        ]);

        // 2. Buat Trip baru
        $trip = Trip::create([
            'creator_id' => Auth::id(), // Ambil ID user yang sedang login
            'nomor_surat' => $validated['nomor_surat'],
            'tujuan' => $validated['tujuan'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'lat' => $validated['lat'],
            'lon' => $validated['lon'],
        ]);

        // 3. Tambahkan peserta ke trip tersebut
        foreach ($validated['members'] as $member) {
            TripMember::create([
                'trip_id' => $trip->id,
                'user_id' => $member['user_id'],
                'jabatan_saat_perdin' => $member['jabatan_saat_perdin'],
            ]);
        }
        
        // 4. Muat relasi members untuk response
        $trip->load('members.user');

        // 5. Kirim response berhasil
        return response()->json([
            'message' => 'Perjalanan dinas berhasil dibuat.',
            'data' => $trip,
        ], 201); // 201 Created
    }
}