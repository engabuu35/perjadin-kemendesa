<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Siperdin')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo_kementerian.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body class="flex flex-col min-h-screen">
    @php
    $desktopOnly = request()->is('pic/*');
    @endphp
    @include('partials.navbar')

    <x-desktop-warning :desktopOnly="$desktopOnly" />

    <div class="flex flex-1">
        @include('partials.sidebarPIC')

        <main class="flex-1 p-6 overflow-y-auto {{ $desktopOnly ? 'mt-[52px] lg:mt-0' : '' }}">
            @yield('content')
        </main>
    </div>

    @include('partials.footer')
    @include('partials.mainBackground')
    @include('partials.logout-modal')
    @stack('scripts')
    <x-floating-button />
</body>

</html>