<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pencatatan Perjalanan Dinas')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

    {{-- Navbar atas --}}
    @include('partials.navbar')

    <div class="flex flex-1">
        {{-- Sidebar kiri --}}
        @include('partials.sidebar')

        {{-- Konten utama --}}
        <main class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    {{-- Footer --}}
    @include('partials.footer')

</body>
</html>
