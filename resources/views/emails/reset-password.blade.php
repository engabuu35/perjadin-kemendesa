<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        /* --- CSS Reset & Base --- */
        body { margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-font-smoothing: antialiased; color: #334155; }
        table { border-collapse: collapse; width: 100%; }
        a { color: #2563eb; text-decoration: none; }
        img { display: block; border: 0; }

        /* --- Layout Class --- */
        .wrapper { width: 100%; table-layout: fixed; background-color: #f1f5f9; padding: 40px 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }

        /* --- Header --- */
        .header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); padding: 32px 40px; text-align: center; }
        .logo-container { background: white; padding: 8px; border-radius: 12px; display: inline-block; margin-bottom: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .logo { width: 64px; height: auto; }
        .header-title { color: #ffffff; font-size: 20px; font-weight: 700; margin: 0; letter-spacing: 0.5px; }
        .header-subtitle { color: #e0f2fe; font-size: 13px; margin-top: 6px; font-weight: 500; }

        /* --- Body Content --- */
        .content { padding: 40px; }
        .greeting { font-size: 16px; font-weight: 600; color: #0f172a; margin-bottom: 16px; }
        .text { font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 24px; }
        
        /* --- Info Box --- */
        .info-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .info-item { display: block; margin-bottom: 12px; font-size: 14px; }
        .info-item:last-child { margin-bottom: 0; }
        .info-label { font-weight: 600; color: #64748b; display: inline-block; width: 130px; }
        .info-value { color: #0f172a; font-weight: 500; }

        /* --- Button --- */
        .btn-container { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background-color: #2563eb; color: #ffffff !important; font-weight: 600; font-size: 16px; padding: 14px 32px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3); transition: all 0.2s; }
        .btn:hover { background-color: #1d4ed8; box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.4); transform: translateY(-1px); }

        /* --- Fallback & Footer --- */
        .fallback { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 32px; padding-top: 20px; border-top: 1px solid #f1f5f9; line-height: 1.5; word-break: break-all; }
        .footer { background-color: #f1f5f9; text-align: center; padding: 24px; font-size: 12px; color: #94a3b8; }

        /* --- Mobile --- */
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; border-radius: 0; }
            .content { padding: 24px; }
            .header { padding: 24px; }
            .info-label { display: block; margin-bottom: 4px; }
            .info-value { display: block; margin-bottom: 12px; }
        }
    </style>
</head>
<body>

    <table role="presentation" class="wrapper">
        <tr>
            <td align="center">
                <!-- Main Card -->
                <table role="presentation" class="container">
                    
                    <!-- Header Section -->
                    <tr>
                        <td class="header">
                            <div class="logo-container">
                                <img src="{{ $logoUrl ?? url(asset('img/logo_kementerian.png')) }}" alt="Logo Kementerian" class="logo">
                            </div>
                            <h1 class="header-title">Permintaan Reset Password</h1>
                            <div class="header-subtitle">Sistem Perjalanan Dinas - Itjen Kemendesa</div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td class="content">
                            
                            <p class="greeting">Yth. Bapak/Ibu{{ isset($name) && $name ? ' ' . $name : '' }},</p>
                            
                            <p class="text">
                                Kami menerima permintaan untuk mereset kata sandi akun Anda. Demi keamanan, silakan verifikasi permintaan ini dengan menekan tombol di bawah.
                            </p>

                            <!-- Detail Box -->
                            <div class="info-box">
                                <div class="info-item">
                                    <span class="info-label">Akun Email</span>
                                    <span class="info-value">{{ $email ?? ($notifiable->email ?? '—') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Batas Waktu</span>
                                    <span class="info-value">{{ $expire ?? config('auth.passwords.'.config('auth.defaults.passwords').'.expire') ?? 60 }} Menit</span>
                                </div>
                                <div class="info-item" style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #cbd5e1;">
                                    <span class="info-label" style="width: auto; color: #ef4444;">⚠️ Perhatian:</span>
                                    <span style="display: block; font-size: 13px; color: #64748b; margin-top: 4px;">
                                        Jika Anda tidak merasa melakukan permintaan ini, mohon abaikan email ini. Akun Anda tetap aman.
                                    </span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="btn-container">
                                <a href="{{ $url }}" class="btn" target="_blank">Reset Password Saya</a>
                            </div>

                            <!-- Fallback Link -->
                            <div class="fallback">
                                <p style="margin-bottom: 8px;">Tombol tidak berfungsi? Salin tautan berikut ke browser Anda:</p>
                                <a href="{{ $url }}" style="color: #3b82f6; text-decoration: underline;">{{ $url }}</a>
                            </div>
                        </td>
                    </tr>

                </table>
                
                <!-- Footer -->
                <div class="footer">
                    &copy; {{ date('Y') }} Kementerian Desa, PDT & Transmigrasi Republik Indonesia.<br>
                    Hak Cipta Dilindungi Undang-Undang.
                </div>
            </td>
        </tr>
    </table>

</body>
</html>