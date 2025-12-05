<?php

namespace App\Notifications;

use App\Models\Notifikasi;

class LaporanNotification
{
    private $user;
    private $laporan;
    private $type;

    public function __construct($user, $laporan, $type = 'reminder')
    {
        $this->user = $user;
        $this->laporan = $laporan;
        $this->type = $type;
    }

    public function send()
    {
        $configs = [
            'reminder_1' => [
                'title' => 'Pengingat Laporan (H+1)',
                'message' => "Perjalanan dinas Anda telah berakhir. Silakan segera lengkapi laporan uraian hasil untuk {$this->laporan->nomor}",
                'icon' => '📋',
                'color' => 'blue',
                'type' => 'report_reminder',
            ],
            'reminder_3' => [
                'title' => 'Pengingat Laporan (H+3)',
                'message' => "Batas waktu pengiriman laporan akan segera berakhir untuk perjalanan dinas {$this->laporan->nomor}",
                'icon' => '📋',
                'color' => 'red',
                'type' => 'report_reminder',
            ],
            'confirmed' => [
                'title' => 'Laporan Berhasil Dikirim',
                'message' => "Laporan perjalanan dinas {$this->laporan->nomor} telah berhasil dikirim. Status: SELESAI",
                'icon' => '✅',
                'color' => 'green',
                'type' => 'report_confirmed',
            ],
        ];

        $config = $configs[$this->type] ?? $configs['reminder_1'];

        return Notifikasi::create([
            'user_id' => $this->user->nip,
            'type' => $config['type'],
            'title' => $config['title'],
            'message' => $config['message'],
            'icon' => $config['icon'],
            'color' => $config['color'],
            'action_url' => "/laporan-dinas/{$this->laporan->id}",
            'data' => $this->laporan->toArray(),
        ]);
    }
}
