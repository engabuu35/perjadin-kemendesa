<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerjalananDinas;
use App\Models\LaporanPerjadin;
use App\Models\BuktiLaporan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PelaporanController extends Controller
{
    public function index(Request $request)
    {
        // ... (Kode Index Sama) ...
        $query = PerjalananDinas::query();
        $query->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
              ->select('perjalanandinas.*', 'statusperjadin.nama_status');
        $query->whereIn('statusperjadin.nama_status', ['Menunggu Verifikasi Laporan', 'Menunggu Validasi PPK', 'Selesai', 'Ditolak']);

        if ($request->has('q')) {
            $q = $request->q;
            $query->where('nomor_surat', 'like', "%$q%")->orWhere('tujuan', 'like', "%$q%");
        }

        $laporanList = $query->orderBy('updated_at', 'desc')->paginate(10);
        return view('pic.pelaporan.index', compact('laporanList'));
    }

    public function show($id)
    {
        // ... (Kode Show Sama) ...
        $perjalanan = PerjalananDinas::findOrFail($id);
        $statusText = DB::table('statusperjadin')->where('id', $perjalanan->id_status)->value('nama_status');
        $isReadOnly = in_array($statusText, ['Menunggu Validasi PPK', 'Selesai']);

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
        $statusId = DB::table('statusperjadin')->where('nama_status', 'Menunggu Verifikasi Laporan')->value('id');
        
        if ($perjalanan->id_status != $statusId) {
            return back()->with('error', 'Data sudah dikunci.');
        }

        $request->validate([
            'target_nip' => 'required|exists:users,nip',
            'kategori'   => 'required',
            'bukti'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', 
        ]);

        $dataToSave = [
            'kategori' => $request->kategori,
            'nama_file' => null,
            'path_file' => null,
            'nominal' => 0,
            'keterangan' => null
        ];

        if ($request->filled('nominal')) {
            // --- KASUS 1: INPUT BIAYA ---
            $request->validate(['nominal' => 'required|numeric|min:0']);
            $dataToSave['nominal'] = $request->nominal;
        } else {
            // --- KASUS 2: INPUT TEKS (KETERANGAN) ---
            // Ambil input dinamis dari form
            if ($request->kategori == 'Maskapai') {
                $request->validate(['maskapai' => 'required']);
                $dataToSave['keterangan'] = $request->maskapai;
            } 
            elseif ($request->kategori == 'Kode Tiket') {
                $request->validate(['kode_tiket' => 'required']);
                $dataToSave['keterangan'] = $request->kode_tiket;
            }
            elseif ($request->kategori == 'Nama Penginapan') {
                $request->validate(['nama_hotel' => 'required']);
                $dataToSave['keterangan'] = $request->nama_hotel;
            }
            elseif ($request->kategori == 'Kota') {
                $request->validate(['kota' => 'required']);
                $dataToSave['keterangan'] = $request->kota;
            }
        }

        $laporan = LaporanPerjadin::firstOrCreate(
            ['id_perjadin' => $id, 'id_user' => $request->target_nip],
            ['uraian' => 'Diinput oleh PIC', 'is_final' => true] 
        );
        $dataToSave['id_laporan'] = $laporan->id;

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_' . $request->target_nip . '_' . $file->getClientOriginalName();
            $dataToSave['path_file'] = $file->storeAs('bukti_perjadin', $filename, 'public');
            $dataToSave['nama_file'] = $filename;
        }

        BuktiLaporan::create($dataToSave);
        return back()->with('success', 'Data berhasil disimpan.');
    }

    public function deleteBukti($idBukti)
    {
        // ... (Sama) ...
        $bukti = BuktiLaporan::find($idBukti);
        if($bukti) {
            if($bukti->path_file && Storage::disk('public')->exists($bukti->path_file)) {
                Storage::disk('public')->delete($bukti->path_file);
            }
            $bukti->delete();
            return back()->with('success', 'Item dihapus');
        }
        return back()->with('error', 'Item tidak ditemukan');
    }

    public function submitToPPK($id)
    {
        // ... (Sama) ...
        $perjalanan = PerjalananDinas::findOrFail($id);
        $idPPK = DB::table('statusperjadin')->where('nama_status', 'Menunggu Validasi PPK')->value('id');
        $perjalanan->update(['id_status' => $idPPK]);
        return redirect()->route('pic.pelaporan.index')->with('success', 'Terkirim ke PPK.');
    }
}