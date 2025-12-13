<!DOCTYPE html>
<html lang="id">

<style>
    @media (max-width: 640px) {
    html {
        font-size: 14px;
    }
    input, button, select, textarea {
        font-size: 0.9rem;
    }
    
}
</style>   

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pencatatan Perjalanan Dinas')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('img/logo_kementerian.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="flex flex-col min-h-screen">
    @include('partials.navbar')

    <div class="flex flex-1">
        @include('partials.sidebarDinamis')

        <main class="flex-1 p-6 overflow-y-auto min-h-screen">
            @yield('content')
        </main>
    </div>

    @include('partials.footer')
    @include('partials.mainBackground')

    <x-floating-button />

    <!-- Tambahkan Modal Konfirmasi Logout yang hilang -->
    @include('partials.logout-modal')

</body>

</html>