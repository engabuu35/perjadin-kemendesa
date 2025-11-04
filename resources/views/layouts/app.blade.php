<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pencatatan Perjalanan Dinas')</title>

    @vite(['resources/css/app.css'])
</head>
<body class="bg-red-100 flex flex-col min-h-screen">
    @include('partials.navbar')

    <div class="flex flex-1">
        @include('partials.sidebar')

        <main class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    @include('partials.footer')
</body>
</html>
