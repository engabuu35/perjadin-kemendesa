<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\PerjalananDinas;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Scheduler untuk update status PerjalananDinas otomatis
        $schedule->call(function () {
            // Ambil id status aktif yang perlu dicek
            $activeStatusIds = DB::table('statusperjadin')
                ->whereIn('nama_status', [
                    'Belum Berlangsung',
                    'Sedang Berlangsung',
                    'Pembuatan Laporan',
                    'Menunggu Validasi PPK',
                    'Perlu Tindakan'
                ])->pluck('id')->toArray();

            // Ambil semua perjalanan aktif
            $perjalananAktif = PerjalananDinas::whereIn('id_status', $activeStatusIds)->get();

            foreach ($perjalananAktif as $perjalanan) {
                // Panggil helper di model untuk update status
                $perjalanan->updateStatus();
            }
        })
        ->everyFiveMinutes() // interval pengecekan setiap 5 menit
        ->name('update_perjadin_status')
        ->withoutOverlapping(); // hindari job tumpang tindih
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
