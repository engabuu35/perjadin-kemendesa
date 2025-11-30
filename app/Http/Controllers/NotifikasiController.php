<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // Ambil notifikasi user yang login
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get();
        
        $unreadCount = auth()->user()->unreadNotifications->count();
        
        return view('dashboard', compact('notifications', 'unreadCount'));
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah dibaca'
        ]);
    }
}