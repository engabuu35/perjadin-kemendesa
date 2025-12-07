<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Reset Password - Siperdin</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo_kementerian.png') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite('resources/css/app.css')
</head>
<body class="relative flex items-center justify-center min-h-screen bg-login-pattern bg-cover bg-center bg-no-repeat">
    <div class="absolute inset-0 bg-gray-200/45"></div>

    <div class="bg-white rounded-2xl border-2 border-blue-500 shadow-lg p-8 pt-12 pb-12 w-[400px] z-10">
        <div class="flex flex-col items-center">
            <img src="{{ asset('img/logo_kementerian_desa.png') }}" alt="Logo" class="w-24 h-24 mt-1 mb-5 drop-shadow" />
            <h2 class="text-lg font-semibold text-gray-800 mb-1">Reset Password</h2>
            <p class="text-sm text-gray-500 text-center mb-5">Gunakan kombinasi huruf dan angka untuk keamanan akun anda.</p>
        </div>

        <!-- Status / Errors -->
        @if(session('status'))
            <div class="mb-4 text-sm text-green-600 text-center">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- token dikirim oleh controller showResetForm -->
            <input type="hidden" name="token" value="{{ $token ?? $request->route('token') }}">

            <!-- NIP (ganti dari email menjadi nip sesuai controller Anda) -->
            <div class="mb-4">
                <label for="nip" class="text-sm font-medium text-black block mb-2">NIP / NIK</label>
                <div class="relative">
                    <i class="fa-solid fa-id-card absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="nip" name="nip" type="text"
                        value="{{ old('nip', $request->nip ?? '') }}"
                        required autofocus
                        class="w-full text-sm pl-11 pr-4 py-2 rounded-full border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>
                @error('nip')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- PASSWORD -->
            <div class="mb-4">
                <label for="password" class="text-sm font-medium text-black block mb-2">Password Baru</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                    <input id="password" name="password" type="password" required
                        class="w-full text-sm pl-11 pr-11 py-2 rounded-full border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300">

                    <!-- Toggle -->
                    <button type="button"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500"
                        onclick="togglePassword('password', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- KONFIRMASI PASSWORD -->
            <div class="mb-4">
                <label for="password_confirmation" class="text-sm font-medium text-black block mb-2">Konfirmasi Password</label>
                <div class="relative">
                    <i class="fa-solid fa-shield-halved absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="w-full text-sm pl-11 pr-11 py-2 rounded-full border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300">

                    <!-- Toggle -->
                    <button type="button"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500"
                        onclick="togglePassword('password_confirmation', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- BUTTON -->
            <div class="flex justify-end mt-4">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full text-sm font-medium flex items-center gap-2">
                    <i class="fa-solid fa-key"></i>
                    Reset Password
                </button>
            </div>
        </form>
    </div>

    <!-- SCRIPT TOGGLE PASSWORD -->
    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>

</body>
</html>
