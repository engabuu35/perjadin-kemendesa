<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Lupa Password - Siperdin</title>
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

            <h2 class="text-lg font-semibold text-gray-800 mb-1">Lupa Password</h2>
            <p class="text-sm text-gray-500 text-center mb-5">Masukkan NIP/NIK Anda<br>sistem akan mengirim link reset ke email yang terdaftar.</p>
            
            @error('nip')
                <p id="nip-error" class=" mb-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if(session('status'))
                <div class="mb-1 text-sm text-center text-green-600" role="status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="w-full" novalidate>
                @csrf

                <div class="mb-5">
                    <label for="nip" class="text-sm text-black font-medium block mb-2">NIP / NIK</label>

                    <div class="relative">
                        <i class="fa-solid fa-id-card absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                        <input id="nip" name="nip" type="text" required autofocus
                            autocomplete="username"
                            aria-label="NIP atau NIK"
                            aria-invalid="{{ $errors->has('nip') ? 'true' : 'false' }}"
                            @if($errors->has('nip')) aria-describedby="nip-error" @endif
                            value="{{ old('nip') }}"
                            class="w-full text-sm pl-11 pr-4 py-2 rounded-full border
                                   {{ $errors->has('nip') ? 'border-red-400' : 'border-blue-200' }}
                                   focus:outline-none focus:ring-2 focus:ring-blue-300"
                            placeholder="Masukkan NIP atau NIK Anda">

                    </div>
                </div>

                <div class="flex justify-between items-center mt-8 space-x-4">
                    <a href="{{ route('login') }}" class="w-1/2">
                        <button type="button" class="w-full px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-full shadow font-medium">
                            &lt; Kembali
                        </button>
                    </a>

                    <button type="submit" class="w-1/2 inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full shadow text-sm font-medium">
                        <i class="fa-solid fa-envelope-circle-check mr-2"></i>
                        Kirim Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
