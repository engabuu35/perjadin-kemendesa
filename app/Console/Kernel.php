<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // cek transisi tgl_mulai (Belum -> Sedang) sering tapi ringan
        $schedule->command('perjadin:update-status --only-start --chunk=300')
                    ->everyFiveMinutes()
                    ->name('perjadin:update-starts')
                    ->withoutOverlapping();

        // jalankan full check sekali sehari (cek semua aturan transisi)
        $schedule->command('perjadin:update-status --chunk=500')
                    ->dailyAt('00:05')
                    ->name('perjadin:update-full')
                    ->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
