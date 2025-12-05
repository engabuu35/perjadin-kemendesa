    <!doctype html>
    <html lang="id">
    <head>
    <meta charset="utf-8">
    <title>Penugasan Perjalanan Dinas</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body { margin:0; padding:0; background:#f3f6fb; font-family: Poppins, Arial, Helvetica, sans-serif; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
        table { border-collapse:collapse; }
        img { border:0; display:block; }
        a { color:inherit; text-decoration:none; }
        .wrap { width:100%; padding:28px 10px; }
        .card { max-width:640px; margin:0 auto; background:#fff; border-radius:12px; overflow:hidden; border:1px solid #e6eef8; box-shadow:0 8px 30px rgba(17,24,39,0.06); }
        .head { padding:20px 22px; background:linear-gradient(90deg,#1f4aa8,#3a6ad9); color:#fff; text-align:left; display:flex; gap:12px; align-items:center; }
        .logo { width:64px; height:64px; border-radius:10px; background:#fff; padding:6px; display:block; }
        .brand { font-size:16px; font-weight:700; margin:0; }
        .sub { font-size:12px; opacity:0.9; margin-top:4px; }
        .body { padding:20px 22px; color:#0f172a; }
        .greeting { font-size:15px; margin:0 0 8px 0; }
        .desc { font-size:13px; color:#55607a; margin:0 0 14px 0; }
        .panel { background:#fbfdff; border-left:6px solid #2f63c7; padding:14px; border-radius:8px; margin-bottom:16px; }
        .meta-row { display:block; margin-bottom:10px; }
        .label { font-weight:600; color:#274aa0; display:inline-block; width:140px; vertical-align:top; font-size:13px; }
        .value { display:inline-block; max-width:420px; font-size:13px; color:#0f172a; vertical-align:top; }
        .total { margin-top:6px; font-size:12px; color:#6b7280; }
        .cta-wrap { text-align:center; padding:6px 0 16px 0; }
        .btn { display:inline-block; background:#2457b8; color:#fff; padding:12px 18px; border-radius:10px; font-weight:700; text-decoration:none; box-shadow:0 8px 20px rgba(36,87,184,0.18); }
        .fallback { font-size:12px; color:#6b7280; text-align:center; margin-top:6px; }
        .footer { background:#f3f6fb; padding:14px 18px; text-align:center; color:#9aa4b5; font-size:12px; }
        /* responsive */
        @media only screen and (max-width:480px){
        .label{display:block;width:100%;margin-bottom:6px;}
        .value{display:block;width:100%;}
        .head{flex-direction:row;gap:10px;}
        .logo{width:56px;height:56px;}
        }
    </style>
    </head>
    <body>
    <table role="presentation" width="100%" class="wrap">
        <tr>
        <td align="center">
            <table role="presentation" width="100%" class="card">
            <!-- header -->
            <tr>
                <td class="head" valign="middle">
                <img src="{{ $logoUrl ?? url(asset('img/logo_kementerian.png')) }}" alt="Logo" class="logo">
                <div>
                    <div class="brand">Sistem Perjalanan Dinas — Itjen Kemendesa</div>
                    <div class="sub">Notifikasi penugasan perjalanan dinas</div>
                </div>
                </td>
            </tr>

            <!-- body -->
            <tr>
                <td class="body">
                <p class="greeting">Yth. Bapak/Ibu{{ isset($penerimaNama) && $penerimaNama ? ' ' . $penerimaNama : '' }},</p>
                <p class="desc">Anda mendapatkan penugasan perjalanan dinas — simak ringkasan berikut dan buka detail untuk informasi lebih lengkap.</p>

                <div class="panel" role="region" aria-label="Ringkasan Perjalanan">
                    <div class="meta-row">
                    <span class="label">Tujuan</span>
                    <span class="value">{{ $perjalanan->tujuan ?? '—' }}</span>
                    </div>

                    <div class="meta-row">
                    <span class="label">Tanggal</span>
                    <span class="value">
                        {{ $tglMulaiFormatted ?? ($perjalanan->tgl_mulai ?? '—') }}
                        <strong style="margin:0 8px;">s/d</strong>
                        {{ $tglSelesaiFormatted ?? ($perjalanan->tgl_selesai ?? '—') }}
                    </span>
                    </div>

                    @php
                        use Carbon\Carbon;

                        $mulai = Carbon::parse($perjalanan->tgl_mulai);
                        $selesai = Carbon::parse($perjalanan->tgl_selesai);

                        $totalHari = $mulai->diffInDays($selesai) + 1; // +1 jika hari mulai dihitung
                    @endphp

                    <div class="meta-row">
                        <span class="label">Total Hari</span>
                        <span class="value">{{ $totalHari }} hari</span>
                    </div>
                        
                    </span>
                    </div>

                    <div class="meta-row">
                    <span class="label">Ditetapkan oleh</span>
                    <span class="value">{{ $penetapName ?? ($perjalanan->id_pembuat ?? '—') }}</span>
                    </div>

                </div>

                <div class="cta-wrap">
                    <a href="{{ $perjalananUrl ?? url('/perjalanan/'.$perjalanan->id) }}" class="btn" target="_blank" rel="noopener">Lihat Detail Perjalanan</a>
                </div>

                <p class="fallback">Jika tombol tidak berfungsi, salin link berikut:<br>
                    <a href="{{ $perjalananUrl ?? url('/perjalanan/'.$perjalanan->id) }}" style="color:#2457b8;text-decoration:underline;">{{ $perjalananUrl ?? url('/perjalanan/'.$perjalanan->id) }}</a>
                </p>
                </td>
            </tr>

            <!-- footer -->
            <tr>
                <td class="footer">
                © {{ date('Y') }} Kementerian Desa, PDT & Transmigrasi Republik Indonesia
                </td>
            </tr>
            </table>
        </td>
        </tr>
    </table>
    </body>
    </html>
