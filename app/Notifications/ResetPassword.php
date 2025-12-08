<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email
        ], false));

        return (new MailMessage)
            ->subject('Reset Password Akun SIPERDIN')
            ->view('emails.reset-password', [
                'name' => $notifiable->nama ?? 'Pengguna',
                'url'  => $url,
                'email' => $notifiable->email 
            ]);
    }
}
