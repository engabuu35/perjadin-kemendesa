<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Konfirmasi Password</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-2xl border-2 border-blue-500 shadow-lg p-8 pt-12 pb-12 w-[400px]">
        <div class="mb-4 text-sm text-gray-600">
            Ini area aman aplikasi. Konfirmasi password sebelum melanjutkan.
        </div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div class="mb-4">
                <label for="password" class="text-sm font-medium text-black block mb-2">Password</label>
                <input id="password" name="password" type="password" required
                    class="w-full text-sm px-4 py-2 rounded-full border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full text-sm font-medium">Confirm</button>
            </div>
        </form>
    </div>
</body>
</html>
