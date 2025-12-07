<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Reset Password -Siperdin</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo_kementerian.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="relative flex items-center justify-center min-h-screen bg-login-pattern bg-cover bg-center bg-no-repeat">
    <div class="absolute inset-0 bg-gray-200/45"></div>

    <div class="bg-white rounded-2xl border-2 border-blue-500 shadow-lg p-8 pt-12 pb-12 w-[400px] z-10">
        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-4">
                <label for="email" class="text-sm font-medium text-black block mb-2">Email</label>
                <input id="email" name="email" type="email" required autofocus value="{{ old('email', $request->email) }}"
                    class="w-full text-sm px-4 py-2 rounded-full border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>

            <div class="mb-4">
                <label for="password" class="text-sm font-medium text-black block mb-2">Password</label>
                <input id="password" name="password" type="password" required
                    class="w-full text-sm px-4 py-2 rounded-full border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="text-sm font-medium text-black block mb-2">Konfirmasi Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="w-full text-sm px-4 py-2 rounded-full border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full text-sm font-medium">Reset Password</button>
            </div>
        </form>
    </div>
</body>
</html>
