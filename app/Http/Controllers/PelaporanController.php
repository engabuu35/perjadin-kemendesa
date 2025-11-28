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
        $query = PerjalananDinas::query();
        $query->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
              ->select('perjalanandinas.*', 'statusperjadin.nama_status');

        // PIC melihat:
        // 1. Menunggu Verifikasi Laporan (Baru masuk dari Pegawai)
        // 2. Perlu Revisi (Ditolak PPK)
        // 3. Menunggu Verifikasi (History yang sudah dikirim ke PPK)
        $query->whereIn('statusperjadin.nama_status', [
            'Menunggu Verifikasi Laporan', 
            'Perlu Revisi',
            'Menunggu Verifikasi' 
        ]);

        if ($request->has('q')) {
            $q = $request->q;
            $query->where('nomor_surat', 'like', "%$q%")->orWhere('tujuan', 'like', "%$q%");
        }

        $laporanList = $query->orderBy('updated_at', 'desc')->paginate(10);
        return view('pic.pelaporan.index', compact('laporanList'));
    }

    public function show($id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        $statusText = DB::table('statusperjadin')->where('id', $perjalanan->id_status)->value('nama_status');
        
        // Read Only jika statusnya "Menunggu Verifikasi" (Sudah di PPK) atau "Selesai"
        $isReadOnly = in_array($statusText, ['Menunggu Verifikasi', 'Selesai']);

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
        
        // PIC hanya bisa edit kalau statusnya ini
        if (!in_array($statusName, ['Menunggu Verifikasi Laporan', 'Perlu Revisi'])) {
            return back()->with('error', 'Data sudah dikirim ke PPK. Tidak bisa diedit.');
        }

        $request->validate([
            'target_nip' => 'required',
            'kategori'   => 'required',
            'bukti'      => 'nullable|max:5120'
        ]);

        $dataToSave = [
            'kategori' => $request->kategori, 'nama_file' => null, 'path_file' => null, 'nominal' => 0, 'keterangan' => null
        ];

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
        return back()->with('success', 'Data tersimpan.');
    }

    // Fungsi Delete (Sama seperti sebelumnya)
    public function deleteBukti($idBukti) {
        $bukti = BuktiLaporan::find($idBukti);
        if($bukti) { $bukti->delete(); return back()->with('success', 'Dihapus'); }
        return back();
    }

    // --- KIRIM KE PPK ---
    public function submitToPPK($id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        
        // Ubah Status jadi "Menunggu Verifikasi" (Masuk PPK)
        $idPPK = DB::table('statusperjadin')->where('nama_status', 'Menunggu Verifikasi')->value('id');
        
        $perjalanan->update(['id_status' => $idPPK, 'catatan_penolakan' => null]);

        return redirect()->route('pic.pelaporan.index')->with('success', 'Laporan berhasil dikirim ke PPK.');
    }
}