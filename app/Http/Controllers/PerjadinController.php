<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geotagging;
use App\Models\PerjalananDinas;
use App\Models\LaporanPerjadin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Notifikasi;
use App\Notifications\PerjalananAssignedNotification;
use App\Models\User;
use App\Http\Controllers\NotificationController;

class PerjadinController extends Controller
{
    /**
     * LIST PENUGASAN
     * Tidak ada auto-update status di sini.
     */
    public function index(Request $request)
    {
        $q = $request->query('q');

        $penugasans = PerjalananDinas::when($q, function ($qr) use ($q) {
                $qr->where('nomor_surat', 'like', "%$q%")
                   ->orWhere('tujuan', 'like', "%$q%");
            })
            ->orderBy('tgl_mulai', 'desc')
            ->paginate(10);

        $missingUraian = [];
        $suratMissing  = [];
        $conflicts     = [];

        $allPerjadin = PerjalananDinas::select('id', 'tgl_mulai', 'tgl_selesai')->get();

        foreach ($penugasans as $p) {
            $pegs = DB::table('pegawaiperjadin')
                ->where('id_perjadin', $p->id)
                ->pluck('id_user')
                ->toArray();

            // LOGIKA BARU: Jika status manual (ID 7), anggap uraian lengkap
            if ($p->id_status == 7) {
                $missingUraian[$p->id] = [];
            } else {
                $missingUraian[$p->id] = DB::table('laporan_perjadin')
                    ->where('id_perjadin', $p->id)
                    ->where(function ($q) {
                        $q->whereNull('uraian')
                          ->orWhereRaw("TRIM(IFNULL(uraian,'')) = ''");
                    })
                    ->pluck('id_user')
                    ->toArray();
            }

            $suratMissing[$p->id] = empty($p->surat_tugas);

            $conflictNips = [];
            foreach ($pegs as $nip) {
                $exists = DB::table('pegawaiperjadin as pp')
                    ->join('perjalanandinas as pd', 'pp.id_perjadin', '=', 'pd.id')
                    ->where('pp.id_user', $nip)
                    ->where('pp.id_perjadin', '<>', $p->id)
                    ->where('pd.tgl_mulai', '<=', $p->tgl_selesai)
                    ->where('pd.tgl_selesai', '>=', $p->tgl_mulai)
                    ->exists();

                if ($exists) $conflictNips[] = $nip;
            }

            $conflicts[$p->id] = array_values(array_unique($conflictNips));
        }

        return view('pic.penugasan', compact(
            'penugasans', 'q', 'missingUraian', 'suratMissing', 'conflicts'
        ));
    }

    /**
     * DETAIL PERJALANAN
     * Update status DIIZINKAN sekali saat halaman ini dibuka.
     */
    public function show($id)
    {
        $user    = Auth::user();
        $userNip = $user->nip;

        $perjalanan = PerjalananDinas::findOrFail($id);

        // Refresh status perjadin
        if (method_exists($perjalanan, 'updateStatus')) {
            $perjalanan->updateStatus();
            $perjalanan->refresh();
        }

        // Ambil role utama user (kalau ada)
        $roleKode = DB::table('penugasanperan')
            ->join('roles', 'penugasanperan.role_id', '=', 'roles.id')
            ->where('penugasanperan.user_id', $userNip)
            ->value('roles.kode');

        $roleKode = $roleKode ? strtoupper($roleKode) : null;

        // Cek apakah user adalah anggota tim perjadin (tabel pivot)
        $dataSaya = DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->first();

        $isAnggota  = (bool) $dataSaya;
        $isPembuat  = $perjalanan->id_pembuat === $userNip;
        $isPrivRole = in_array($roleKode, ['PIC', 'PPK', 'PIMPINAN']);

        // Kalau bukan anggota, bukan pembuat, dan bukan role istimewa → dilarang
        if (!$isAnggota && !$isPembuat && !$isPrivRole) {
            abort(403);
        }

        // Flag status tugas saya (kalau user bukan anggota, anggap false)
        $isMyTaskFinished = $dataSaya ? ($dataSaya->is_finished == 1) : false;

        // --- status perjadin (riwayat atau belum) ---
        $idStatusSelesai = DB::table('statusperjadin')
            ->where('nama_status', 'Selesai')
            ->value('id');

        $isRiwayatPerjadin = $perjalanan->id_status == $idStatusSelesai;

        // Flag global untuk view: boleh edit atau tidak
        $canEditUraian   = !$isRiwayatPerjadin && !$isMyTaskFinished;
        $canFinishTask   = !$isRiwayatPerjadin && !$isMyTaskFinished;

        $period = CarbonPeriod::create($perjalanan->tgl_mulai, $perjalanan->tgl_selesai);
        $geotagHistory = [];

        $today = Carbon::today();
        $now   = Carbon::now();

        // Hitung total geotagging hari ini (dengan atau tanpa foto)
        $jumlahGeotagHariIni = Geotagging::where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->whereDate('created_at', $today)
            ->count();

        $sudahAbsenHariIni     = $jumlahGeotagHariIni > 0;
        $sudahMaksAbsenHariIni = $jumlahGeotagHariIni >= 2;

        // Cek apakah ada geotagging hari ini yang belum punya foto
        $geotagTanpaFotoHariIni = Geotagging::where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->whereDate('created_at', $today)
            ->whereNull('foto')
            ->orderBy('created_at', 'desc')
            ->first();

        $adaGeotagTanpaFotoHariIni = (bool) $geotagTanpaFotoHariIni;

        // Ambil geotag terakhir hari ini untuk cek jeda 15 menit
        // Ambil geotag terakhir hari ini YANG SUDAH ADA FOTONYA untuk cek jeda 5 menit
        $lastGeotagDenganFotoHariIni = Geotagging::where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->whereDate('created_at', $today)
            ->whereNotNull('foto') // HANYA yang sudah ada foto
            ->orderBy('created_at', 'desc')
            ->first();

        $bolehGeotagSekarang = true;

        if ($sudahMaksAbsenHariIni) {
            $bolehGeotagSekarang = false;
        } elseif ($adaGeotagTanpaFotoHariIni) {
            $bolehGeotagSekarang = false;
        }

        // ---- Build geotagHistory: 2 slot per day (slot 1 & 2).
        $dayNumber = 1;
        foreach ($period as $date) {
            $tagsForDay = Geotagging::where('id_perjadin', $id)
                ->where('id_user', $userNip)
                ->whereDate('created_at', $date)
                ->orderBy('created_at')
                ->get();

            // ensure we have an indexed collection (0-based)
            // create exactly 2 slots per day
            for ($slot = 1; $slot <= 2; $slot++) {
                $tag = $tagsForDay->get($slot - 1); // null if not exists

                if ($tag) {
                    $photoUrls = [];
                    if (!empty($tag->foto)) {
                        // $photoUrls[] = Storage::disk('public')->url($tag->foto);
                        $photoUrls[] = asset('storage/' . $tag->foto);
                    }

                    $geotagHistory[] = [
                        // gunakan format label H{hari}-{slot} agar blade lama tetap kompatibel
                        'hari_ke'    => "{$dayNumber}-{$slot}",
                        'tanggal'    => Carbon::parse($tag->created_at)->format('d M Y'),
                        'lat_raw'    => $tag->latitude,
                        'long_raw'   => $tag->longitude,
                        'lokasi'     => "Lat: {$tag->latitude}, Long: {$tag->longitude}",
                        'waktu'      => Carbon::parse($tag->created_at)->format('H:i'),
                        'status'     => 'Sudah',
                        'photo_urls' => $photoUrls,
                        'foto_count' => count($photoUrls),
                    ];
                } else {
                    // placeholder card (Belum)
                    $geotagHistory[] = [
                        'hari_ke'    => "{$dayNumber}-{$slot}",
                        'tanggal'    => $date->format('d M Y'),
                        'lat_raw'    => null,
                        'long_raw'   => null,
                        'lokasi'     => '-',
                        'waktu'      => '-',
                        'status'     => 'Belum',
                        'photo_urls' => [],
                        'foto_count' => 0,
                    ];
                }
            }

            $dayNumber++;
        }

        $laporanSaya = LaporanPerjadin::firstOrNew([
            'id_perjadin' => $id,
            'id_user'     => $userNip
        ]);

        $isTodayInPeriod = $today->between($perjalanan->tgl_mulai, $perjalanan->tgl_selesai);
        $bolehFotoSekarang = $adaGeotagTanpaFotoHariIni && $isTodayInPeriod && !$isMyTaskFinished;
        $isLastDay = $today->isSameDay($perjalanan->tgl_selesai);
        $isPastEnd = $today->gt($perjalanan->tgl_selesai);

        $canFinish = ($isLastDay && $sudahAbsenHariIni) || $isPastEnd;

        // ---------- finishMessage: alasan tombol belum aktif ----------
        $finishMessage = null;
        if (!$canFinish) {
            if ($today->lt($perjalanan->tgl_mulai)) {
                $finishMessage = 'Belum bisa diselesaikan: tugas belum dimulai (mulai ' . Carbon::parse($perjalanan->tgl_mulai)->format('d M Y') . ').';
            } elseif ($today->gt($perjalanan->tgl_selesai)) {
                // meskipun lewat tanggal selesai, canFinish akan true karena isPastEnd => jarang sampai sini,
                // tetap sediakan fallback
                $finishMessage = 'Periode tugas telah berakhir pada ' . Carbon::parse($perjalanan->tgl_selesai)->format('d M Y') . '.';
            } elseif (!$isLastDay && !$isPastEnd) {
                $finishMessage = 'Tombol hanya aktif pada hari terakhir tugas atau setelah tugas selesai.';
            } elseif ($isLastDay && !$sudahAbsenHariIni) {
                $finishMessage = 'Hari ini hari terakhir tugas, tetapi Anda belum melakukan geotagging hari ini.';
            } else {
                $finishMessage = 'Belum memenuhi syarat untuk menyelesaikan tugas.';
            }
        }
        // ---------------------------------------------------------------

        // ----- tambahan: status message + badge -----
        if (!$isTodayInPeriod) {
            if ($today->lt($perjalanan->tgl_mulai)) {
                $statusMessage = 'Tugas belum dimulai. Periode: ' . Carbon::parse($perjalanan->tgl_mulai)->format('d M Y') . ' — ' . Carbon::parse($perjalanan->tgl_selesai)->format('d M Y') . '.';
            } else {
                $statusMessage = 'Periode tugas telah berakhir pada ' . Carbon::parse($perjalanan->tgl_selesai)->format('d M Y') . '.';
            }
        } else {
            $statusMessage = null;
        }

        if ($isMyTaskFinished) {
            $statusPegawai = 'Selesai';
            $statusBadgeClass = 'bg-gray-100 text-gray-700 border border-gray-200';
        } elseif ($sudahAbsenHariIni) {
            $statusPegawai = 'Hadir Hari Ini';
            $statusBadgeClass = 'bg-green-100 text-green-700 border border-green-200';
        } elseif ($isTodayInPeriod) {
            $statusPegawai = 'Dalam Perjalanan';
            $statusBadgeClass = 'bg-blue-50 text-blue-700 border border-blue-100';
        } else {
            $statusPegawai = 'Belum Hadir';
            $statusBadgeClass = 'bg-yellow-50 text-yellow-700 border border-yellow-100';
        }
        // --------------------------------------------

        $lastGeotagTimestamp = null;
        if ($lastGeotagDenganFotoHariIni) {
            $lastGeotagTimestamp = Carbon::parse($lastGeotagDenganFotoHariIni->updated_at)
                ->timestamp * 1000; // Convert ke milliseconds
        }

        return view('pages.detailperjadin', compact(
            'perjalanan',
            'dataSaya',
            'isRiwayatPerjadin',
            'canEditUraian',
            'canFinishTask',
            'geotagHistory',
            'sudahAbsenHariIni',
            'sudahMaksAbsenHariIni',
            'bolehGeotagSekarang',
            'bolehFotoSekarang',
            'laporanSaya',
            'isTodayInPeriod',
            'isMyTaskFinished',
            'canFinish',
            'statusMessage',
            'statusPegawai',
            'statusBadgeClass',
            'finishMessage',
            'lastGeotagDenganFotoHariIni', // GANTI dari lastGeotagHariIni
            'adaGeotagTanpaFotoHariIni', // TAMBAHKAN ini
            'lastGeotagTimestamp'
        ));
    }


    /**
     * FINISH TUGAS SAYA
     * Tidak update status langsung — model yang menangani.
     */
    public function selesaikanTugasSaya(Request $request, $id)
    {
        $userNip = Auth::user()->nip;
        $perjalanan = PerjalananDinas::findOrFail($id);
        $today = Carbon::today();

        // 1. Validasi Waktu
        if (!$today->isSameDay($perjalanan->tgl_selesai) && !$today->gt($perjalanan->tgl_selesai)) {
            return back()->with('error', 'Hanya bisa diselesaikan pada atau setelah tanggal selesai.');
        }

        // 2. Validasi Uraian Kegiatan
        $laporan = LaporanPerjadin::where('id_perjadin', $id)->where('id_user', $userNip)->first();
        
        if (!$laporan || empty($laporan->uraian)) {
            return back()->with('error', 'Anda belum mengisi uraian kegiatan. Silakan isi terlebih dahulu.');
        }

        $jumlahKata = str_word_count(strip_tags($laporan->uraian));
        if ($jumlahKata < 100) {
            $kurang = 100 - $jumlahKata;
            return back()->with('error', "Uraian kegiatan belum cukup detail. Minimal 100 kata. Anda menulis {$jumlahKata} kata (kurang {$kurang} kata lagi).");
        }

        // 3. Update status pegawai ini jadi finished
        DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->update(['is_finished' => 1]);


        // 3.5. Update Status Laporan (Tabel Laporan - IS FINAL)
        // PERBAIKAN: Ubah is_final menjadi 1 (True)
        $laporan->update(['is_final' => 1]);

        // 4. Cek apakah SEMUA pegawai dalam tim sudah selesai?
        $totalPegawai = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->count();
        $totalSelesai = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->where('is_finished', 1)->count();

        if ($totalPegawai == $totalSelesai) {
            $statusNext = DB::table('statusperjadin')->where('nama_status', 'Menunggu Verifikasi Laporan')->value('id');
            if ($statusNext) {
                $perjalanan->update(['id_status' => $statusNext]);

                $picUsers = User::whereHas('roles', function($q) {
                    $q->where('kode', 'PIC');
                })->pluck('nip')->toArray();

                if (!empty($picUsers)) {
                    app(NotificationController::class)->sendFromTemplate(
                        'laporan_selesai_pegawai',
                        $picUsers,
                        [
                            'nomor_st' => $perjalanan->nomor_surat ?? '-',
                            'tujuan' => $perjalanan->tujuan ?? '-',
                        ],
                        ['action_url' => '/pic/pelaporan-keuangan/' . $id]
                    );
                }
                // <END CHANGE>

                return back()->with('success', 'Tugas Anda selesai! Laporan diteruskan ke PIC.');
            }
        }
        return back()->with('success', 'Tugas Anda selesai.');
    }

    /**
     * SIMPAN URAIAN PER USER
     */
    public function storeUraian(Request $request, $id)
    {
        $request->validate(['uraian' => 'nullable|string']);

        $nip = Auth::user()->nip;

        $cek = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->where('id_user', $nip)->first();
        if (!$cek) return back()->with('error', 'Anda tidak terdaftar.');
        if ($cek->is_finished) return back()->with('error', 'Sudah selesai, tidak bisa edit.');

        $laporan = LaporanPerjadin::firstOrNew(['id_perjadin' => $id, 'id_user' => $nip]);
        $laporan->uraian = $request->uraian;
        $laporan->save();

        return back()->with('success', 'Uraian tersimpan.');
    }

    /**
     * ABSENSI / GEOTAGGING
     */
    public function tandaiKehadiran(Request $request, $id)
    {
        $request->validate([
            'latitude'  => 'required',
            'longitude' => 'required',
            'id_tipe'   => 'required'
        ]);

        $perjalanan = PerjalananDinas::findOrFail($id);
        $today = Carbon::today();
        $now   = Carbon::now();

        if (!$today->between($perjalanan->tgl_mulai, $perjalanan->tgl_selesai)) {
            return response()->json(['status' => 'error', 'message' => 'Di luar jadwal.'], 403);
        }

        // Tidak boleh geotag lagi jika masih ada geotag hari ini yang belum punya foto
        $geotagTanpaFotoHariIni = Geotagging::where('id_perjadin', $id)
            ->where('id_user', Auth::user()->nip)
            ->whereDate('created_at', $today)
            ->whereNull('foto')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($geotagTanpaFotoHariIni) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Silakan ambil foto terlebih dahulu untuk geotagging sebelumnya sebelum melakukan geotagging lagi.'
            ], 400);
        }

        // Batas total geotag (dengan/ tanpa foto) per hari: 2
        $jumlahGeotagHariIni = Geotagging::where('id_perjadin', $id)
            ->where('id_user', Auth::user()->nip)
            ->whereDate('created_at', $today)
            ->count();

        if ($jumlahGeotagHariIni >= 2) {
            return response()->json(['status' => 'error', 'message' => 'Batas geotagging hari ini sudah 2x.'], 400);
        }

        Geotagging::create([
            'id_perjadin' => $id,
            'id_user'     => Auth::user()->nip,
            'id_tipe'     => $request->id_tipe,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude
        ]);

        return response()->json(['status' => 'success', 'message' => 'Hadir!']);
    }

    /**
     * SIMPAN FOTO GEOTAGGING (MAKS 2 FOTO PER HARI)
     */
    public function storeFotoGeotagging(Request $request, $id)
    {
        $request->validate([
            'image'     => 'required|string', // data URL base64
            'latitude'  => 'required',
            'longitude' => 'required',
        ]);

        $userNip = Auth::user()->nip;
        $perjalanan = PerjalananDinas::findOrFail($id);
        $today = Carbon::today();

        if (!$today->between($perjalanan->tgl_mulai, $perjalanan->tgl_selesai)) {
            return response()->json(['status' => 'error', 'message' => 'Di luar jadwal.'], 403);
        }

        // Pastikan user terdaftar di perjalanan ini dan belum finished
        $cek = DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->first();

        if (!$cek) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak terdaftar pada perjalanan ini.'], 403);
        }

        if ($cek->is_finished ?? false) {
            return response()->json(['status' => 'error', 'message' => 'Tugas Anda sudah selesai, tidak bisa upload foto lagi.'], 400);
        }

        // Cari geotagging hari ini yang belum memiliki foto
        $geotagTanpaFoto = Geotagging::where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->whereDate('created_at', $today)
            ->whereNull('foto')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$geotagTanpaFoto) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda belum melakukan geotagging atau foto untuk geotagging sebelumnya sudah diambil.'
            ], 400);
        }

        $imageData = $request->image;
        if (!preg_match('/^data:image\/jpe?g;base64,/', $imageData)) {
            return response()->json(['status' => 'error', 'message' => 'Format gambar tidak valid (hanya JPG).'], 422);
        }

        $imageData = substr($imageData, strpos($imageData, ',') + 1);
        $binary = base64_decode($imageData);

        if ($binary === false) {
            return response()->json(['status' => 'error', 'message' => 'Data gambar tidak bisa didecode.'], 422);
        }

        $extension = 'jpg';
        $fileName = 'geotag_' . $id . '_' . $userNip . '_' . now()->format('Ymd_His') . '.' . $extension;
        $path = 'geotagging/' . $fileName;

        Storage::disk('public')->put($path, $binary);

        // Update baris geotagging terakhir tanpa foto dengan data foto & koordinat dari kamera
        $geotagTanpaFoto->update([
            // 'latitude'  => $request->latitude,
            // 'longitude' => $request->longitude,
            'foto'      => $path,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Foto geotagging tersimpan.',
            'path'    => $path,
        ]);
    }

    public function assign(Request $request, PerjalananDinas $perjalanan)
    {
        // Ambil user yang ditugaskan
        $user = User::find($request->input('user_id'));

        if ($user) {
            // Simpan data lama perjalanan untuk payload
            $oldData = $perjalanan->only(['tgl_mulai','tgl_selesai','tujuan']);

            // Kirim notifikasi email langsung ke user
            $user->notify(new PerjalananAssignedNotification($perjalanan));
        }

        return back()->with('success', 'Perjalanan berhasil ditugaskan dan notifikasi dikirim.');
    }

}
