<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PerjalananDinas;

class UpdatePerjadinStatus extends Command
{
    protected $signature = 'perjadin:update-status 
                            {--chunk=200 : Number of records per chunk} 
                            {--only-start : Only process transitions based on tgl_mulai (Belum->Sedang)}';

    protected $description = 'Sinkronkan status Perjalanan Dinas (non-final).';

    public function handle()
    {
        $startTime = now();
        $this->info("Start perjadin:update-status at {$startTime->toDateTimeString()}");
        Log::info('[perjadin:update-status] start', ['time' => $startTime->toDateTimeString(), 'only_start' => $this->option('only-start')]);

        $chunk = (int) $this->option('chunk');

        // ambil id status final agar tidak diproses
        $finalStatusIds = DB::table('statusperjadin')
            ->whereIn('nama_status', ['Diselesaikan Manual', 'Dibatalkan'])
            ->pluck('id')
            ->toArray();

        // safety: jika tidak ada status terdaftar, abort
        if (empty($finalStatusIds)) {
            $this->warn('Tidak ditemukan status final di tabel statusperjadin. Abort.');
            Log::warning('[perjadin:update-status] no final statuses found');
            return 0;
        }

        // base query: hanya non-final
        $query = PerjalananDinas::whereNotIn('id_status', $finalStatusIds);

        // Jika ingin hanya update transisi "Belum -> Sedang" berdasarkan tgl_mulai,
        // aktifkan opsi --only-start untuk membatasi pekerjaan (lebih efisien).
        if ($this->option('only-start')) {
            // tgl_mulai <= hari ini
            $query->whereDate('tgl_mulai', '<=', now()->toDateString());
        }

        $totalProcessed = 0;
        $totalChanged = 0;

        $query->chunkById($chunk, function ($models) use (&$totalProcessed, &$totalChanged) {
            foreach ($models as $m) {
                $totalProcessed++;
                try {
                    $changed = $m->updateStatus();
                    if ($changed) {
                        $totalChanged++;
                        Log::info('[perjadin:update-status] updated', ['id' => $m->id, 'new_status' => $m->id_status]);
                    }
                } catch (\Throwable $e) {
                    $this->error("Error id={$m->id} : " . $e->getMessage());
                    Log::error('[perjadin:update-status] exception', ['id' => $m->id ?? null, 'message' => $e->getMessage()]);
                }
            }
        });

        $endTime = now();
        $this->info("Finished. Processed: {$totalProcessed}, Changed: {$totalChanged}. Time: {$endTime->diffForHumans($startTime)}");
        Log::info('[perjadin:update-status] finished', [
            'processed' => $totalProcessed,
            'changed' => $totalChanged,
            'duration' => $endTime->diffInSeconds($startTime),
        ]);

        return 0;
    }
}
