<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Reset Password - Suspsys</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
    /* Base */
    body { margin:0; padding:0; background:#f3f6fb; font-family: Poppins, Arial, Helvetica, sans-serif; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
    table { border-collapse:collapse; width:100%; }
    img { border:0; display:block; max-width:100%; }
    a { color:inherit; text-decoration:none; }
    .wrap { width:100%; padding:28px 10px; }
    .card { max-width:640px; margin:0 auto; background:#fff; border-radius:12px; overflow:hidden; border:1px solid #e6eef8; box-shadow:0 8px 30px rgba(17,24,39,0.06); }
    .head { padding:20px 22px; background:linear-gradient(90deg,#1f4aa8,#3a6ad9); color:#fff; text-align:left; display:flex; gap:12px; align-items:center; }
    .logo { width:64px; height:64px; border-radius:10px; background:#fff; padding:6px; display:block; object-fit:contain; }
    .brand { font-size:16px; font-weight:700; margin:0; }
    .sub { font-size:12px; opacity:0.95; margin-top:4px; }
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
    .fallback { font-size:12px; color:#6b7280; text-align:center; margin-top:6px; word-break:break-word; }
    .footer { background:#f3f6fb; padding:14px 18px; text-align:center; color:#9aa4b5; font-size:12px; }
    @media only screen and (max-width:480px){
        .label{display:block;width:100%;margin-bottom:6px;}
        .value{display:block;width:100%;}
        .head{flex-direction:row;gap:10px;}
        .logo{width:56px;height:56px;}
    }
</style>
</head>
<body>
<table role="presentation" class="wrap">
    <tr>
        <td align="center">

            <table role="presentation" class="card" aria-labelledby="title">
                <!-- header -->
                <tr>
                    <td class="head" valign="middle">
                        {{-- Logo: gunakan URL absolut / $logoUrl bila disediakan --}}
                        <img src="{{ url(asset('img/logo_kementerian.png')) }}" alt="Logo">
                        <div>
                            <div class="brand">Sistem Perjalanan Dinas — Suspsys</div>
                            <div class="sub">Notifikasi reset password akun</div>
                        </div>
                    </td>
                </tr>

                <!-- body -->
                <tr>
                    <td class="body">
                        <h2 id="title" style="margin:0 0 12px 0; font-size:20px; color:#0f172a;">Reset Password Anda</h2>

                        <p class="greeting">Yth. {{ $name ?? 'Pengguna' }},</p>

                        <p class="desc">Kami menerima permintaan untuk mereset password akun Anda pada <strong>Suspsys</strong>. Untuk mengatur ulang password silakan gunakan tautan di bawah ini.</p>

                        <div class="panel" role="region" aria-label="Instruksi Reset Password">
                            <div class="meta-row">
                                <span class="label">Akun</span>
                                <span class="value">{{ $email ?? ($notifiable->email ?? '—') }}</span>
                            </div>

                            <div class="meta-row">
                                <span class="label">Tautan berlaku</span>
                                <span class="value">
                                    {{ $expire ?? config('auth.passwords.'.config('auth.defaults.passwords').'.expire') ?? 60 }} menit
                                </span>
                            </div>

                            <div class="meta-row">
                                <span class="label">Keamanan</span>
                                <span class="value">Jika Anda tidak meminta reset password, abaikan pesan ini atau hubungi admin jika ragu.</span>
                            </div>
                        </div>

                        <div class="cta-wrap">
                            <a href="{{ $url }}" class="btn" target="_blank" rel="noopener">Reset Password</a>
                        </div>

                        <p class="fallback">Jika tombol tidak berfungsi, salin & tempel tautan berikut di peramban Anda:<br>
                            <a href="{{ $url }}" style="color:#2457b8;text-decoration:underline;">{{ $url }}</a>
                        </p>
                    </td>
                </tr>

                <!-- footer -->
                <tr>
                    <td class="footer">
                        © {{ date('Y') }} Suspsys — Kementerian Desa, PDT & Transmigrasi RI
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
</body>
</html>
