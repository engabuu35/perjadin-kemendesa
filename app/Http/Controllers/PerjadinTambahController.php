<?php

namespace App\Http\Controllers;

use App\Models\PerjalananDinas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PerjadinTambahController extends Controller
{
    public function create()
    {
        $users = User::select('nip','nama')->get();

        // Ambil semua row (mapping status pegawai) -- tetap kalau kamu pakai
        $rows = DB::table('pegawaiperjadin as pp')
            ->join('perjalanandinas as p', 'pp.id_perjadin', '=', 'p.id')
            ->join('users as u', 'pp.id_user', '=', 'u.nip')
            ->select('u.nip', 'p.id_status', 'p.tgl_mulai', 'p.tgl_selesai')
            ->orderBy('p.id_status', 'asc')
            ->get();

        $pegawaiStatus = [];
        foreach ($rows as $r) {
            $nip = (string) $r->nip;
            $pegawaiStatus[$nip] = $r->id_status;
        }

        // Hitung pegawai yang sedang punya perjalanan "aktif" sekarang
        $today = Carbon::today()->toDateString();

        // Ambil id status "Sedang Berlangsung" (jika ada)
        $sedangBerlangsungId = DB::table('statusperjadin')
            ->where('nama_status', 'Sedang Berlangsung')
            ->value('id');

        $activeQuery = DB::table('pegawaiperjadin as pp')
            ->join('perjalanandinas as p', 'pp.id_perjadin', '=', 'p.id')
            ->where(function($q) use ($today, $sedangBerlangsungId) {
                // kondisi: tanggal tumpang tindih (today in [tgl_mulai, tgl_selesai])
                $q->whereDate('p.tgl_mulai', '<=', $today)
                  ->whereDate('p.tgl_selesai', '>=', $today);

                // OR jika kamu ingin juga memasukkan berdasarkan status "Sedang Berlangsung"
                if ($sedangBerlangsungId) {
                    $q->orWhere('p.id_status', $sedangBerlangsungId);
                }
            })
            ->select('pp.id_user')
            ->distinct();

        $pegawaiActive = $activeQuery->pluck('id_user')->toArray();

        // Ambil list pimpinan dari penugasanperan (role_id = 1)
        $pimpinans = DB::table('penugasanperan as pp')
            ->join('users as u', 'pp.user_id', '=', 'u.nip')
            ->where('pp.role_id', 1)
            ->select('u.nip', 'u.nama')
            ->get();

        return view('pic.penugasanTambah', compact('users', 'pegawaiStatus', 'pimpinans', 'pegawaiActive'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'tujuan' => 'required|string',
            'tgl_mulai' => 'required|date|after_or_equal:tanggal_surat',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'pegawai' => 'required|array|min:1',
            'pegawai.*.nip' => 'required|string|exists:users,nip',
            'surat_tugas' => 'nullable|file|mimes:pdf|max:5120', // opsional
            'approved_by' => 'nullable|exists:users,nip', // opsional
        ]);

        $nips = array_map(fn($p) => $p['nip'], $validated['pegawai']);
        if (count($nips) !== count(array_unique($nips))) {
            return back()->withInput()->withErrors(['pegawai' => 'Nama pegawai tidak boleh duplikat']);
        }

        DB::transaction(function() use ($validated, $request) {
            
            // PERBAIKAN: Gunakan ID Status "Belum Berlangsung" yang dinamis
            $idStatusAwal = DB::table('statusperjadin')->where('nama_status', 'Belum Berlangsung')->value('id');
            if (!$idStatusAwal) {
                $idStatusAwal = DB::table('statusperjadin')->insertGetId(['nama_status' => 'Belum Berlangsung']);
            }

            $perjalanan = PerjalananDinas::create([
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'tujuan' => $validated['tujuan'],
                'tgl_mulai' => $validated['tgl_mulai'],
                'tgl_selesai' => $validated['tgl_selesai'],
                'approved_by' => $validated['approved_by'] ?? null,
                'id_pembuat' => Auth::id(),
                'id_status' => $idStatusAwal, 
            ]);

            // Handle file PDF
            if ($request->hasFile('surat_tugas')) {
                $file = $request->file('surat_tugas');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('surat_tugas', $filename, 'public');

                $perjalanan->surat_tugas = $path;
                $perjalanan->save();
            }

            // Insert pegawai & Init Laporan
            $insertPegawai = [];
            $insertLaporan = [];
            $now = now();

            foreach ($validated['pegawai'] as $pegawai) {
                // Data untuk tabel pivot pegawaiperjadin
                $insertPegawai[] = [
                    'id_perjadin' => $perjalanan->id,
                    'id_user' => $pegawai['nip'],
                ];

                // Data inisialisasi untuk tabel laporan_perjadin
                $insertLaporan[] = [
                    'id_perjadin' => $perjalanan->id,
                    'id_user' => $pegawai['nip'],
                    'uraian' => null, // Biarkan null dulu
                    'is_final' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

            if (!empty($insertPegawai)) {
                DB::table('pegawaiperjadin')->insert($insertPegawai);
            }

            // PERBAIKAN: Masukkan data laporan kosong agar tidak error di file lain
            if (!empty($insertLaporan)) {
                DB::table('laporan_perjadin')->insert($insertLaporan);
            }
        });

        return redirect()->route('pic.penugasan')
            ->with('success','Perjalanan Dinas berhasil disimpan.');
    }

    public function edit($id)
    {
        $users = User::select('nip','nama')->get();

        $rows = DB::table('pegawaiperjadin as pp')
            ->join('perjalanandinas as p', 'pp.id_perjadin', '=', 'p.id')
            ->join('users as u', 'pp.id_user', '=', 'u.nip')
            ->select('u.nip', 'p.id_status', 'p.tgl_mulai', 'p.tgl_selesai')
            ->orderBy('p.id_status', 'asc')
            ->get();

        $pegawaiStatus = [];
        foreach ($rows as $r) {
            $nip = (string) $r->nip;
            $pegawaiStatus[$nip] = $r->id_status;
        }

        $perjalanan = PerjalananDinas::findOrFail($id);

        $pegawaiList = DB::table('pegawaiperjadin as pp')
            ->join('users as u', 'pp.id_user', '=', 'u.nip')
            ->where('pp.id_perjadin', $id)
            ->select('u.nip', 'u.nama')
            ->get()
            ->map(fn($r) => ['nip' => $r->nip, 'nama' => $r->nama])
            ->toArray();

        // Hitung pegawai aktif (sama logic seperti create)
        $today = Carbon::today()->toDateString();
        $sedangBerlangsungId = DB::table('statusperjadin')
            ->where('nama_status', 'Sedang Berlangsung')
            ->value('id');

        $activeQuery = DB::table('pegawaiperjadin as pp')
            ->join('perjalanandinas as p', 'pp.id_perjadin', '=', 'p.id')
            ->where(function($q) use ($today, $sedangBerlangsungId) {
                $q->whereDate('p.tgl_mulai', '<=', $today)
                  ->whereDate('p.tgl_selesai', '>=', $today);

                if ($sedangBerlangsungId) {
                    $q->orWhere('p.id_status', $sedangBerlangsungId);
                }
            })
            ->select('pp.id_user')
            ->distinct();

        $pegawaiActive = $activeQuery->pluck('id_user')->toArray();

        $pimpinans = DB::table('penugasanperan as pp')
            ->join('users as u', 'pp.user_id', '=', 'u.nip')
            ->where('pp.role_id', 1)
            ->select('u.nip', 'u.nama')
            ->get();

        return view('pic.penugasanTambah', compact('users','pegawaiStatus','perjalanan','pegawaiList','pimpinans','pegawaiActive'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'tujuan' => 'required|string',
            'tgl_mulai' => 'required|date|after_or_equal:tanggal_surat',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'pegawai' => 'required|array|min:1',
            'pegawai.*.nip' => 'required|string|exists:users,nip',
            'surat_tugas' => 'nullable|file|mimes:pdf|max:5120',
            'approved_by' => 'nullable|exists:users,nip',
        ]);

        DB::transaction(function() use ($validated, $id) {
            $perjalanan = PerjalananDinas::findOrFail($id);
            $perjalanan->update([
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'tujuan' => $validated['tujuan'],
                'tgl_mulai' => $validated['tgl_mulai'],
                'tgl_selesai' => $validated['tgl_selesai'],
                'approved_by' => $validated['approved_by'],
            ]);

            if (request()->hasFile('surat_tugas')) {
                $file = request()->file('surat_tugas');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('surat_tugas', $filename, 'public');

                $perjalanan->surat_tugas = $path;
                $perjalanan->save();
            }

            // Hapus pegawai lama
            DB::table('pegawaiperjadin')->where('id_perjadin', $perjalanan->id)->delete();
            // PERBAIKAN: Hapus laporan lama juga agar sinkron (Opsional: tergantung kebijakan)
            // DB::table('laporan_perjadin')->where('id_perjadin', $perjalanan->id)->delete(); 
            // Jika Anda hapus laporan, data uraian lama hilang. Jika tidak, data uraian lama tetap ada.
            // Untuk amannya, kita biarkan laporan lama, nanti di insert kita pakai insertOrIgnore atau logic cek dulu.

            $insertPegawai = [];
            $insertLaporan = [];
            $now = now();

            foreach ($validated['pegawai'] as $pegawai) {
                $insertPegawai[] = [
                    'id_perjadin' => $perjalanan->id,
                    'id_user' => $pegawai['nip'],
                ];

                // Cek apakah sudah ada laporan, jika belum buat baru
                $exists = DB::table('laporan_perjadin')
                    ->where('id_perjadin', $perjalanan->id)
                    ->where('id_user', $pegawai['nip'])
                    ->exists();

                if (!$exists) {
                    $insertLaporan[] = [
                        'id_perjadin' => $perjalanan->id,
                        'id_user' => $pegawai['nip'],
                        'uraian' => null,
                        'is_final' => 0,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
            }

            if (!empty($insertPegawai)) {
                DB::table('pegawaiperjadin')->insert($insertPegawai);
            }
            if (!empty($insertLaporan)) {
                DB::table('laporan_perjadin')->insert($insertLaporan);
            }
        });

        return redirect()->route('pic.penugasan')
            ->with('success', 'Perjalanan Dinas berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $perjalanan = PerjalananDinas::findOrFail($id);

        // Map status string ke id_status
        $statusMap = [
            'Diselesaikan Manual' => 7, // sesuai seeder
            'Dibatalkan' => 8,
        ];

        if (!isset($statusMap[$request->status])) {
            return response()->json(['message' => 'Status tidak valid'], 422);
        }

        $perjalanan->id_status = $statusMap[$request->status];
        $perjalanan->save();

        return response()->json(['message' => 'Status berhasil diubah']);
    }
}