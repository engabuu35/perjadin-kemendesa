<?php

namespace App\Http\Controllers;

use App\Models\PerjalananDinas;
use App\Models\User;
use App\Models\LaporanPerjadin;
use App\Models\BuktiLaporan;
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

        // [UPDATE] Mengambil data lengkap jadwal untuk pengecekan bentrok di frontend
        $rows = DB::table('pegawaiperjadin as pp')
            ->join('perjalanandinas as p', 'pp.id_perjadin', '=', 'p.id')
            ->join('users as u', 'pp.id_user', '=', 'u.nip')
            ->select('u.nip', 'p.id_status', 'p.tgl_mulai', 'p.tgl_selesai', 'p.id as id_perjadin') // Tambah id_perjadin
            ->orderBy('p.id_status', 'asc')
            ->get();

        $pegawaiStatus = [];
        foreach ($rows as $r) {
            $nip = (string) $r->nip;
            $pegawaiStatus[$nip] = $r->id_status;
        }

        // Kita kirim $rows mentah sebagai $allSchedules ke view
        $allSchedules = $rows; 

        $today = Carbon::today()->toDateString();
        $sedangBerlangsungId = DB::table('statusperjadin')->where('nama_status', 'Sedang Berlangsung')->value('id');

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

        // Tambahkan compact 'allSchedules'
        return view('pic.penugasanTambah', compact('users', 'pegawaiStatus', 'pimpinans', 'pegawaiActive', 'allSchedules'));
    }

    public function store(Request $request)
    {
        $messages = [
            'tgl_mulai.after_or_equal' => 'Tanggal mulai tidak boleh mendahului (sebelum) Tanggal Surat.',
            'tgl_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan Tanggal Mulai.',
            'pegawai.required' => 'Minimal harus ada satu pegawai yang dipilih.',
        ];

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
            'dalam_rangka' => 'required|string',
        ], $messages);

        $nips = array_map(fn($p) => $p['nip'], $validated['pegawai']);
        if (count($nips) !== count(array_unique($nips))) {
            return back()->withInput()->withErrors(['pegawai' => 'Nama pegawai tidak boleh duplikat']);
        }

        DB::transaction(function() use ($validated, $request) {
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
                'dalam_rangka' => $request->input('dalam_rangka', null),
            ]);

            if ($request->hasFile('surat_tugas')) {
                $file = $request->file('surat_tugas');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('surat_tugas', $filename, 'public');
                $perjalanan->surat_tugas = $path;
                $perjalanan->save();
            }

            $insertPegawai = [];
            $insertLaporan = [];
            $now = now();

            foreach ($validated['pegawai'] as $pegawai) {
                $insertPegawai[] = [
                    'id_perjadin' => $perjalanan->id,
                    'id_user' => $pegawai['nip'],
                ];
                
                app(NotificationController::class)->sendFromTemplate(
                    'penugasan_perjalanan',
                    [$pegawai['nip']],
                    [
                        'lokasi' => $perjalanan->tujuan,
                        'tanggal' => Carbon::parse($perjalanan->tgl_mulai)->format('d M Y')
                    ],
                    [
                        'action_url' => '/perjalanan/' . $perjalanan->id
                    ]
                );

                $insertLaporan[] = [
                    'id_perjadin' => $perjalanan->id,
                    'id_user' => $pegawai['nip'],
                    'uraian' => null,
                    'is_final' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

            if (!empty($insertPegawai)) {
                DB::table('pegawaiperjadin')->insert($insertPegawai);
            }
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

        // [UPDATE] Ambil jadwal lengkap untuk logika bentrok frontend
        $rows = DB::table('pegawaiperjadin as pp')
            ->join('perjalanandinas as p', 'pp.id_perjadin', '=', 'p.id')
            ->join('users as u', 'pp.id_user', '=', 'u.nip')
            ->select('u.nip', 'p.id_status', 'p.tgl_mulai', 'p.tgl_selesai', 'p.id as id_perjadin')
            ->orderBy('p.id_status', 'asc')
            ->get();

        $pegawaiStatus = [];
        foreach ($rows as $r) {
            $nip = (string) $r->nip;
            $pegawaiStatus[$nip] = $r->id_status;
        }

        // Variabel baru: allSchedules
        $allSchedules = $rows;

        $perjalanan = PerjalananDinas::findOrFail($id);

        $pegawaiList = DB::table('pegawaiperjadin as pp')
            ->join('users as u', 'pp.id_user', '=', 'u.nip')
            ->where('pp.id_perjadin', $id)
            ->select('u.nip', 'u.nama', 'pp.role_perjadin')
            ->get();

        foreach ($pegawaiList as $p) {
            $laporan = LaporanPerjadin::where('id_perjadin', $id)->where('id_user', $p->nip)->first();
            $buktiMap = [];
            if ($laporan) {
                $buktis = BuktiLaporan::where('id_laporan', $laporan->id)->get();
                foreach ($buktis as $b) {
                    $buktiMap[$b->kategori] = $b; 
                }
            }
            $p->buktiMap = $buktiMap;
        }

        $today = Carbon::today()->toDateString();
        $sedangBerlangsungId = DB::table('statusperjadin')->where('nama_status', 'Sedang Berlangsung')->value('id');

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

        // Tambahkan compact 'allSchedules'
        return view('pic.penugasanTambah', compact(
            'users','pegawaiStatus','perjalanan','pegawaiList','pimpinans','pegawaiActive', 'allSchedules'
        ));
    }

    public function update(Request $request, $id)
    {
        // ... (Kode update tidak berubah)
        $messages = [
            'tgl_mulai.after_or_equal' => 'Tanggal mulai tidak boleh mendahului (sebelum) Tanggal Surat.',
            'tgl_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan Tanggal Mulai.',
            'pegawai.required' => 'Minimal harus ada satu pegawai yang dipilih.',
        ];

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
            'dalam_rangka' => 'required|string',
        ], $messages);

        DB::transaction(function() use ($validated, $id) {
            $perjalanan = PerjalananDinas::findOrFail($id);
            $perjalanan->update([
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'tujuan' => $validated['tujuan'],
                'tgl_mulai' => $validated['tgl_mulai'],
                'tgl_selesai' => $validated['tgl_selesai'],
                'approved_by' => $validated['approved_by'],
                'dalam_rangka' => $validated['dalam_rangka'],
            ]);

            if (request()->hasFile('surat_tugas')) {
                $file = request()->file('surat_tugas');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('surat_tugas', $filename, 'public');
                $perjalanan->surat_tugas = $path;
                $perjalanan->save();
            }

            DB::table('pegawaiperjadin')->where('id_perjadin', $perjalanan->id)->delete();
            
            $insertPegawai = [];
            $insertLaporan = [];
            $now = now();

            foreach ($validated['pegawai'] as $pegawai) {
                $insertPegawai[] = [
                    'id_perjadin' => $perjalanan->id,
                    'id_user' => $pegawai['nip'],
                ];

                app(NotificationController::class)->sendFromTemplate(
                    'penugasan_perjalanan',
                    [$pegawai['nip']],
                    [
                        'lokasi' => $perjalanan->tujuan,
                        'tanggal' => Carbon::parse($perjalanan->tgl_mulai)->format('d M Y')
                    ],
                    [
                        'action_url' => '/perjalanan/' . $perjalanan->id
                    ]
                );

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
        // ... (Kode updateStatus tidak berubah)
        $request->validate([
            'status' => 'required|string',
            'alasan' => 'nullable|string',
        ]);

        $perjalanan = PerjalananDinas::findOrFail($id);

        $statusMap = [
            'Diselesaikan Manual' => 7,
            'Dibatalkan' => 8,
        ];

        if (!isset($statusMap[$request->status])) {
            return response()->json(['message' => 'Status tidak valid'], 422);
        }

        DB::transaction(function() use ($perjalanan, $request, $statusMap) {
            $perjalanan->id_status = $statusMap[$request->status];

            if ($request->status === 'Diselesaikan Manual') {
                $perjalanan->selesaikan_manual = $request->alasan;
                $perjalanan->save();
            } else {
                $perjalanan->save();
            }
        });

        return response()->json(['message' => 'Status berhasil diperbarui']);
    }

    // public function simpanKeuanganManual(Request $request, $id)
    // {
    //     $perjalanan = PerjalananDinas::findOrFail($id);

    //     if ($perjalanan->id_status != 7) {
    //         return back()->with('error', 'Hanya bisa input manual jika status Diselesaikan Manual.');
    //     }

    //     $items = $request->input('items', []);
        
    //     DB::transaction(function() use ($perjalanan, $items) {
    //         foreach ($items as $nip => $categories) {
    //             $laporan = LaporanPerjadin::firstOrCreate(
    //                 ['id_perjadin' => $perjalanan->id, 'id_user' => $nip],
    //                 [
    //                     'uraian' => 'Diselesaikan Manual oleh PIC (Alasan: ' . $perjalanan->selesaikan_manual . ')', 
    //                     'is_final' => 1, 
    //                     'created_at' => now(),
    //                     'updated_at' => now()
    //                 ]
    //             );

    //             foreach ($categories as $kategori => $val) {
    //                 $nominalStr = $val['nominal'] ?? '0';
    //                 $nominal = (int) str_replace(['.', ','], '', $nominalStr);
    //                 $keterangan = $val['text'] ?? null; 

    //                 BuktiLaporan::updateOrCreate(
    //                     [
    //                         'id_laporan' => $laporan->id,
    //                         'kategori'   => $kategori
    //                     ],
    //                     [
    //                         'nominal'    => $nominal,
    //                         'keterangan' => $keterangan
    //                     ]
    //                 );
    //             }
    //         }

    //         $idMenunggu = DB::table('statuslaporan')->where('nama_status', 'Menunggu Verifikasi')->value('id');
    //         if (!$idMenunggu) $idMenunggu = 3; 
            
    //         LaporanKeuangan::updateOrCreate(
    //             ['id_perjadin' => $perjalanan->id],
    //             [
    //                 'id_status' => $idMenunggu,
    //                 'updated_at' => now()
    //             ]
    //         );
    //     });

    //     return back()->with('success', 'Data keuangan manual berhasil disimpan. Laporan masuk ke tahap verifikasi.');
    // }
}
