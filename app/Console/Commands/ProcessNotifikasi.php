<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notifikasi;
use App\Notifications\PerjalananDigestNotification;

class ProcessNotifikasi extends Command
{
    protected $signature = 'notifikasi:process';
    protected $description = 'Kirim pending notifikasi yang sudah available';

    public function handle()
    {
        $pendingGrouped = Notifikasi::where('status','pending')
            ->where('available_at','<=', now())
            ->get()
            ->groupBy('user_id');

        foreach ($pendingGrouped as $userId => $items) {
            $user = \App\Models\User::find($userId);
            if (!$user) continue;

            $summary = $items->pluck('payload')->toArray();

            $user->notify(new PerjalananDigestNotification($summary));

            Notifikasi::whereIn('id', $items->pluck('id'))
                        ->update(['status'=>'sent']);
        }

        $this->info('Pending notifikasi diproses.');
    }
}
