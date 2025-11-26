<?php

namespace App\Http\Controllers;

use App\Models\PerjalananDinas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PerjadinTambahController extends Controller
{
    public function create()
    {
        // Ambil semua pegawai dari users (nip & nama) — jangan select 'id' karena kolom id tidak ada
        $users = User::select('nip','nama')->get();

        /**
         * Ambil status perjalanan yang terkait user.
         * NOTE: join berdasarkan users.nip karena id_user menyimpan NIP (bukan users.id).
         */
        $rows = DB::table('pegawaiperjadin as pp')
            ->join('perjalanandinas as p', 'pp.id_perjadin', '=', 'p.id')
            ->join('users as u', 'pp.id_user', '=', 'u.nip')
            ->select('u.nip', 'p.id_status', 'p.tgl_mulai')
            ->orderBy('p.id_status', 'asc')
            ->get();

        $pegawaiStatus = [];
        foreach ($rows as $r) {
            $nip = (string) $r->nip;
            $status = (int) $r->id_status;

            // jika belum ada mapping untuk nip ini, simpan (karena sudah di-order by tgl_mulai desc)
            if (! array_key_exists($nip, $pegawaiStatus)) {
                $pegawaiStatus[$nip] = $status;
                continue;
            }

            // jika sudah ada dan ditemukan status 2, prioritaskan jadi 2
            if ($status === 2) {
                $pegawaiStatus[$nip] = 2;
            }
        }

        return view('pic.penugasanTambah', compact('users', 'pegawaiStatus'));
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
            'surat_tugas' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        DB::transaction(function() use ($validated) {
            $perjalanan = PerjalananDinas::create([
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'tujuan' => $validated['tujuan'],
                'tgl_mulai' => $validated['tgl_mulai'],
                'tgl_selesai' => $validated['tgl_selesai'],
                'id_pembuat' => Auth::id(),
                'id_status' => 1,
            ]);

            // Buat array untuk insert batch
            $insertData = [];
            foreach ($validated['pegawai'] as $pegawai) {
                // Ambil user berdasarkan NIP — kita tetap menyimpan NIP ke kolom id_user
                $user = User::where('nip', $pegawai['nip'])->first();
                if ($user) {
                    $insertData[] = [
                        'id_perjadin' => $perjalanan->id,
                        'id_user' => $user->nip, // sesuai skema saat ini
                    ];
                }
            }

            if (!empty($insertData)) {
                DB::table('pegawaiperjadin')->insert($insertData);
            }
        });

        return redirect()->route('pic.penugasan')
            ->with('success','Perjalanan Dinas berhasil disimpan.');
    }
    public function edit($id)
    {
        $users = User::select('nip','nama')->get();

        // mapping status pegawai sama seperti di create()
        $rows = DB::table('pegawaiperjadin as pp')
            ->join('perjalanandinas as p', 'pp.id_perjadin', '=', 'p.id')
            ->join('users as u', 'pp.id_user', '=', 'u.nip')
            ->select('u.nip', 'p.id_status', 'p.tgl_mulai')
            ->orderBy('p.id_status', 'asc')
            ->get();

        $pegawaiStatus = [];
        foreach ($rows as $r) {
            $nip = (string) $r->nip;
            $status = (int) $r->id_status;
            if (! array_key_exists($nip, $pegawaiStatus)) {
                $pegawaiStatus[$nip] = $status;
                continue;
            }
            if ($status === 2) {
                $pegawaiStatus[$nip] = 2;
            }
        }

        // ambil perjalanan
        $perjalanan = PerjalananDinas::findOrFail($id);

        // ambil list pegawai untuk perjalanandinas ini (nip + nama)
        $pegawaiList = DB::table('pegawaiperjadin as pp')
            ->join('users as u', 'pp.id_user', '=', 'u.nip')
            ->where('pp.id_perjadin', $id)
            ->select('u.nip', 'u.nama')
            ->get()
            ->map(function($r){ return ['nip'=> $r->nip, 'nama' => $r->nama]; })
            ->toArray();

        return view('pic.penugasanTambah', compact('users','pegawaiStatus','perjalanan','pegawaiList'));
    }

    /**
     * Update perjalanan & pegawai yang terkait.
     */
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
        ]);

        DB::transaction(function() use ($validated, $id) {
            $perjalanan = PerjalananDinas::findOrFail($id);
            $perjalanan->update([
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'tujuan' => $validated['tujuan'],
                'tgl_mulai' => $validated['tgl_mulai'],
                'tgl_selesai' => $validated['tgl_selesai'],
                // jangan ubah id_pembuat di edit
            ]);

            // handle surat_tugas file jika diupload => replace existing
            if (request()->hasFile('surat_tugas')) {
                $file = request()->file('surat_tugas');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('surat_tugas', $filename, 'public');

                // jika ada kolom surat_tugas pada model, simpan path (sesuaikan)
                $perjalanan->surat_tugas = $path;
                $perjalanan->save();
            }

            // reset pegawaiperjadin: hapus lalu insert baru
            DB::table('pegawaiperjadin')->where('id_perjadin', $perjalanan->id)->delete();

            $insertData = [];
            foreach ($validated['pegawai'] as $pegawai) {
                $user = User::where('nip', $pegawai['nip'])->first();
                if ($user) {
                    $insertData[] = [
                        'id_perjadin' => $perjalanan->id,
                        'id_user' => $user->nip,
                    ];
                }
            }
            if (!empty($insertData)) {
                DB::table('pegawaiperjadin')->insert($insertData);
            }
        });

        return redirect()->route('pic.penugasan')
            ->with('success', 'Perjalanan Dinas berhasil diperbarui.');
    }
}
