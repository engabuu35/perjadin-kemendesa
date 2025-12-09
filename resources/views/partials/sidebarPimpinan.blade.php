<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar - SIPERDIN (Tailwind)</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <x-style-sidebar />
</head>

<body class="bg-[#e8e9f0]">
    <!-- Overlay -->
    <div class="overlay fixed top-0 left-0 w-full h-full bg-black/50 opacity-0 invisible 
                transition-all duration-300 z-[98] pointer-events-none" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar bg-sidebar fixed left-0 top-[65px] w-[60px] h-[calc(100vh-50px)]
                transition-all duration-300 ease-in-out z-[99] pt-3 rounded-r-[20px]
                shadow-[4px_0_12px_rgba(0,0,0,0.2)] overflow-visible">

        <!-- Menu List -->
        <ul class="sidebar-menu list-none py-3">
            <!-- Menu Beranda -->
            <li class="my-1.5">
                <a href="{{ route('pages.beranda') }}"
                    class="flex items-center py-[10px] px-[18px] text-white no-underline 
                                transition-colors duration-300 gap-[10px] text-base whitespace-nowrap {{ request()->routeIs('pages.beranda') ? 'active' : '' }}">
                    <span class="icon w-[22px] h-[22px] flex items-center justify-center text-base">
                        <i class="fa-solid fa-home"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Beranda</span>
                </a>
            </li>

            {{-- MENU MONITORING --}}
            <li class="my-1.5">
                <a href="{{ route('pimpinan.monitoring') }}" class="flex items-center py-[10px] px-[18px] text-white no-underline
                        transition-colors duration-300 gap-[10px] text-base whitespace-nowrap
                        {{ request()->routeIs('pimpinan.monitoring') ? 'active' : '' }}">
                    <span class="icon w-[22px] h-[22px] flex items-center justify-center text-base">
                        <i class="fa-solid fa-desktop"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Monitoring</span>
                </a>
            </li>

            {{-- MENU RIWAYAT --}}
            <li class="my-1.5">
                <a href="{{ route('pimpinan.riwayat') }}" class="flex items-center py-[10px] px-[18px] text-white no-underline
                        transition-colors duration-300 gap-[10px] text-base whitespace-nowrap
                        {{ request()->routeIs('pimpinan.riwayat') ? 'active' : '' }}">
                    <span class="icon w-[22px] h-[22px] flex items-center justify-center text-base">
                        <i class="fa-solid fa-history"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Riwayat</span>
                </a>
            </li>
        </ul>

        <!-- User Profile Section -->
        <div class="user-profile absolute bottom-0 left-0 right-0 px-[10px] pt-3 pb-0 -mb-5
                    border-t border-white/20 flex flex-col items-center transition-all duration-300">

            <!-- Avatar Dinamis (UPDATE) -->
            <!-- Avatar Dinamis (UPDATE) -->
            <a id="avatarBtn" aria-label="Menu Profil" href="{{ route('profile') }}"
                class="avatar w-[38px] h-[38px] bg-white/20 rounded-full flex items-center justify-center mb-1.5 text-lg text-white cursor-pointer transition-all duration-300 overflow-hidden">
                @if(Auth::user()->foto_profil)
                    <!-- Tampilkan Foto User -->
                    <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" alt="Foto Profil"
                        class="w-full h-full object-cover">
                @else
                    <!-- Tampilkan Icon Default -->
                    <i class="fas fa-user"></i>
                @endif
            </a>

            <!-- Popup Menu dan logout -->
            <x-popup />
    </aside>

    <!-- JavaScript -->
    <x-script-sidebar />
</body>

</html>