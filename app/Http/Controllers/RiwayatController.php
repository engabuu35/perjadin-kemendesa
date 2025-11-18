<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    /**
     * Menampilkan halaman riwayat perjalanan dinas
     */
    public function index(Request $request)
    {
        // Ambil parameter pencarian jika ada
        $search = $request->input('search');
        
        // Query untuk mengambil data perjalanan dinas yang sudah selesai (id_status = 4)
        // dalam 1 tahun terakhir
        $query = DB::table('perjalanandinas')
            ->where('id_status', 4) // Status Selesai
            ->where('created_at', '>=', Carbon::now()->subYear()); // 1 tahun terakhir
        
        // Jika ada pencarian, filter berdasarkan nomor surat atau tujuan
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat', 'LIKE', "%{$search}%")
                  ->orWhere('tujuan', 'LIKE', "%{$search}%");
            });
        }
        
        // Urutkan dari yang terbaru
        $riwayat_list = $query->orderBy('created_at', 'desc')->get();
        
        // Kirim data ke view pages.riwayat
        return view('pages.riwayat', compact('riwayat_list'));
    }
    
    /**
     * Menampilkan detail perjalanan dinas
     */
    public function show($id)
    {
        // Ambil data perjalanan dinas berdasarkan ID
        $perjalanan = DB::table('perjalanandinas')
            ->where('id', $id)
            ->first();
        
        // Jika data tidak ditemukan
        if (!$perjalanan) {
            return redirect()->route('riwayat.index')
                ->with('error', 'Data perjalanan dinas tidak ditemukan.');
        }
        
        return view('pages.detail', compact('perjalanan'));
    }
}