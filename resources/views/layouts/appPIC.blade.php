<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pencatatan Perjalanan Dinas')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class= "flex flex-col min-h-screen">
    @include('partials.navbar')

    <div class="flex flex-1">
        @include('partials.sidebarPIC')

        <main class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    @include('partials.footer')
    @include('partials.mainBackground')
    @stack('scripts')
</body>
</html>
