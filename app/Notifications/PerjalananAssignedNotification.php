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
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Penugasan Perjalanan Dinas')
            ->view('emails.perjalanan_tailwind', [
                'perjalanan' => $this->perjalanan
            ]);
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
