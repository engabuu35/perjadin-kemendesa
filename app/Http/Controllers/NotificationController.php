<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Auth;  

class NotificationController extends Controller
{
    public function index()
    {
        // Ambil notifikasi user yang login
        $user = Auth::user();
        $notifications = $user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get();
        
        $unreadCount = $user()->unreadNotifications->count();
        
        return view('dashboard', compact('notifications', 'unreadCount'));
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah dibaca'
        ]);
    }
}