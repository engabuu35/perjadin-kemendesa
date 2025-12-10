<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class NewAccountNotification extends Notification
{
    use Queueable;

    public $token;
    public $nip;
    public $passwordSementara;

    public function __construct($token, $nip, $passwordSementara)
    {
        $this->token = $token;
        $this->nip = $nip;
        $this->passwordSementara = $passwordSementara;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Membuat URL reset password yang valid
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));

        return (new MailMessage)
            ->subject('Informasi Akun Baru SIPERDIN')
            ->view('emails.new-account', [
                'name' => $notifiable->nama,
                'email' => $notifiable->email,
                'nip' => $this->nip,
                'tempPassword' => $this->passwordSementara,
                'url' => $url,
            ]);
    }
}