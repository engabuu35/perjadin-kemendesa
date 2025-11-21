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
    
   <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Sidebar Active State */
        .sidebar.active {
            width: 250px;
        }

        .sidebar.active .sidebar-menu a span:last-child {
            opacity: 1;
        }

        .sidebar.active .user-profile {
            align-items: center;
            padding-left: 30px;
            padding-right: 30px;
            padding-top: 20px;
            padding-bottom: 20px;
            bottom: 30px;
            margin-bottom: 0;
        }

        .sidebar.active .user-profile .name,
        .sidebar.active .user-profile .role {
            opacity: 1;
        }
        
        .sidebar.active .logout-btn {
            opacity: 1;
        }

        /* Overlay Active State */
        .overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        /* Sidebar Background */
        .bg-sidebar {
            background-image: 
                url('../img/sidebar-pattern.png'),
                linear-gradient(to bottom, #2954B0, #24519D);
            background-repeat: repeat, no-repeat;
            background-size: 255px, cover;
        }

        /* Hover Effect Bulat untuk Menu */
        .sidebar-menu a {
            position: relative;
            overflow: visible;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-menu a::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 0;
            height: 0;
            background-color: #EDEDFF;
            border-radius: 50%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 0;
            opacity: 0;
        }

        .sidebar-menu a:hover::before {
            width: 60px;
            height: 60px;
            opacity: 1;
        }
        
        .sidebar-menu a:active::before {
            width: 60px;
            height: 60px;
            opacity: 1;
        }

        .sidebar-menu a.active::before {
            content: "";
            position: absolute;
            width: 55px;            /* ukuran lingkaran */
            height: 55px;
            background: #ffffffd0;  /* putih lembut */
            border-radius: 50%;     /* bentuk lingkaran */
            opacity: 1;
            z-index: -1;
        }

        .sidebar-menu a.active .icon {
            color: #2954B0;
            transform: scale(1.1);
        }

        .sidebar-menu a.active span:last-child {
            color: #2954B0 !important;
        }
        
        .sidebar-menu a.active {
            background: none !important;
        }
        .sidebar-menu a .icon {
            position: relative;
        }

        /* icon active berubah biru */
        .sidebar-menu a.active .icon {
            color: #2954B0 !important;
        }


        /* Hover memanjang saat sidebar aktif */
        .sidebar.active .sidebar-menu a {
            padding-left: 40px;
            padding-right: 40px;
        }

        .sidebar.active .sidebar-menu a:hover::before {
            width: calc(100% - 40px);
            height: 50px;
            border-radius: 20px;
            opacity: 1;
        }
        
        .sidebar.active .sidebar-menu a:active::before {
            width: calc(100% - 40px);
            height: 50px;
            border-radius: 20px;
            opacity: 1;
        }

        .sidebar.active .sidebar-menu a.active::before {
            width: calc(100% - 40px);
            height: 50px;
            border-radius: 20px;
            opacity: 1;
        }

        /* Ripple Animation - 2 waves */
        @keyframes ripple-effect-1 {
            0% {
                width: 0;
                height: 0;
                opacity: 1;
            }
            100% {
                width: 120px;
                height: 120px;
                opacity: 0;
            }
        }
        
        @keyframes ripple-effect-2 {
            0% {
                width: 0;
                height: 0;
                opacity: 1;
            }
            100% {
                width: 150px;
                height: 150px;
                opacity: 0;
            }
        }

        .sidebar-menu a .icon {
            position: relative;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), scale 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 30;
        }

        .sidebar-menu a:hover .icon {
            transform: scale(1.1);
            color: #2954B0;
        }

        .sidebar-menu a:active .icon {
            color: #2954B0 !important;
            transform: none !important;
        }

        .sidebar.active .sidebar-menu a:active .icon {
            color: #2954B0 !important;
            transform: none !important;
        }

        .sidebar-menu a span:last-child {
            position: relative;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(-10px);
            z-index: 30;
        }

        .sidebar.active .sidebar-menu a span:last-child {
            opacity: 1;
            transform: translateX(0);
            color: white;
        }

        .sidebar-menu a:hover span:last-child {
            color: #2954B0 !important;
        }
        
        .sidebar.active .sidebar-menu a:hover span:last-child {
            color: #2954B0 !important;
        }
        
        .sidebar-menu a:active span:last-child {
            color: #2954B0 !important;
        }
        
        .sidebar.active .sidebar-menu a:active span:last-child {
            color: #2954B0 !important;
        }

        /* Hover Effect untuk Logout Button */
        .logout-btn {
            position: relative;
            overflow: hidden;
            transform-origin: center;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .logout-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 0;
            height: 0;
            background: linear-gradient(to right, rgba(239, 68, 68, 0.3), rgba(220, 38, 38, 0.3));
            border-radius: 50%;
            transition: all 0.7s ease-out;
            z-index: -1;
        }

        .logout-btn:hover {
            transform: scale(1.05);
            background-color: rgba(239, 68, 68, 0.2);
            box-shadow: 0 5px 20px rgba(239, 68, 68, 0.3);
        }

        .logout-btn:hover::before {
            width: 200px;
            height: 200px;
        }

        .logout-btn:active {
            transform: scale(0.95);
            transition: all 0.1s ease-in-out;
        }

        .logout-btn i {
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .logout-btn:hover i {
            transform: translateX(-3px) rotate(-10deg);
        }
    </style>
</head>
<body class="bg-[#e8e9f0]">
    <!-- Overlay -->
    <div class="overlay fixed top-0 left-0 w-full h-full bg-black/50 opacity-0 invisible 
                transition-all duration-300 z-[98] pointer-events-none" 
        onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar bg-sidebar fixed left-0 top-[90px] w-[80px] h-[calc(100vh-80px)]
                transition-all duration-300 ease-in-out z-[99] pt-5 rounded-r-[30px]
                shadow-[5px_0_15px_rgba(0,0,0,0.2)] overflow-hidden">

        <!-- Menu List -->
        <ul class="sidebar-menu list-none py-5">
            <!-- Menu Beranda -->
            <li class="my-2.5">
                <a href="{{ route('pages.beranda') }}" class="flex items-center py-[15px] px-[26px] text-white no-underline 
                                transition-colors duration-300 gap-[15px] text-xl whitespace-nowrap {{ request()->routeIs('pages.beranda') ? 'active' : '' }}">
                    <span class="icon w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-home"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Beranda</span>
                </a>
            </li>
            
            <!-- Menu Penugasan -->
            <li class="my-2.5">
                <a href="{{ route('pic.penugasan') }}" class="flex items-center py-[15px] px-[26px] text-white no-underline 
                                transition-colors duration-300 gap-[15px] text-xl whitespace-nowrap {{ request()->routeIs('pic.penugasan') ? 'active' : '' }}">
                    <span class="icon w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-briefcase"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Penugasan</span>
                </a>
            </li>

            <!-- Menu Pelaporan -->
            <li class="my-2.5">
                <a href="{{ route('pic.pelaporan') }}" class="flex items-center py-[15px] px-[26px] text-white no-underline 
                                transition-colors duration-300 gap-[15px] text-xl whitespace-nowrap {{ request()->routeIs('pic.pelaporan') ? 'active' : '' }}">
                    <span class="icon w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-file"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Pelaporan</span>
                </a>
            </li>

            <!-- Menu LS Rampung -->
            <li class="my-2.5">
                <a href="{{ route('pic.lsrampung') }}" class="flex items-center py-[15px] px-[26px] text-white no-underline 
                                transition-colors duration-300 gap-[15px] text-xl whitespace-nowrap {{ request()->routeIs('pic.lsrampung') ? 'active' : '' }}">
                    <span class="icon w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-money-check-dollar"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">LS Rampung</span>
                </a>
            </li>

            <!-- Menu Pegawai -->
            <li class="my-2.5">
                <a href="{{ route('pic.pegawai.index') }}" class="flex items-center py-[15px] px-[26px] text-white no-underline 
                                transition-colors duration-300 gap-[15px] text-xl whitespace-nowrap {{ request()->routeIs('pic.pegawai.index') ? 'active' : '' }}">
                    <span class="icon w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-users-rectangle"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Pegawai</span>
                </a>
            </li>
            
            <!-- Menu Riwayat -->
            <li class="my-2.5">
                <a href="{{ route('riwayat') }}" class="flex items-center py-[15px] px-[26px] text-white no-underline 
                                transition-colors duration-300 gap-[15px] text-xl whitespace-nowrap {{ request()->routeIs('riwayat') ? 'active' : '' }}">
                    <span class="icon w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-history"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Riwayat</span>
                </a>
            </li>
        </ul>

        <!-- User Profile Section -->
        <div class="user-profile absolute bottom-0 left-0 right-0 px-[15px] pt-5 pb-0 -mb-8
                    border-t border-white/20 flex flex-col items-center transition-all duration-300">
            
            <!-- Avatar -->
            <div class="avatar w-[50px] h-[50px] bg-white/20 rounded-full flex items-center 
                        justify-center mb-2.5 text-2xl text-white">
                <i class="fas fa-user"></i>
            </div>
            
            <!-- User Name -->
            <div class="name text-white text-sm font-bold mb-[5px] opacity-0 transition-opacity duration-300 whitespace-nowrap">
                {{ Auth::user()->nama }}
            </div>
            
            <!-- User Role/NPM -->
            <div class="role text-white/70 text-xs mb-[15px] opacity-0 transition-opacity duration-300 whitespace-nowrap">
                {{ Auth::user()->nip }}
            </div>
            
            <!-- Logout Button -->
            <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                @csrf
                <button id="logoutBtn" type="button" class="logout-btn flex items-center justify-center gap-2.5 
                        py-3 px-6
                        w-full
                        bg-white/10 border-none rounded-lg text-white cursor-pointer 
                        transition-all duration-300 hover:bg-white/20 text-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>

            <!-- Modal Konfirmasi Logout -->
            <div id="logoutModal" class="fixed inset-0 bg-black/50 flex items-center justify-center opacity-0 invisible transition-opacity duration-300 z-50">
                <div class="bg-white rounded-lg shadow-lg w-[90%] max-w-sm p-5 text-center">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Konfirmasi Logout</h3>
                    <p class="text-gray-600 mb-5">Apakah kamu yakin ingin keluar dari akun ini?</p>
                    <div class="flex justify-between gap-3">
                        <button id="cancelLogout" class="flex-1 py-2 px-4 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Batal</button>
                        <button id="confirmLogout" class="flex-1 py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Logout</button>
                    </div>
                </div>
            </div>
        </div>
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

        logoutBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault(); // sangat penting supaya form gak submit langsung
            logoutModal.classList.remove('opacity-0', 'invisible');
            logoutModal.classList.add('opacity-100', 'visible');
        });

        // Tutup modal saat klik Batal
        cancelLogout.addEventListener('click', (e) => {
            e.preventDefault();
            logoutModal.classList.add('opacity-0', 'invisible');
            logoutModal.classList.remove('opacity-100', 'visible');
        });

        // Logout sesungguhnya saat klik Logout
        confirmLogout.addEventListener('click', () => {
            logoutForm.submit();
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
    </script>
    <!-- <script>
    

    // ===== Sidebar Ripple Effect =====
    sidebar.addEventListener('click', function(e) {
        // Jangan toggle jika klik di user profile atau logout button
        if (userProfile.contains(e.target)) return;

        toggleSidebar();

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

        setTimeout(() => ripple.remove(), 1000);
    });

    // ===== Logout Modal =====
    const logoutModalHTML = `
    <div id="logoutModal" class="fixed inset-0 bg-black/50 flex items-center justify-center opacity-0 invisible transition-opacity duration-300 z-50">
        <div class="bg-white rounded-lg shadow-lg w-[90%] max-w-sm p-5 text-center">
            <h3 class="text-lg font-bold mb-4 text-gray-800">Konfirmasi Logout</h3>
            <p class="text-gray-600 mb-5">Apakah kamu yakin ingin keluar dari akun ini?</p>
            <div class="flex justify-between gap-3">
                <button id="cancelLogout" class="flex-1 py-2 px-4 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Batal</button>
                <button id="confirmLogout" class="flex-1 py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Logout</button>
            </div>
        </div>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', logoutModalHTML);

    const logoutModal = document.getElementById('logoutModal');
    const cancelLogout = document.getElementById('cancelLogout');
    const confirmLogout = document.getElementById('confirmLogout');

    logoutBtn.addEventListener('click', function(e) {
        e.stopPropagation(); // Jangan trigger ripple sidebar
        logoutModal.classList.remove('opacity-0', 'invisible');
        logoutModal.classList.add('opacity-100', 'visible');
    });

    // Tutup modal saat klik Batal
    cancelLogout.addEventListener('click', () => {
        logoutModal.classList.add('opacity-0', 'invisible');
        logoutModal.classList.remove('opacity-100', 'visible');
    });

    // Logout sesungguhnya saat klik Logout
    confirmLogout.addEventListener('click', () => {
        logoutForm.submit();
    });

    // ===== Menu Click Smooth Transition =====
    const menuLinks = document.querySelectorAll('.sidebar-menu a');
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            menuLinks.forEach(item => {
                if (item !== this && item.classList.contains('active')) {
                    item.style.transition = 'all 0.3s ease-out';
                    item.classList.remove('active');
                }
            });

            this.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            this.classList.add('active');

            const url = this.getAttribute('href');
            setTimeout(() => { window.location.href = url; }, 150);
        });
    });

    // ===== Toggle Sidebar =====
    function toggleSidebar() {
        const overlay = document.querySelector('.overlay');
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');

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
    </script> -->
</body>
</html>