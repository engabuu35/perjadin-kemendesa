<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::middleware('auth:web')->group(function () {
    Route::prefix('notifications')->name('api.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        
        Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
        
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        
        Route::delete('/{id}', [NotificationController::class, 'delete'])->name('delete');
        
        Route::post('/delete-all', [NotificationController::class, 'deleteAll'])->name('delete-all');
        
        Route::get('/refresh', [NotificationController::class, 'refresh'])->name('refresh');
        
        Route::post('/send', [NotificationController::class, 'sendNotification'])->name('send');
        
        Route::post('/send-by-role', [NotificationController::class, 'sendByRole'])->name('send-by-role');
        
        Route::get('/templates', [NotificationController::class, 'getTemplates'])->name('templates');
        
        Route::get('/statistics', [NotificationController::class, 'statistics'])->name('statistics');
    });
});
