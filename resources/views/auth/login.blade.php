<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login - Siperdin</title>
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
            <p class="text-center text-xl text-[#2954B0] font-bold">Inspektorat Jenderal</p>
            <p class="text-center text-sm text-gray-700 font-medium mb-5">
                Kementerian Desa dan Pembangunan Daerah Tertinggal Republik Indonesia
            </p>

            @if(session('status'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            @error('nip')
                <p class="text-sm text-red-600 mb-2">{{ $message }}</p>
            @enderror
        </div>

        <form method="POST" action="{{ route('login.attempt') }}" class="w-full">
            @csrf

            <!-- NIP/NIK -->
            <div class="mb-4">
                <label for="nip" class="text-sm font-medium text-black block mb-2">NIP/NIK</label>

                <div class="relative">
                    <i class="fa-solid fa-id-card absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                    <input id="nip" name="nip" type="text"
                        value="{{ old('nip') }}"
                        required autofocus
                        autocomplete="username"
                        aria-invalid="{{ $errors->has('nip') ? 'true' : 'false' }}"
                        class="w-full text-sm pl-11 pr-4 py-2 rounded-full border 
                               {{ $errors->has('nip') ? 'border-red-400' : 'border-blue-200' }}
                               focus:outline-none focus:ring-2 focus:ring-blue-300"
                        placeholder="Masukkan NIP/NIK">
                </div>
            </div>

            <!-- PASSWORD -->
            <div class="mb-4">
                <label for="password" class="text-sm font-medium text-black block mb-2">Password</label>

                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                    <input id="password" name="password" type="password"
                        required
                        autocomplete="current-password"
                        aria-label="Password"
                        aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                        class="w-full text-sm pl-11 pr-11 py-2 rounded-full border
                               {{ $errors->has('password') ? 'border-red-400' : 'border-blue-200' }}
                               focus:outline-none focus:ring-2 focus:ring-blue-300"
                        placeholder="Masukkan password">

                    <!-- Toggle -->
                    <button type="button"
                        aria-label="Toggle password visibility"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-600"
                        onclick="togglePassword()">
                        <i id="toggleIcon" class="fa-solid fa-eye"></i>
                    </button>
                </div>

                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember + Forgot Password -->
            <div class="flex items-center justify-between mt-4">
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="h-3 w-3 rounded border-gray-300">
                    <label for="remember_me" class="ml-2 text-xs text-gray-600 select-none">
                        Remember me
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:underline">
                        Lupa Password?
                    </a>
                @endif
            </div>

            <!-- SUBMIT -->
            <div class="mt-8 text-center">
                <button type="submit"
                    class="w-full inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full shadow text-sm font-medium">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i>
                    Masuk
                </button>
            </div>
        </form>
    </div>

    <!-- SCRIPT TOGGLE PASSWORD -->
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');

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
