<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login</title>
    @vite('resources/css/app.css')
</head>
<body class="relative flex items-center justify-center min-h-screen bg-login-pattern bg-cover bg-center bg-no-repeat">
    <div class="absolute inset-0 bg-gray-200/45"></div>

    <div class="bg-white rounded-2xl border-2 border-blue-500 shadow-lg p-8 pt-12 pb-12 w-[400px] z-10">
        <div class="flex flex-col items-center">
            <img src="{{ asset('img/logo_kementerian_desa.png') }}" alt="Logo" class="w-24 h-24 mt-1 mb-5 drop-shadow" />

            @if(session('status'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 text-sm text-red-600">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="w-full">

                @csrf

                <div class="mb-4">
                    <label for="nip" class="text-sm font-medium text-black block mb-2">NIP/NIK</label>
                    <input id="nip" name="nip" type="text" required autofocus value="{{ old('nip') }}"
                        class="w-full text-sm px-4 py-2 rounded-full border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300"
                        placeholder="Masukkan NIP/NIK">
                </div>

                <div class="mb-3">
                    <label for="password" class="text-sm font-medium text-black block mb-2">Password</label>
                    <input id="password" name="password" type="password" required
                        class="w-full text-sm px-4 py-2 rounded-full border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300"
                        placeholder="Masukkan password">
                </div>

                <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember"
                            class="h-3 w-3 rounded border-gray-300">
                        <label for="remember_me" class="ml-2 text-xs text-gray-600 select-none">Remember me</label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:underline">Lupa Password?</a>
                    @endif
                </div>

                <div class="mt-8 text-center">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full shadow text-sm font-medium">
                        Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
