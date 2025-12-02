<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerjalananDinas;
use App\Models\LaporanPerjadin;
use App\Models\BuktiLaporan;
use App\Models\LaporanKeuangan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Pastikan import ini ada

class PelaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = PerjalananDinas::query();
        $query->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
              ->leftJoin('laporankeuangan', 'perjalanandinas.id', '=', 'laporankeuangan.id_perjadin')
              ->select(
                  'perjalanandinas.*', 
                  'statusperjadin.nama_status',
                  'laporankeuangan.id as id_keuangan'
               );

        $query->whereIn('statusperjadin.nama_status', [
            'Pembuatan Laporan', 
            'Menunggu Verifikasi Laporan',
            'Perlu Revisi',
            'Menunggu Verifikasi',
            'Menunggu Validasi PPK'
        ]);

        if ($request->has('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q){
                $sub->where('nomor_surat', 'like', "%$q%")
                    ->orWhere('tujuan', 'like', "%$q%");
            });
        }

        $laporanList = $query->orderBy('updated_at', 'desc')->paginate(12);
        
        $laporanList->getCollection()->transform(function ($item) {
            if ($item->nama_status == 'Perlu Revisi') {
                $item->custom_status = 'Perlu Revisi';
                $item->status_color  = 'red'; 
                $item->status_icon   = '<i class="fa-solid fa-triangle-exclamation"></i>';
            } 
            elseif (in_array($item->nama_status, ['Menunggu Verifikasi', 'Menunggu Validasi PPK'])) {
                $item->custom_status = 'Menunggu PPK';
                $item->status_color  = 'yellow'; 
                $item->status_icon   = '<i class="fa-solid fa-clock"></i>';
            }
            else {
                if (!$item->id_keuangan) {
                    $item->custom_status = 'Perlu Tindakan';
                    $item->status_color  = 'blue'; 
                    $item->status_icon   = '<i class="fa-solid fa-bolt"></i>';
                } else {
                    $item->custom_status = 'Sedang Dilengkapi';
                    $item->status_color  = 'indigo'; 
                    $item->status_icon   = '<i class="fa-regular fa-pen-to-square"></i>';
                }
            }
            return $item;
        });

        return view('pic.pelaporan.index', compact('laporanList'));
    }

    public function show($id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        $statusText = DB::table('statusperjadin')->where('id', $perjalanan->id_status)->value('nama_status');
        
        $editableStatuses = ['Pembuatan Laporan', 'Menunggu Verifikasi Laporan', 'Perlu Revisi'];
        $isReadOnly = !in_array($statusText, $editableStatuses);

        $allPeserta = DB::table('pegawaiperjadin')
            ->join('users', 'users.nip', '=', 'pegawaiperjadin.id_user')
            ->where('pegawaiperjadin.id_perjadin', $id)
            ->select('users.nip', 'users.nama as name', 'pegawaiperjadin.role_perjadin')
            ->get();

        // Siapkan Data Existing untuk Form Bulk
        foreach($allPeserta as $peserta) {
            $laporan = LaporanPerjadin::with('bukti')->where('id_perjadin', $id)->where('id_user', $peserta->nip)->first();
            $peserta->laporan = $laporan;
            
            // Map bukti ke array assoc biar gampang dipanggil di view: $buktiMap['Tiket']
            $buktiMap = [];
            $total = 0;
            if($laporan && $laporan->bukti) {
                foreach($laporan->bukti as $b) {
                    $buktiMap[$b->kategori] = $b;
                    if ($b->kategori != 'SSPB') $total += $b->nominal; // Hitung total
                }
            }
            $peserta->buktiMap = $buktiMap;
            $peserta->total_biaya = $total;
        }

        // Definisi Kategori agar view dinamis
        $catBiaya = ['Tiket', 'Uang Harian', 'Penginapan', 'Uang Representasi', 'Sewa Kendaraan', 'Pengeluaran Riil', 'Transport', 'SSPB'];
        $catPendukung = ['Maskapai', 'Kode Tiket', 'Nama Penginapan', 'Kota'];

        return view('pic.pelaporan.detail', compact('perjalanan', 'allPeserta', 'statusText', 'isReadOnly', 'catBiaya', 'catPendukung'));
    }

    // --- FUNGSI BARU: SIMPAN MASSAL ---
    public function storeBulk(Request $request, $id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        $statusName = DB::table('statusperjadin')->where('id', $perjalanan->id_status)->value('nama_status');
        
        if (!in_array($statusName, ['Pembuatan Laporan', 'Menunggu Verifikasi Laporan', 'Perlu Revisi'])) {
            return back()->with('error', 'Data terkunci. Tidak bisa diedit.');
        }

        // 2. SIMPAN DATA KEUANGAN PEGAWAI
        $items = $request->input('items', []);
        $allFiles = $request->file('items') ?? [];

        foreach ($items as $nip => $categories) {
            $laporan = LaporanPerjadin::firstOrCreate(
                ['id_perjadin' => $id, 'id_user' => $nip], 
                ['uraian' => 'Laporan Keuangan', 'is_final' => 1]
            );

            foreach ($categories as $kategori => $data) {
                $nominal = isset($data['nominal']) ? (int) str_replace(['.', ','], '', $data['nominal']) : 0;
                $keterangan = $data['text'] ?? null;

                $pathFile = null;
                $namaFile = null;

                // Ambil data bukti lama
                $bukti = BuktiLaporan::where('id_laporan', $laporan->id)
                                     ->where('kategori', str_replace('_', ' ', $kategori))
                                     ->first();

                $fileBaru = $allFiles[$nip][$kategori]['file'] ?? null;

                if ($fileBaru && $fileBaru->isValid()) {
                    $cleanKategori = str_replace(' ', '', $kategori);
                    $filename = time().'_'.$nip.'_'.$cleanKategori.'_'.$fileBaru->getClientOriginalName();
                    
                    $pathFile = $fileBaru->storeAs('bukti_perjadin', $filename, 'public');
                    $namaFile = $fileBaru->getClientOriginalName();
                    
                    if ($bukti && $bukti->path_file) {
                        Storage::disk('public')->delete($bukti->path_file);
                    }
                } else {
                    if ($bukti) {
                        $pathFile = $bukti->path_file;
                        $namaFile = $bukti->nama_file;
                    }
                }

                BuktiLaporan::updateOrCreate(
                    [
                        'id_laporan' => $laporan->id, 
                        'kategori' => $kategori 
                    ],
                    [
                        'nominal' => $nominal,
                        'keterangan' => $keterangan,
                        'path_file' => $pathFile,
                        'nama_file' => $namaFile
                    ]
                );
            }
        }

        // Update status laporan keuangan
        $statusAwal = DB::table('statuslaporan')->where('nama_status', 'Perlu Tindakan')->value('id') ?? 2;
        LaporanKeuangan::firstOrCreate(['id_perjadin' => $id], ['id_status' => $statusAwal, 'created_at' => now()]);

        return back()->with('success', 'Seluruh data berhasil disimpan.');
    }

    public function deleteBukti($idBukti) {
        // Method ini mungkin jarang dipakai di mode bulk, tapi biarkan saja
        $bukti = BuktiLaporan::find($idBukti);
        if($bukti) { $bukti->delete(); return back()->with('success', 'Dihapus'); }
        return back();
    }

    public function submitToPPK($id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        
        $idPPK = DB::table('statusperjadin')->where('nama_status', 'Menunggu Validasi PPK')->value('id');
        if (!$idPPK) {
             $idPPK = DB::table('statusperjadin')->where('nama_status', 'Menunggu Verifikasi')->value('id');
        }

        if ($idPPK) {
            $perjalanan->update(['id_status' => $idPPK, 'catatan_penolakan' => null]);
        } else {
            return back()->with('error', 'Status PPK tidak ditemukan.');
        }

        $idLapPPK = DB::table('statuslaporan')->where('nama_status', 'Menunggu Verifikasi')->value('id');
        if (!$idLapPPK) $idLapPPK = 3; 

        LaporanKeuangan::updateOrCreate(['id_perjadin' => $id], ['id_status' => $idLapPPK, 'updated_at' => now()]);

        return redirect()->route('pic.pelaporan.index')->with('success', 'Laporan berhasil dikirim ke PPK.');
    }
}