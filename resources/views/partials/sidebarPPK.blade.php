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
                transition-all duration-300 z-[98] pointer-events-none" 
        onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar bg-sidebar fixed left-0 top-[65px] w-[60px] h-[calc(100vh-50px)]
                transition-all duration-300 ease-in-out z-[99] pt-3 rounded-r-[20px]
                shadow-[4px_0_12px_rgba(0,0,0,0.2)] overflow-visible">

        <!-- Menu List -->
        <ul class="sidebar-menu list-none py-3">
            <!-- Menu Beranda -->
            <li class="my-1.5">
                <a href="{{ route('pages.beranda') }}" class="flex items-center py-[10px] px-[18px] text-white no-underline 
                                transition-colors duration-300 gap-[10px] text-base whitespace-nowrap {{ request()->routeIs('pages.beranda') ? 'active' : '' }}">
                    <span class="icon w-[22px] h-[22px] flex items-center justify-center text-base">
                        <i class="fa-solid fa-home"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Beranda</span>
                </a>
            </li>
            
            <!-- Menu Pelaporan -->
            <li class="my-1.5">
                <a href="{{ route('ppk.pelaporan') }}" class="flex items-center py-[10px] px-[18px] text-white no-underline 
                                transition-colors duration-300 gap-[10px] text-base whitespace-nowrap  {{ request()->routeIs('ppk.pelaporan', 'ppk.detailPelaporan') ? 'active' : '' }}">
                    <span class="icon w-[22px] h-[22px] flex items-center justify-center text-base">
                        <i class="fa-solid fa-file"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Pelaporan</span>
                </a>
            </li>
            
            <!-- Menu Tabel Rekapan -->
            <li class="my-1.5">
                <a href="{{ route('ppk.tabelrekap') }}" class="flex items-center py-[10px] px-[18px] text-white no-underline 
                                transition-colors duration-300 gap-[10px] text-base whitespace-nowrap {{ request()->routeIs('ppk.tabelrekap') ? 'active' : '' }}">
                    <span class="icon w-[22px] h-[22px] flex items-center justify-center text-base">
                        <i class="fa-solid fa-table"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Tabel Rekap</span>
                </a>
            </li>

            <!-- Menu Riwayat -->
            <li class="my-1.5">
                <a href="{{ route('riwayat') }}" class="flex items-center py-[10px] px-[18px] text-white no-underline 
                                transition-colors duration-300 gap-[10px] text-base whitespace-nowrap {{ request()->routeIs('riwayat') ? 'active' : '' }}">
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
        <a id="avatarBtn" 
        href="{{ route('profile') }}"
        class="avatar w-[38px] h-[38px] bg-white/20 rounded-full flex items-center justify-center mb-1.5 text-lg text-white cursor-pointer transition-all duration-300 overflow-hidden">
            @if(Auth::user()->foto_profil)
                <!-- Tampilkan Foto User -->
                <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" alt="Foto Profil" class="w-full h-full object-cover">
            @else
                <!-- Tampilkan Icon Default -->
                <i class="fas fa-user"></i>
            @endif
        </a>

        <!-- Popup Menu dan logout -->
        <x-popup /> 
    </aside>

    <!-- JavaScript -->
    <script>
        // Ripple effect saat klik di sidebar - 1 gelombang aja
        const sidebar = document.querySelector('.sidebar');
        // const userProfile = document.querySelector('.user-profile');
        const logoutBtn = document.querySelector('.logout-btn');
        const logoutForm = document.getElementById('logoutForm');
        const logoutModal = document.getElementById('logoutModal');
        const cancelLogout = document.getElementById('cancelLogout');
        const confirmLogout = document.getElementById('confirmLogout');

        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                logoutModal.classList.remove('opacity-0', 'invisible');
                logoutModal.classList.add('opacity-100', 'visible');
            });
        }

        // Tutup modal saat klik Batal
        cancelLogout.addEventListener('click', (e) => {
            e.preventDefault();
            logoutModal.classList.add('opacity-0', 'invisible');
            logoutModal.classList.remove('opacity-100', 'visible');
        });


        // Logout sesungguhnya saat klik Logout
        confirmLogout.addEventListener('click', () => {
            // Tutup modal logout
            logoutModal.classList.add('opacity-0', 'invisible');
            logoutModal.classList.remove('opacity-100', 'visible');

            // === Buat modal sukses yang bentuknya sama dengan konfirmasi ===
            const successDiv = document.createElement("div");

            successDiv.innerHTML = `
                <div class="fixed inset-0 bg-black/50 flex items-center justify-center 
                    opacity-0 transition-opacity duration-300 z-[9999]" id="logoutSuccess">
                    
                    <div class="bg-white rounded-lg shadow-lg w-[90%] max-w-sm p-6 text-center">
                        
                        <!-- Animasi Centang -->
                        <svg class="w-16 h-16 mx-auto text-green-600 animate-check" fill="none" 
                            stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path d="M5 13l4 4L19 7" />
                        </svg>

                        <h3 class="text-lg font-bold mt-4 text-gray-800">Logout Berhasil</h3>
                        <p class="text-gray-600 mt-1">Kamu telah keluar dari akun.</p>
                    </div>
                </div>

                <style>
                @keyframes checkAnimation {
                    0% { stroke-dashoffset: 50; }
                    100% { stroke-dashoffset: 0; }
                }
                .animate-check path {
                    stroke-dasharray: 50;
                    stroke-dashoffset: 50;
                    animation: checkAnimation .6s ease forwards;
                }
                </style>
            `;

            document.body.appendChild(successDiv);

            // Tampilkan modal sukses (fade-in)
            setTimeout(() => {
                document.getElementById("logoutSuccess").classList.remove("opacity-0");
            }, 50);

            // Kirim form logout setelah animasi selesai
            setTimeout(() => {
                logoutForm.submit();
            }, 1300);
        });

        // tutup modal jika klik di luar dialog (di overlay modal)
        logoutModal.addEventListener('click', (e) => {
            if (e.target === logoutModal) {
                logoutModal.classList.add('opacity-0', 'invisible');
                logoutModal.classList.remove('opacity-100', 'visible');
            }
        });

        sidebar.addEventListener('click', function(e) {
            // Dapatkan posisi klik relatif terhadap sidebar
            const rect = sidebar.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            // Buat ripple
            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute;
                left: ${x}px;
                top: ${y}px;
                width: 0;
                height: 0;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.4);
                transform: translate(-50%, -50%);
                animation: ripple-effect-1 1s ease-out;
                pointer-events: none;
                z-index: 9999;
            `;
            
            sidebar.appendChild(ripple);
            
            // Hapus ripple setelah animasi selesai
            setTimeout(() => ripple.remove(), 1000);
        });

        // Handle menu click dengan smooth transition
        const menuLinks = document.querySelectorAll('.sidebar-menu a');
        
        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // Hapus class active dari semua menu
                menuLinks.forEach(item => {
                    if (item !== this && item.classList.contains('active')) {
                        item.style.transition = 'all 0.3s ease-out';
                        item.classList.remove('active');
                    }
                });

                // Tambahkan class active ke menu yang diklik
                this.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                this.classList.add('active');

                // Redirect ke URL setelah delay animasi
                const url = this.getAttribute('href');
                setTimeout(() => {
                    window.location.href = url;
                }, 150); // delay sesuai animasi
            });
        });


        // FUNCTION INI HARUS SAMA dengan yang di navbar
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay');
            
            // Toggle class active
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Sinkronisasi dengan hamburger icon di navbar (jika ada)
            const menuIcon = document.querySelector('.menu-icon');
            if (menuIcon) {
                const iconElement = menuIcon.querySelector('i');
                menuIcon.classList.toggle('active');
                
                if (menuIcon.classList.contains('active')) {
                    iconElement.classList.remove('fa-bars');
                    iconElement.classList.add('fa-bars-staggered');
                } else {
                    iconElement.classList.remove('fa-bars-staggered');
                    iconElement.classList.add('fa-bars');
                }
            }
        }

        const sidebarEl = document.querySelector('.sidebar');
        const avatarBtn = document.getElementById('avatarBtn');
        const profilePopup = document.getElementById('profilePopup');
        const popupLogout = document.getElementById('popupLogout');

        //profil pop up
        profilePopup.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Klik avatar
        avatarBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const sidebarExpanded =
                sidebarEl.classList.contains('active') ||
                sidebarEl.offsetWidth > 60;

            if (!sidebarExpanded) {
                e.preventDefault();
                profilePopup.classList.remove('opacity-0', 'invisible');
                profilePopup.classList.add('opacity-100', 'visible');
            }
        });


        // klik Logout dari popup
        popupLogout.addEventListener('click', function (e) {
            e.stopPropagation();
            logoutModal.classList.remove('opacity-0', 'invisible');
            logoutModal.classList.add('opacity-100', 'visible');
        });

        // Klik di luar popup → tutup
        document.addEventListener('click', function (e) {
            if (!profilePopup.contains(e.target) && !avatarBtn.contains(e.target)) {
                profilePopup.classList.add('opacity-0', 'invisible');
                profilePopup.classList.remove('opacity-100', 'visible');
            }
        });
    </script>
</body>
</html>