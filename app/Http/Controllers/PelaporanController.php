<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerjalananDinas;
use App\Models\LaporanPerjadin;
use App\Models\BuktiLaporan;
use App\Models\LaporanKeuangan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        // Filter: Mengambil semua data yang relevan bagi PIC
        $query->whereIn('statusperjadin.nama_status', [
            'Pembuatan Laporan',           // Status Awal
            'Menunggu Verifikasi Laporan', // Status Alternatif
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
            // Status Merah (Revisi)
            if ($item->nama_status == 'Perlu Revisi') {
                $item->custom_status = 'Perlu Revisi';
                $item->status_color  = 'red'; 
                $item->status_icon   = '<i class="fa-solid fa-triangle-exclamation"></i>';
            } 
            // Status Kuning (Sudah di PPK)
            elseif (in_array($item->nama_status, ['Menunggu Verifikasi', 'Menunggu Validasi PPK'])) {
                $item->custom_status = 'Menunggu PPK';
                $item->status_color  = 'yellow'; 
                $item->status_icon   = '<i class="fa-solid fa-clock"></i>';
            }
            // Status Biru (Baru Masuk / Draft PIC)
            else {
                if (!$item->id_keuangan) {
                    $item->custom_status = 'Perlu Tindakan';
                    $item->status_color  = 'blue'; 
                    $item->status_icon   = '<i class="fa-solid fa-bolt"></i>';
                } else {
                    $item->custom_status = 'Sedang Dilengkapi';
                    $item->status_color  = 'indigo'; 
                    $item->status_icon   = '<i class="fa-solid fa-pen-to-square"></i>';
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
        
        // Hanya bisa diedit jika statusnya belum masuk PPK
        $editableStatuses = ['Pembuatan Laporan', 'Menunggu Verifikasi Laporan', 'Perlu Revisi'];
        $isReadOnly = !in_array($statusText, $editableStatuses);

        $allPeserta = DB::table('pegawaiperjadin')
            ->join('users', 'users.nip', '=', 'pegawaiperjadin.id_user')
            ->where('pegawaiperjadin.id_perjadin', $id)
            ->select('users.nip', 'users.nama as name', 'pegawaiperjadin.role_perjadin')
            ->orderBy('pegawaiperjadin.is_lead', 'desc')
            ->get();

        foreach($allPeserta as $peserta) {
            $laporan = LaporanPerjadin::with('bukti')->where('id_perjadin', $id)->where('id_user', $peserta->nip)->first();
            $peserta->laporan = $laporan;
            $peserta->total_biaya = $laporan ? $laporan->bukti->sum('nominal') : 0;
        }

        return view('pic.pelaporan.detail', compact('perjalanan', 'allPeserta', 'statusText', 'isReadOnly'));
    }

    public function storeBukti(Request $request, $id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        $statusName = DB::table('statusperjadin')->where('id', $perjalanan->id_status)->value('nama_status');
        
        $allowed = ['Pembuatan Laporan', 'Menunggu Verifikasi Laporan', 'Perlu Revisi'];
        if (!in_array($statusName, $allowed)) {
            return back()->with('error', 'Data sudah dikirim ke PPK. Tidak bisa diedit.');
        }

        $request->validate(['target_nip'=>'required', 'kategori'=>'required']);

        $dataToSave = ['kategori' => $request->kategori, 'nama_file' => null, 'path_file' => null, 'nominal' => 0, 'keterangan' => null];

        if ($request->filled('nominal')) {
            $dataToSave['nominal'] = $request->nominal;
        } else {
            if ($request->kategori == 'Maskapai') $dataToSave['keterangan'] = $request->maskapai;
            elseif ($request->kategori == 'Kode Tiket') $dataToSave['keterangan'] = $request->kode_tiket;
            elseif ($request->kategori == 'Nama Penginapan') $dataToSave['keterangan'] = $request->nama_hotel;
            elseif ($request->kategori == 'Kota') $dataToSave['keterangan'] = $request->kota;
        }

        $laporan = LaporanPerjadin::firstOrCreate(['id_perjadin' => $id, 'id_user' => $request->target_nip], ['uraian' => 'PIC Input', 'is_final' => 1]);
        $dataToSave['id_laporan'] = $laporan->id;

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $dataToSave['path_file'] = $file->storeAs('bukti_perjadin', time().'_'.$file->getClientOriginalName(), 'public');
            $dataToSave['nama_file'] = $file->getClientOriginalName();
        }
        BuktiLaporan::create($dataToSave);
        
        // Pastikan status laporan keuangan 'Perlu Tindakan' (ID 2) ada
        $statusAwal = DB::table('statuslaporan')->where('nama_status', 'Perlu Tindakan')->value('id') ?? 2;
        LaporanKeuangan::firstOrCreate(['id_perjadin' => $id], ['id_status' => $statusAwal, 'created_at' => now()]);

        return back()->with('success', 'Data tersimpan.');
    }

    public function deleteBukti($idBukti) {
        $bukti = BuktiLaporan::find($idBukti);
        if($bukti) { $bukti->delete(); return back()->with('success', 'Dihapus'); }
        return back();
    }

    // --- PERBAIKAN PENTING: KONSISTENSI STATUS KE PPK ---
    public function submitToPPK($id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        
        // Target: "Menunggu Validasi PPK"
        $idPPK = DB::table('statusperjadin')->where('nama_status', 'Menunggu Validasi PPK')->value('id');
        
        // Fallback: Jika tidak ketemu, cari "Menunggu Verifikasi"
        // if (!$idPPK) {
        //      $idPPK = DB::table('statusperjadin')->where('nama_status', 'Menunggu Verifikasi')->value('id');
        // }

        if ($idPPK) {
            $perjalanan->update(['id_status' => $idPPK, 'catatan_penolakan' => null]);
        } else {
            return back()->with('error', 'Status PPK (Menunggu Validasi PPK) tidak ditemukan di database.');
        }

        // Status Laporan Keuangan: "Menunggu Verifikasi"
        $idLapPPK = DB::table('statuslaporan')->where('nama_status', 'Menunggu Verifikasi')->value('id');
        if (!$idLapPPK) $idLapPPK = 3; // Fallback ID

        LaporanKeuangan::updateOrCreate(['id_perjadin' => $id], ['id_status' => $idLapPPK, 'updated_at' => now()]);

        return redirect()->route('pic.pelaporan.index')->with('success', 'Laporan berhasil dikirim ke PPK.');
    }
}