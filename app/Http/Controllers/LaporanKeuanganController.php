<?php

namespace App\Http\Controllers;

use App\Models\LaporanKeuangan;
use App\Models\RincianAnggaran;
use App\Models\StatusLaporan; // Asumsi model ini ada
use App\Exports\LaporanKeuanganExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LaporanKeuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Start query with eager loading for related data
        $query = LaporanKeuangan::with([
            'perjalananDinas:id,nomor_surat,tujuan,tgl_mulai,tgl_selesai',
            'status:id,nama_status',
            'verifier:nip,nama'
        ]);

        // Add filtering based on request parameters (example)
        if ($request->has('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('nama_status', $request->status);
            });
        }

        // Add search (example)
        if ($request->has('search')) {
            $query->whereHas('perjalananDinas', function ($q) use ($request) {
                $q->where('nomor_surat', 'like', '%' . $request->search . '%')
                  ->orWhere('tujuan', 'like', '%' . $request->search . '%');
            });
        }

        $laporan = $query->paginate(15)->withQueryString();

        return view('laporan.index', ['laporan' => $laporan]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $laporan = LaporanKeuangan::with([
            'perjalananDinas.pembuat:nip,nama',
            'perjalananDinas.pegawai:nip,nama',
            'status',
            'verifier:nip,nama',
            'rincianAnggaran.kategori'
        ])->findOrFail($id);

        return view('laporan.show', ['laporan' => $laporan]);
    }

    /**
     * Show the form for editing the specified resource.
     * Users typically don't 'create' a report, they 'edit' the one
     * automatically created with PerjalananDinas.
     */
    public function edit(string $id)
    {
        $laporan = LaporanKeuangan::with('rincianAnggaran.kategori')
            ->findOrFail($id);

        // Add logic to check authorization (e.g., only assigned person can edit)
        
        return view('laporan.edit', ['laporan' => $laporan]);
    }

    /**
     * Update the specified resource in storage.
     * This handles saving financial details and line items (rincian).
     */
    public function update(Request $request, string $id)
    {
        $laporan = LaporanKeuangan::findOrFail($id);

        // --- Authorization Check (Example) ---
        // if (Auth::user()->nip !== $laporan->perjalananDinas->... ) {
        //     abort(403, 'Unauthorized action.');
        // }

        $validated = $request->validate([
            'nomor_spm' => 'nullable|string|max:100',
            'tanggal_spm' => 'nullable|date',
            'nomor_sp2d' => 'nullable|string|max:100',
            'tanggal_sp2d' => 'nullable|date',
            'rincian' => 'nullable|array',
            'rincian.*.id_kategori' => 'required_with:rincian|integer|exists:kategoribiaya,id',
            'rincian.*.tanggal_biaya' => 'required_with:rincian|date',
            'rincian.*.deskripsi_biaya' => 'nullable|string|max:255',
            'rincian.*.jumlah_biaya' => 'required_with:rincian|numeric|min:0',
            'rincian.*.path_bukti' => 'nullable|string', // Handle file upload separately
        ]);

        // Use a transaction to ensure data integrity
        DB::beginTransaction();
        try {
            // Update parent report details
            $laporan->update($request->only([
                'nomor_spm', 'tanggal_spm', 'nomor_sp2d', 'tanggal_sp2d'
            ]));

            // Delete existing line items and add new ones
            $laporan->rincianAnggaran()->delete();

            $totalBiaya = 0;
            if (!empty($validated['rincian'])) {
                $rincianToInsert = [];
                foreach ($validated['rincian'] as $rincian) {
                    $rincian['id_laporan'] = $laporan->id; // Ensure foreign key is set
                    $rincianToInsert[] = $rincian;
                    $totalBiaya += $rincian['jumlah_biaya'];
                }
                RincianAnggaran::insert($rincianToInsert);
            }

            // Update the total cost (biaya_rampung)
            $laporan->biaya_rampung = $totalBiaya;
            
            // Update status if needed (e.g., set to 'Diajukan')
            // $statusDiajukan = StatusLaporan::where('nama_status', 'Diajukan')->first();
            // if ($statusDiajukan) {
            //     $laporan->id_status = $statusDiajukan->id;
            // }
            
            $laporan->save();

            DB::commit();

            return redirect()->route('laporan.show', $laporan->id)
                ->with('success', 'Laporan keuangan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error: \Log::error($e->getMessage());
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * Custom action to verify a financial report.
     */
    public function verify(Request $request, string $id)
    {
        // --- Authorization Check (Example) ---
        // $this->authorize('verify-laporan-keuangan'); // Using a Policy
        
        $laporan = LaporanKeuangan::findOrFail($id);

        $validated = $request->validate([
            'catatan_verifikasi' => 'nullable|string',
            'aksi' => 'required|in:setujui,tolak',
        ]);

        // Find status IDs from DB
        $statusDisetujui = StatusLaporan::where('nama_status', 'Disetujui')->firstOrFail();
        $statusDitolak = StatusLaporan::where('nama_status', 'Ditolak')->firstOrFail();

        if ($validated['aksi'] === 'setujui') {
            $laporan->id_status = $statusDisetujui->id;
            $laporan->verified_by = Auth::user()->nip; // Get NIP from logged-in user
            $laporan->verified_at = now();
            // $laporan->catatan = $validated['catatan_verifikasi'] ?? 'Disetujui.';
        } else {
            $laporan->id_status = $statusDitolak->id;
            $laporan->verified_by = Auth::user()->nip;
            $laporan->verified_at = now();
            // $laporan->catatan = $validated['catatan_verifikasi'] ?? 'Ditolak.';
        }
        
        $laporan->save();

        return redirect()->route('laporan.show', $laporan->id)
            ->with('success', 'Laporan keuangan telah di-' . $validated['aksi'] . '.');
    }


    /**
     * Generate and download the Excel report.
     */
    public function generateExcel()
    {
        // Pass request filters to the export class if needed
        $filters = request()->only(['status', 'search']);
        
        return Excel::download(
            new LaporanKeuanganExport($filters), 
            'laporan-keuangan-' . date('Y-m-d') . '.xlsx'
        );
    }
}