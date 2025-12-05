<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\PerjalananDinas;

class PerjalananAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $perjalanan;

    public function __construct(PerjalananDinas $perjalanan)
    {
        $this->perjalanan = $perjalanan;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Penugasan Perjalanan Dinas')
            ->view('emails.perjadin-email', [
                'perjalanan' => $this->perjalanan
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'perjalanan_id' => $this->perjalanan->id,
            'type' => 'penugasan_perjalanan',
            'category' => 'persiapan',
            'title' => 'Penugasan Perjalanan Dinas',
            'message' => 'Anda ditugaskan perjalanan dinas ke ' . $this->perjalanan->kota_tujuan . ' pada tanggal ' . $this->perjalanan->tanggal_berangkat,
            'icon' => 'briefcase',
            'color' => 'blue',
            'priority' => 'high',
            'action_url' => '/perjadin/' . $this->perjalanan->id,
        ];
    }
}
