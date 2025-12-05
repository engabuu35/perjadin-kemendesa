<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    // Notification Types
    const TYPE_PENUGASAN = 'penugasan';
    const TYPE_REMINDER_GEOTAGGING = 'reminder_geotagging';
    const TYPE_REMINDER_LAPORAN = 'reminder_laporan';
    const TYPE_PERINGATAN_LAPORAN = 'peringatan_laporan';
    const TYPE_KONFIRMASI_LAPORAN = 'konfirmasi_laporan';
    const TYPE_STATUS_VERIFIKASI_PPK = 'status_verifikasi_ppk';
    const TYPE_RINGKASAN_BULANAN = 'ringkasan_bulanan';
    const TYPE_LAPORAN_MASUK = 'laporan_masuk';
    const TYPE_REMINDER_NOMINATIF = 'reminder_nominatif';
    const TYPE_LAPORAN_DIKEMBALIKAN = 'laporan_dikembalikan';
    const TYPE_LAPORAN_DISETUJUI = 'laporan_disetujui';
    const TYPE_KONFIRMASI_APPROVAL = 'konfirmasi_approval';
    const TYPE_KONFIRMASI_PENGEMBALIAN = 'konfirmasi_pengembalian';
    const TYPE_REKAPITULASI_SIAP = 'rekapitulasi_siap';
    const TYPE_MAINTENANCE = 'maintenance';
    const TYPE_PEMBARUAN_SISTEM = 'pembaruan_sistem';
    const TYPE_LOGIN_BARU = 'login_baru';
    const TYPE_PERUBAHAN_PASSWORD = 'perubahan_password';
    const TYPE_LAPORAN_SELESAI_PEGAWAI = 'laporan_selesai_pegawai';

    public function __construct()
    {
        // No service needed
    }

    private function getNotificationsQuery()
    {
        $user = auth()->user();
        $nip = $user->nip;

        return DB::table('notifications')
            ->where('user_id', $nip)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc');
    }

    public function index(Request $request)
    {
        try {
            $type = $request->query('type');

            $query = $this->getNotificationsQuery();
            
            if ($type) {
                $query->where('type', $type);
            }

            $notifications = $query->get()->map(function ($notification) {
                return $this->formatNotification($notification);
            });

            $unreadCount = $this->getNotificationsQuery()->whereNull('read_at')->count();

            return response()->json([
                'success' => true,
                'data' => $notifications->values(),
                'unread_count' => $unreadCount,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
                'unread_count' => 0,
            ], 500);
        }
    }

    public function unread()
    {
        $notifications = $this->getNotificationsQuery()
            ->whereNull('read_at')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return $this->formatNotification($notification);
            });

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'count' => $notifications->count(),
        ]);
    }

    public function markAsRead($id)
    {
        $user = auth()->user();
        
        DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', $user->nip)
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi telah ditandai sebagai dibaca',
        ]);
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        
        DB::table('notifications')
            ->where('user_id', $user->nip)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah ditandai sebagai dibaca',
        ]);
    }

    public function delete($id)
    {
        $user = auth()->user();
        
        DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', $user->nip)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi dihapus',
        ]);
    }

    public function deleteAll()
    {
        $user = auth()->user();
        
        DB::table('notifications')
            ->where('user_id', $user->nip)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi dihapus',
        ]);
    }

    public function unreadCount()
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not authenticated',
                    'count' => 0,
                ], 401);
            }

            $count = DB::table('notifications')
                ->where('user_id', $user->nip)
                ->whereNull('read_at')
                ->whereNull('deleted_at')
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching unread count: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'count' => 0,
            ], 500);
        }
    }

    public function refresh()
    {
        $notifications = $this->getNotificationsQuery()
            ->limit(20)
            ->get()
            ->map(function ($notification) {
                return $this->formatNotification($notification);
            });

        $unreadCount = $this->getNotificationsQuery()->whereNull('read_at')->count();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    private function formatNotification($notification)
    {
        $createdAt = $notification->created_at;
        if (is_string($createdAt)) {
            $createdAt = \Carbon\Carbon::parse($createdAt);
        }

        // Data tambahan dari kolom JSON jika ada
        $data = [];
        if (!empty($notification->data)) {
            if (is_string($notification->data)) {
                $decoded = json_decode($notification->data, true);
                $data = is_array($decoded) ? $decoded : [];
            } elseif (is_array($notification->data)) {
                $data = $notification->data;
            }
        }

        return [
            'id' => $notification->id,
            'type' => $notification->type ?? 'system',
            'title' => $notification->title ?? 'Notifikasi',
            'message' => $notification->message ?? '',
            'icon' => $notification->icon ?? 'bell',
            'color' => $notification->color ?? 'blue',
            'action_url' => $notification->action_url ?? null,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
            'time_ago' => $this->getTimeAgo($createdAt),
            'is_read' => !is_null($notification->read_at),
            'data' => $data,
        ];
    }

    private function getTimeAgo($datetime)
    {
        $now = now();
        $diff = $now->diff($datetime);

        if ($diff->y > 0) return $diff->y . ' tahun lalu';
        if ($diff->m > 0) return $diff->m . ' bulan lalu';
        if ($diff->d > 0) return $diff->d . ' hari lalu';
        if ($diff->h > 0) return $diff->h . ' jam lalu';
        if ($diff->i > 0) return $diff->i . ' menit lalu';
        return 'Baru saja';
    }

    public function send(array $userIds, string $type, string $title, string $message, array $data = [], array $options = [])
    {
        $notifications = [];

        $iconMap = [
            self::TYPE_PENUGASAN => '<i class="fas fa-plane"></i>',
            self::TYPE_REMINDER_GEOTAGGING => '<i class="fas fa-map-marker-alt"></i>',
            self::TYPE_REMINDER_LAPORAN => '<i class="fas fa-file-alt"></i>',
            self::TYPE_PERINGATAN_LAPORAN => '<i class="fas fa-exclamation-triangle"></i>',
            self::TYPE_KONFIRMASI_LAPORAN => '<i class="fas fa-check-circle"></i>',
            self::TYPE_STATUS_VERIFIKASI_PPK => '<i class="fas fa-check-circle"></i>',
            self::TYPE_RINGKASAN_BULANAN => '<i class="fas fa-chart-bar"></i>',
            self::TYPE_LAPORAN_MASUK => '<i class="fas fa-inbox"></i>',
            self::TYPE_REMINDER_NOMINATIF => '<i class="fas fa-clipboard-list"></i>',
            self::TYPE_LAPORAN_DIKEMBALIKAN => '<i class="fas fa-undo"></i>',
            self::TYPE_LAPORAN_DISETUJUI => '<i class="fas fa-check-circle"></i>',
            self::TYPE_KONFIRMASI_APPROVAL => '<i class="fas fa-check-circle"></i>',
            self::TYPE_KONFIRMASI_PENGEMBALIAN => '<i class="fas fa-undo"></i>',
            self::TYPE_REKAPITULASI_SIAP => '<i class="fas fa-chart-bar"></i>',
            self::TYPE_MAINTENANCE => '<i class="fas fa-wrench"></i>',
            self::TYPE_PEMBARUAN_SISTEM => '<i class="fas fa-sync-alt"></i>',
            self::TYPE_LOGIN_BARU => '<i class="fas fa-shield-alt"></i>',
            self::TYPE_PERUBAHAN_PASSWORD => '<i class="fas fa-key"></i>',
            self::TYPE_LAPORAN_SELESAI_PEGAWAI => '<i class="fas fa-clipboard-check"></i>',
        ];

        foreach ($userIds as $userId) {
            $notificationId = DB::table('notifications')->insertGetId([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => json_encode($data),
                'icon' => $options['icon'] ?? ($iconMap[$type] ?? '🔔'),
                'color' => $options['color'] ?? 'blue',
                'action_url' => $options['action_url'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $notifications[] = $notificationId;
        }

        return count($notifications) === 1 ? $notifications[0] : $notifications;
    }

    public function sendToRole(string $role, string $type, string $title, string $message, array $data = [], array $options = [])
    {
        $users = User::whereHas('roles', function($q) use ($role) {
            $q->where('kode', strtoupper($role));
        })->pluck('nip')->toArray();

        if (empty($users)) {
            return [];
        }

        return $this->send($users, $type, $title, $message, $data, $options);
    }

    public function sendFromTemplate(string $templateName, array $userIds, array $replacements = [], array $extraOptions = [])
    {
        $templates = $this->getTemplatesArray();

        if (!isset($templates[$templateName])) {
            throw new \InvalidArgumentException("Template '{$templateName}' tidak ditemukan");
        }

        $template = $templates[$templateName];

        $title = $template['title'];
        $message = $template['message'];

        foreach ($replacements as $key => $value) {
            $title = str_replace('{' . $key . '}', $value, $title);
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        $options = array_merge([
            'icon' => $template['icon'],
            'color' => $template['color'],
        ], $extraOptions);

        return $this->send($userIds, $template['type'], $title, $message, $replacements, $options);
    }

    protected function getTemplatesArray(): array
    {
        return [
            // ========== NOTIFIKASI UNTUK PEGAWAI ==========
            'penugasan_perjalanan' => [
                'type' => self::TYPE_PENUGASAN,
                'title' => 'Penugasan Perjalanan Dinas',
                'message' => 'Anda telah ditugaskan untuk perjalanan dinas ke {lokasi} pada tanggal {tanggal}. Silakan cek detail penugasan.',
                'icon' => '<i class="fas fa-plane"></i>',
                'color' => 'blue',
            ],
            'reminder_geotagging' => [
                'type' => self::TYPE_REMINDER_GEOTAGGING,
                'title' => 'Reminder Geotagging',
                'message' => 'Jangan lupa untuk melakukan geotagging selama perjalanan dinas Anda.',
                'icon' => '<i class="fas fa-map-marker-alt"></i>',
                'color' => 'orange',
            ],
            'reminder_geotagging_belum' => [
                'type' => self::TYPE_REMINDER_GEOTAGGING,
                'title' => 'Geotagging Belum Dilakukan',
                'message' => 'Anda belum melakukan geotagging hari ini untuk perjalanan dinas {nomor_st}.',
                'icon' => '<i class="fas fa-map-marker-alt"></i>',
                'color' => 'red',
            ],
            'reminder_laporan_h1' => [
                'type' => self::TYPE_REMINDER_LAPORAN,
                'title' => 'Reminder Laporan Perjalanan Dinas',
                'message' => 'Perjalanan dinas Anda telah berakhir. Silakan segera lengkapi laporan uraian hasil dan unggah file bukti perjalanan.',
                'icon' => '<i class="fas fa-file-alt"></i>',
                'color' => 'yellow',
            ],
            'reminder_laporan_h3' => [
                'type' => self::TYPE_REMINDER_LAPORAN,
                'title' => 'Pengingat: Laporan Belum Dilengkapi',
                'message' => 'Pengingat: Laporan perjalanan dinas {nomor_st} belum dilengkapi. Batas waktu pengiriman: {batas_waktu}.',
                'icon' => '<i class="fas fa-clock"></i>',
                'color' => 'orange',
            ],
            'peringatan_laporan_karakter' => [
                'type' => self::TYPE_PERINGATAN_LAPORAN,
                'title' => 'Peringatan: Laporan Kurang Lengkap',
                'message' => 'Laporan uraian hasil Anda kurang dari 100 karakter. Minimal 100 karakter diperlukan.',
                'icon' => '<i class="fas fa-exclamation-triangle"></i>',
                'color' => 'red',
            ],
            'peringatan_laporan_file' => [
                'type' => self::TYPE_PERINGATAN_LAPORAN,
                'title' => 'Peringatan: File Bukti Belum Diunggah',
                'message' => 'File bukti perjalanan dinas belum diunggah. Silakan lengkapi untuk menyelesaikan pelaporan.',
                'icon' => '<i class="fas fa-paperclip"></i>',
                'color' => 'red',
            ],
            'konfirmasi_laporan_berhasil' => [
                'type' => self::TYPE_KONFIRMASI_LAPORAN,
                'title' => 'Laporan Berhasil Dikirim',
                'message' => 'Laporan perjalanan dinas Anda telah berhasil dikirim. Status: Menunggu verifikasi.',
                'icon' => '<i class="fas fa-check-circle"></i>',
                'color' => 'green',
            ],
            'status_verifikasi_disetujui' => [
                'type' => self::TYPE_STATUS_VERIFIKASI_PPK,
                'title' => 'Laporan Disetujui PPK',
                'message' => 'Laporan perjalanan dinas {nomor_st} telah diverifikasi dan disetujui oleh PPK.',
                'icon' => '<i class="fas fa-check-circle"></i>',
                'color' => 'green',
            ],
            'status_verifikasi_ditolak' => [
                'type' => self::TYPE_STATUS_VERIFIKASI_PPK,
                'title' => 'Laporan Ditolak PPK',
                'message' => 'Laporan perjalanan dinas {nomor_st} ditolak oleh PPK. Alasan: {alasan}.',
                'icon' => '<i class="fas fa-times-circle"></i>',
                'color' => 'red',
            ],
            'ringkasan_bulanan' => [
                'type' => self::TYPE_RINGKASAN_BULANAN,
                'title' => 'Ringkasan Perjalanan Dinas Bulanan',
                'message' => 'Ringkasan perjalanan dinas bulan {bulan}: {jumlah_selesai} selesai, {jumlah_pending} pending.',
                'icon' => '<i class="fas fa-chart-bar"></i>',
                'color' => 'blue',
            ],

            // ========== NOTIFIKASI UNTUK PIMPINAN ==========
            'laporan_masuk_pimpinan' => [
                'type' => self::TYPE_LAPORAN_MASUK,
                'title' => 'Laporan Perjalanan Dinas Masuk',
                'message' => '{nama_pegawai} telah mengirimkan laporan perjalanan dinas {nomor_st}.',
                'icon' => '<i class="fas fa-inbox"></i>',
                'color' => 'blue',
            ],

            // ========== NOTIFIKASI UNTUK STAF PIC ==========
            'laporan_masuk_pic' => [
                'type' => self::TYPE_LAPORAN_MASUK,
                'title' => 'Laporan Pegawai Diterima',
                'message' => 'Laporan perjalanan dinas dari {nama_pegawai} ({nomor_st}) telah masuk dan siap diteruskan ke PPK.',
                'icon' => '<i class="fas fa-inbox"></i>',
                'color' => 'blue',
            ],
            'laporan_selesai_pegawai' => [
                'type' => self::TYPE_LAPORAN_SELESAI_PEGAWAI,
                'title' => 'Laporan Perjalanan Siap Diverifikasi',
                'message' => 'Semua pegawai telah menyelesaikan laporan untuk perjalanan dinas {nomor_st} ke {tujuan}. Silakan verifikasi laporan.',
                'icon' => '<i class="fas fa-clipboard-check"></i>',
                'color' => 'blue',
            ],
            'reminder_nominatif' => [
                'type' => self::TYPE_REMINDER_NOMINATIF,
                'title' => 'Pengingat Nominatif Belum Dikirim',
                'message' => 'Nominatif untuk {nomor_st} belum dikirim ke PPK. Segera proses untuk menghindari keterlambatan.',
                'icon' => '<i class="fas fa-clipboard-list"></i>',
                'color' => 'orange',
            ],
            'laporan_dikembalikan_ppk' => [
                'type' => self::TYPE_LAPORAN_DIKEMBALIKAN,
                'title' => 'Laporan Dikembalikan oleh PPK',
                'message' => 'PPK mengembalikan laporan {nomor_st}. Alasan: {alasan}. Silakan koordinasi dengan pegawai terkait.',
                'icon' => '<i class="fas fa-undo"></i>',
                'color' => 'red',
            ],
            'laporan_disetujui_ppk' => [
                'type' => self::TYPE_LAPORAN_DISETUJUI,
                'title' => 'Laporan Disetujui PPK',
                'message' => 'Laporan {nomor_st} telah disetujui oleh PPK dan siap untuk diproses lebih lanjut.',
                'icon' => '<i class="fas fa-check-circle"></i>',
                'color' => 'green',
            ],

            // ========== NOTIFIKASI UNTUK STAF PPK ==========
            'laporan_masuk_ppk' => [
                'type' => self::TYPE_LAPORAN_MASUK,
                'title' => 'Nominatif Siap Diverifikasi',
                'message' => 'Nominatif dari Staf PIC untuk {nomor_st} telah masuk dan siap untuk diverifikasi.',
                'icon' => '<i class="fas fa-inbox"></i>',
                'color' => 'blue',
            ],
            'konfirmasi_approval_ppk' => [
                'type' => self::TYPE_KONFIRMASI_APPROVAL,
                'title' => 'Konfirmasi Persetujuan',
                'message' => 'Anda telah menyetujui nominatif {nomor_st}. Status telah diperbarui.',
                'icon' => '<i class="fas fa-check-circle"></i>',
                'color' => 'green',
            ],
            'konfirmasi_pengembalian_ppk' => [
                'type' => self::TYPE_KONFIRMASI_PENGEMBALIAN,
                'title' => 'Konfirmasi Pengembalian',
                'message' => 'Anda telah mengembalikan nominatif {nomor_st} dengan alasan: {alasan}.',
                'icon' => '<i class="fas fa-undo"></i>',
                'color' => 'orange',
            ],
            'rekapitulasi_siap' => [
                'type' => self::TYPE_REKAPITULASI_SIAP,
                'title' => 'Rekapitulasi Siap Download',
                'message' => 'Rekapitulasi nominatif periode {periode} telah siap. Silakan unduh dari menu Rekapitulasi.',
                'icon' => '<i class="fas fa-chart-bar"></i>',
                'color' => 'green',
            ],

            // ========== NOTIFIKASI SISTEM ==========
            'maintenance' => [
                'type' => self::TYPE_MAINTENANCE,
                'title' => 'Pemberitahuan Maintenance',
                'message' => 'Sistem akan mengalami maintenance pada {tanggal} pukul {waktu}. Harap simpan pekerjaan Anda.',
                'icon' => '<i class="fas fa-wrench"></i>',
                'color' => 'yellow',
            ],
            'pembaruan_sistem' => [
                'type' => self::TYPE_PEMBARUAN_SISTEM,
                'title' => 'Pembaruan Sistem',
                'message' => 'Sistem telah diperbarui ke versi {versi}. Lihat changelog untuk informasi lebih lanjut.',
                'icon' => '<i class="fas fa-sync-alt"></i>',
                'color' => 'blue',
            ],
            'login_baru' => [
                'type' => self::TYPE_LOGIN_BARU,
                'title' => 'Login dari Perangkat Baru',
                'message' => 'Terdeteksi login dari perangkat baru: {device} di {lokasi}. Jika bukan Anda, segera ubah password.',
                'icon' => '<i class="fas fa-shield-alt"></i>',
                'color' => 'orange',
            ],
            'perubahan_password' => [
                'type' => self::TYPE_PERUBAHAN_PASSWORD,
                'title' => 'Password Berhasil Diubah',
                'message' => 'Password akun Anda telah berhasil diubah pada {tanggal}.',
                'icon' => '<i class="fas fa-key"></i>',
                'color' => 'green',
            ],

            'laporan_selesai' => [
                'type' => self::TYPE_LAPORAN_DISETUJUI,
                'title' => 'Pelaporan Selesai',
                'message' => 'Laporan perjalanan ke {tujuan} tanggal {tanggal} telah selesai diproses dan disetujui PPK.',
                'icon' => '<i class="fas fa-flag-checkered"></i>',
                'color' => 'green',
            ],

            'laporan_disetujui' => [
                'type' => self::TYPE_LAPORAN_DISETUJUI,
                'title' => 'Laporan Disetujui',
                'message' => 'Laporan perjalanan ke {tujuan} tanggal {tanggal} telah disetujui oleh PPK.',
                'icon' => '<i class="fas fa-check-circle"></i>',
                'color' => 'green',
            ],

            'laporan_dikembalikan' => [
                'type' => self::TYPE_LAPORAN_DIKEMBALIKAN,
                'title' => 'Laporan Dikembalikan',
                'message' => 'Laporan perjalanan ke {tujuan} dikembalikan. Alasan: {alasan}',
                'icon' => '<i class="fas fa-undo"></i>',
                'color' => 'orange',
            ],
        ];
    }
}
