<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class PasswordResetController extends Controller
{
    // Form Lupa Password (masukkan NIP)
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Kirim link reset password berdasarkan NIP
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|exists:users,nip',
        ], [
            'nip.required' => 'NIP/NIK harus diisi.',
            'nip.exists' => 'NIP/NIK tidak terdaftar.',
        ]);

        $user = User::where('nip', $request->nip)->first();

        if (! $user) {
            return back()->withErrors(['nip' => 'Pengguna tidak ditemukan.']);
        }

        // Kirim reset link ke email user
        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Link reset password telah dikirim ke email: ' . $this->maskEmail($user->email));
        }

        return back()->withErrors(['nip' => 'Gagal mengirim link reset. Silakan coba lagi.']);
    }

    // Tampilkan form reset
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'request' => $request,
        ]);
    }

    // Proses reset password
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'nip' => 'required|string|exists:users,nip',
            'password' => 'required|string|min:8|confirmed',
        ], [
            // Pesan custom untuk rules
            'token.required' => 'Token reset diperlukan.',
            'nip.required' => 'NIP/NIK harus diisi.',
            'nip.exists' => 'NIP/NIK tidak terdaftar.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal harus memiliki 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $userModel = User::where('nip', $request->nip)->first();

        if (! $userModel) {
            return back()->withErrors(['nip' => 'Pengguna tidak ditemukan.']);
        }

        // Jalankan Reset
        $status = Password::reset(
            [
                'email' => $userModel->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token,
            ],
            function ($user, $password) {

                // Perubahan paling penting -> password_hash
                $user->forceFill([
                    'password_hash' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Password berhasil diubah. Silakan login.');
        }

        return back()->withErrors(['nip' => 'Gagal mereset password. Token mungkin tidak valid atau sudah kadaluarsa.']);
    }

    // Optional: masking email
    protected function maskEmail($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        [$local, $domain] = explode('@', $email);
        $maskedLocal = strlen($local) <= 2 
            ? substr($local, 0, 1) . '*' 
            : substr($local, 0, 1) . str_repeat('*', max(1, strlen($local) - 2)) . substr($local, -1);

        return $maskedLocal . '@' . $domain;
    }
}
