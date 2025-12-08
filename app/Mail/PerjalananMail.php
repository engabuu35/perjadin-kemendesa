<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PerjalananDinas;
use App\Models\User;
use Carbon\Carbon;

class PerjalananMail extends Mailable
{
    use Queueable, SerializesModels;

    public $perjalanan;
    public $penerimaNama;
    public $logoUrl;
    public $perjalananUrl;
    public $tglMulaiFormatted;
    public $tglSelesaiFormatted;
    public $penetapName;
    public $totalDays;

    public function __construct(
        PerjalananDinas $perjalanan,
        ?string $penerimaNama = null,
        ?string $penetapName = null,
        ?string $logoUrl = null,
        ?string $perjalananUrl = null
    ) {
        $this->perjalanan = $perjalanan;

        // fallback logo & url absolute
        $this->logoUrl = $logoUrl ?? asset('img/logo_kementerian.png');
        $this->perjalananUrl = $perjalananUrl ?? url('/perjalanan/'.$perjalanan->id);

        // penerima nama jika dikirim, atau coba ambil dari relasi/kolom
        $this->penerimaNama = $penerimaNama ?? 'Pegawai';

        // penetap: jika diberikan gunakan, kalau tidak coba ambil relasi user via id_pembuat
        $this->penetapName = $penetapName
            ?? ($perjalanan->pembuat ? $perjalanan->pembuat->nama : null)
            ?? 'Pejabat Pembuat Komitmen';

        // tanggal: aman parse dengan Carbon bila ada
        $this->tglMulaiFormatted = $perjalanan->tgl_mulai
            ? Carbon::parse($perjalanan->tgl_mulai)->format('d M Y')
            : null;
        $this->tglSelesaiFormatted = $perjalanan->tgl_selesai
            ? Carbon::parse($perjalanan->tgl_selesai)->format('d M Y')
            : null;

        // total days: jika kedua tanggal ada, hitung inklusif (mis. 1 s/d 3 = 3 hari)
        if ($perjalanan->tgl_mulai && $perjalanan->tgl_selesai) {
            try {
                $start = Carbon::parse($perjalanan->tgl_mulai);
                $end = Carbon::parse($perjalanan->tgl_selesai);
                $this->totalDays = $end->diffInDays($start) + 1;
            } catch (\Throwable $e) {
                $this->totalDays = null;
            }
        } else {
            $this->totalDays = null;
        }
    }

    public function build()
    {
        return $this->subject('Notifikasi Penugasan Perjalanan Dinas')
                    ->view('emails.perjadin-email')
                    ->with([
                        'logoUrl' => $this->logoUrl,
                        'penerimaNama' => $this->penerimaNama,
                        'perjalanan' => $this->perjalanan,
                        'tglMulaiFormatted' => $this->tglMulaiFormatted,
                        'tglSelesaiFormatted' => $this->tglSelesaiFormatted,
                        'penetapName' => $this->penetapName,
                        // tambahkan totalDays agar bisa dipakai di blade (blade Anda fiks — lihat catatan)
                        'totalDays' => $this->totalDays,
                        'perjalananUrl' => $this->perjalananUrl,
                    ]);
    }
}
