<?php

namespace App\Notifications;

use App\Models\Notifikasi;

class VerifikasiNotification
{
    private $user;
    private $laporan;
    private $verifier;
    private $status;

    public function __construct($user, $laporan, $verifier = 'ppk', $status = 'approved')
    {
        $this->user = $user;
        $this->laporan = $laporan;
        $this->verifier = $verifier;
        $this->status = $status;
    }

    public function send()
    {
        $isApproved = $this->status === 'approved';
        
        if ($this->verifier === 'ppk') {
            $title = $isApproved ? 'Laporan Diverifikasi PPK' : 'Laporan Dikembalikan PPK';
            $message = $isApproved 
                ? "Laporan nominatif Anda telah diverifikasi oleh PPK"
                : "Laporan Anda dikembalikan oleh PPK. Silakan lakukan revisi.";
            $type = 'ppk_verification';
        } else {
            $title = $isApproved ? 'BKU Terverifikasi' : 'BKU Ditolak';
            $message = $isApproved 
                ? "Bukti Kas Umum (BKU) Anda telah diverifikasi dan diterima"
                : "Bukti Kas Umum (BKU) Anda ditolak. Silakan perbaiki.";
            $type = 'bku_verification';
        }

        return Notifikasi::create([
            'user_id' => $this->user->nip,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $isApproved ? '✓' : '⚠️',
            'color' => $isApproved ? 'green' : 'red',
            'action_url' => "/laporan-dinas/{$this->laporan->id}",
            'data' => array_merge($this->laporan->toArray(), ['status' => $this->status]),
        ]);
    }
}
