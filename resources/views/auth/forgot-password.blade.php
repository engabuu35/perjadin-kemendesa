<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Lupa Password</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-2xl border-2 border-blue-500 shadow-lg p-8 pt-12 pb-12 w-[400px]">
        <div class="flex flex-col items-center">
            <img src="{{ asset('img/logo_kementerian_desa.png') }}" alt="Logo" class="w-24 h-24 mt-1 mb-5 drop-shadow" />

            @if(session('status'))
                <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
            @endif

            <h2 class="text-lg font-semibold text-gray-800 mb-1">Lupa Password</h2>
            <p class="text-sm text-gray-500 text-center mb-5">Masukkan email Anda untuk menerima link reset password.</p>

            <form method="POST" action="{{ route('password.email') }}" class="w-full">
                @csrf
                <div class="mb-5">
                    <label for="email" class="text-sm text-black font-medium block mb-2">Email</label>
                    <input id="email" name="email" type="email" required autofocus
                        class="w-full text-sm px-4 py-2 rounded-full border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300"
                        placeholder="Masukkan email Anda" value="{{ old('email') }}">
                </div>

                <div class="flex justify-between items-center mt-8 space-x-4">
                    <a href="{{ route('login') }}" class="w-1/2">
                        <button type="button" class="w-full px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-full shadow font-medium">
                            &lt; Kembali
                        </button>
                    </a>

                    <button type="submit" class="w-1/2 inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full shadow text-sm font-medium">
                        Kirim Link Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
