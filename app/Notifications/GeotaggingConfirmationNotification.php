<?php

namespace App\Notifications;

use App\Models\Notifikasi;

class GeotaggingConfirmationNotification
{
    private $user;
    private $geotagging;
    private $status;

    public function __construct($user, $geotagging, $status = 'confirmed')
    {
        $this->user = $user;
        $this->geotagging = $geotagging;
        $this->status = $status;
    }

    public function send()
    {
        $isConfirmed = $this->status === 'confirmed';

        return Notifikasi::create([
            'user_id' => $this->user->nip,
            'type' => 'geotagging_confirmation',
            'title' => $isConfirmed ? 'Geotagging Berhasil Dikonfirmasi' : 'Geotagging Perlu Diperbaiki',
            'message' => $isConfirmed 
                ? "Geotagging Anda telah berhasil dikonfirmasi oleh admin"
                : "Geotagging Anda perlu diperbaiki. Silakan lakukan ulang.",
            'icon' => $isConfirmed ? '✅' : '⚠️',
            'color' => $isConfirmed ? 'green' : 'orange',
            'action_url' => "/geotagging/{$this->geotagging->id}",
            'data' => $this->geotagging->toArray(),
        ]);
    }
}
