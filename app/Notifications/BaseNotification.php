<?php

namespace App\Notifications;

use App\Models\Notifikasi;

class BaseNotification
{
    protected $type;
    protected $title;
    protected $message;
    protected $data;
    protected $icon;
    protected $color = 'blue';
    protected $actionUrl;

    public static function create($user, $type, $title, $message, $data = [], $icon = null, $color = 'blue', $actionUrl = null)
    {
        return Notifikasi::create([
            'user_id' => $user->nip,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'icon' => $icon,
            'color' => $color,
            'action_url' => $actionUrl,
        ]);
    }
}
