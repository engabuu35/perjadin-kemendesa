<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerjalananDinas;
use App\Models\LaporanPerjadin;
use App\Models\BuktiLaporan;
use App\Models\LaporanKeuangan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        $query->where(function($q) {
            $q->whereIn('statusperjadin.nama_status', [
                'Pembuatan Laporan', 
                'Menunggu Verifikasi Laporan',
                'Perlu Revisi',
                'Menunggu Verifikasi',
                'Menunggu Validasi PPK'
            ])
            ->orWhere('statusperjadin.nama_status', 'Diselesaikan Manual')
            ->orWhere('statusperjadin.nama_status', 'Selesai');
        });

        if ($request->has('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q){
                $sub->where('nomor_surat', 'like', "%$q%")
                    ->orWhere('tujuan', 'like', "%$q%");
            });
        }

        // Logika: Jika status 'Selesai', beri nilai 1. Jika bukan, beri nilai 0.
        // Urutkan ASC (0 dulu, baru 1). Akibatnya 'Selesai' akan selalu di halaman belakang.
        $query->orderByRaw("CASE WHEN statusperjadin.nama_status = 'Selesai' THEN 1 ELSE 0 END ASC");

        // Setelah dipisah antara Selesai dan Belum, baru urutkan berdasarkan tanggal update terbaru
        $query->orderBy('updated_at', 'desc');

        $laporanList = $query->paginate(12);
        
        $laporanList->getCollection()->transform(function ($item) {
            if ($item->nama_status == 'Diselesaikan Manual') {
                if ($item->id_keuangan) {
                    $item->custom_status = 'Menunggu PPK';
                    $item->status_color  = 'yellow';
                    $item->status_icon   = '<i class="fa-solid fa-clock"></i>';
                } else {
                    $item->custom_status = 'Perlu Input Manual';
                    $item->status_color  = 'blue';
                    $item->status_icon   = '<i class="fa-solid fa-pen"></i>';
                }
            } 
            elseif ($item->nama_status == 'Perlu Revisi') {
                $item->custom_status = 'Perlu Revisi';
                $item->status_color  = 'red'; 
                $item->status_icon   = '<i class="fa-solid fa-triangle-exclamation"></i>';
            } 
            elseif (in_array($item->nama_status, ['Menunggu Verifikasi', 'Menunggu Validasi PPK'])) {
                $item->custom_status = 'Menunggu PPK';
                $item->status_color  = 'yellow'; 
                $item->status_icon   = '<i class="fa-solid fa-clock"></i>';
            }
            elseif ($item->nama_status == 'Selesai') {
                $item->custom_status = 'Selesai';
                $item->status_color  = 'green'; 
                $item->status_icon   = '<i class="fa-solid fa-circle-check"></i>';
            }
            else {
                if (!$item->id_keuangan) {
                    $item->custom_status = 'Perlu Tindakan';
                    $item->status_color  = 'blue'; 
                    $item->status_icon = '<i class="fa-solid fa-exclamation-circle"></i>';
                } else {
                    $item->custom_status = 'Sedang Dilengkapi';
                    $item->status_color  = 'gray'; 
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
        
        $editableStatuses = ['Pembuatan Laporan', 'Menunggu Verifikasi Laporan', 'Perlu Revisi', 'Diselesaikan Manual'];
        $isReadOnly = !in_array($statusText, $editableStatuses);

        $allPeserta = DB::table('pegawaiperjadin')
            ->join('users', 'users.nip', '=', 'pegawaiperjadin.id_user')
            ->where('pegawaiperjadin.id_perjadin', $id)
            ->select('users.nip', 'users.nama as name', 'pegawaiperjadin.role_perjadin')
            ->get();

        foreach($allPeserta as $peserta) {
            $laporan = LaporanPerjadin::with('bukti')->where('id_perjadin', $id)->where('id_user', $peserta->nip)->first();
            $peserta->laporan = $laporan;
            
            $buktiMap = [];
            $total = 0;
            if($laporan && $laporan->bukti) {
                foreach($laporan->bukti as $b) {
                    $buktiMap[$b->kategori] = $b;
                    if ($b->kategori != 'SSPB') $total += $b->nominal; 
                }
            }
            $peserta->buktiMap = $buktiMap;
            $peserta->total_biaya = $total;
        }

        $catBiaya = ['Tiket', 'Uang Harian', 'Penginapan', 'Uang Representasi', 'Sewa Kendaraan', 'Pengeluaran Riil', 'Transport', 'SSPB'];
        $catPendukung = ['Jenis Transportasi (Pergi)', 'Kode Tiket (Pergi)', 'Nama Transportasi (Pergi)', 'Jenis Transportasi (Pulang)', 'Kode Tiket (Pulang)', 'Nama Transportasi (Pulang)', 'Nama Penginapan', 'Kota'];

        return view('pic.pelaporan.detail', compact('perjalanan', 'allPeserta', 'statusText', 'isReadOnly', 'catBiaya', 'catPendukung'));
    }

    public function storeBulk(Request $request, $id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        $statusName = DB::table('statusperjadin')->where('id', $perjalanan->id_status)->value('nama_status');
        
        if (!in_array($statusName, ['Pembuatan Laporan', 'Menunggu Verifikasi Laporan', 'Perlu Revisi', 'Diselesaikan Manual'])) {
            return back()->with('error', 'Data terkunci. Tidak bisa diedit.');
        }

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

        $statusAwal = DB::table('statuslaporan')->where('nama_status', 'Perlu Tindakan')->value('id') ?? 2;
        LaporanKeuangan::firstOrCreate(['id_perjadin' => $id], ['id_status' => $statusAwal, 'created_at' => now()]);

        return back()->with('success', 'Seluruh data berhasil disimpan.');
    }

    public function deleteBukti($idBukti) {
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

        $ppkUsers = User::whereHas('roles', function($q) {
            $q->where('kode', 'PPK');
        })->pluck('nip')->toArray();

        if (!empty($ppkUsers)) {
            app(NotificationController::class)->sendFromTemplate(
                'laporan_masuk_ppk',
                $ppkUsers,
                [
                    'nomor_st' => $perjalanan->nomor_st ?? $perjalanan->tujuan ?? 'N/A',
                ],
                ['action_url' => '/ppk/verifikasi/' . $id]
            );
        }

        return redirect()->route('pic.pelaporan.index')->with('success', 'Laporan berhasil dikirim ke PPK.');
    }
}
