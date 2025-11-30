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
        return ['mail', 'database']; // kirim via email + simpan di database
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Penugasan Perjalanan Dinas')
                    ->line('Anda ditugaskan perjalanan dinas.')
                    ->line('Tujuan: ' . $this->perjalanan->tujuan)
                    ->line('Tanggal: ' . $this->perjalanan->tgl_mulai . ' s/d ' . $this->perjalanan->tgl_selesai)
                    ->action('Lihat Detail', url('/perjalanan/' . $this->perjalanan->id));
    }

    public function toArray($notifiable)
    {
        return [
            'perjalanan_id' => $this->perjalanan->id,
            'title' => 'Penugasan Perjalanan Dinas',
            'message' => 'Anda ditugaskan perjalanan dinas.',
        ];
    }
}
