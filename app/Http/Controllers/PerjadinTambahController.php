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
        // Ambil semua pegawai dari users (nip & nama)
        $users = User::select('nip','nama')->get();

        // Ambil status perjalanan dinas tiap pegawai yang sedang "Sedang Berlangsung" (id_status = 2)
        $pegawaiStatus = DB::table('pegawaiperjadin')
            ->join('perjalanandinas', 'pegawaiperjadin.id_perjadin', '=', 'perjalanandinas.id')
            ->select('pegawaiperjadin.id_user', 'perjalanandinas.id_status')
            ->where('perjalanandinas.id_status', 2) // status sedang berlangsung
            ->pluck('id_status','id_user')
            ->toArray();

        return view('pic.penugasan', compact('users', 'pegawaiStatus'));
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
                $user = User::where('nip', $pegawai['nip'])->first();
                if ($user) {
                    $insertData[] = [
                        'id_perjadin' => $perjalanan->id,
                        'id_user' => $user->nip,
                    ];
                }
            }

            // Insert semua sekaligus
            if (!empty($insertData)) {
                DB::table('pegawaiperjadin')->insert($insertData);
            }
        });

        return redirect()->route('pic.penugasan')
            ->with('success','Perjalanan Dinas berhasil disimpan.');
    }
}
