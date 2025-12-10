<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Baru SIPERDIN</title>
    <style>
        /* Menggunakan style yang sama persis dengan reset-password.blade.php kamu */
        body { margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-font-smoothing: antialiased; color: #334155; }
        table { border-collapse: collapse; width: 100%; }
        a { color: #2563eb; text-decoration: none; }
        img { display: block; border: 0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f1f5f9; padding: 40px 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); padding: 32px 40px; text-align: center; }
        .logo-container { background: white; padding: 8px; border-radius: 12px; display: inline-block; margin-bottom: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .logo { width: 64px; height: auto; }
        .header-title { color: #ffffff; font-size: 20px; font-weight: 700; margin: 0; letter-spacing: 0.5px; }
        .header-subtitle { color: #e0f2fe; font-size: 13px; margin-top: 6px; font-weight: 500; }
        .content { padding: 40px; }
        .greeting { font-size: 16px; font-weight: 600; color: #0f172a; margin-bottom: 16px; }
        .text { font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 24px; }
        .info-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .info-item { display: block; margin-bottom: 12px; font-size: 14px; }
        .info-label { font-weight: 600; color: #64748b; display: inline-block; width: 130px; }
        .info-value { color: #0f172a; font-weight: 500; font-family: monospace; font-size: 15px; }
        .btn-container { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background-color: #2563eb; color: #ffffff !important; font-weight: 600; font-size: 16px; padding: 14px 32px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3); transition: all 0.2s; }
        .btn:hover { background-color: #1d4ed8; }
        .footer { background-color: #f1f5f9; text-align: center; padding: 24px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <table role="presentation" class="wrapper">
        <tr>
            <td align="center">
                <table role="presentation" class="container">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            <div class="logo-container">
                                <img src="{{ asset('img/logo_kementerian.png') }}" alt="Logo" class="logo">
                            </div>
                            <h1 class="header-title">Selamat Datang di SIPERDIN</h1>
                            <div class="header-subtitle">Akun Anda Telah Dibuat</div>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td class="content">
                            <p class="greeting">Halo, {{ $name }}</p>
                            <p class="text">
                                Akun kepegawaian Anda telah didaftarkan ke dalam sistem. Berikut adalah kredensial akses Anda. 
                                <strong>Demi keamanan, mohon segera ubah password Anda melalui tombol di bawah ini.</strong>
                            </p>

                            <div class="info-box">
                                <div class="info-item">
                                    <span class="info-label">NIP (Login ID)</span>
                                    <span class="info-value">{{ $nip }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Email</span>
                                    <span class="info-value">{{ $email }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Password</span>
                                    <span class="info-value" style="background: #e2e8f0; padding: 2px 6px; border-radius: 4px;">{{ $tempPassword }}</span>
                                </div>
                            </div>

                            <div class="btn-container">
                                <a href="{{ $url }}" class="btn" target="_blank">Ubah Password Sekarang</a>
                            </div>

                            <p style="font-size: 13px; color: #64748b; text-align: center;">
                                Tautan ini akan kadaluarsa dalam 60 menit.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            &copy; {{ date('Y') }} Kementerian Desa, PDT & Transmigrasi Republik Indonesia.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>