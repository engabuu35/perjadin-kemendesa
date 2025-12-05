<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar - SIPERDIN</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: sans-serif;
            background-color: #e8e9f0;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><circle cx="10" cy="10" r="2" fill="%23d0d0dc" opacity="0.3"/></svg>');
            padding-top: 50px;
        }
        
        /* Animasi untuk hamburger menu */
        .menu-icon {
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 6px;
        }
        
        .menu-icon:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .menu-icon i {
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        /* Hover effect untuk logo dengan gradient shimmer */
        .logo {
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            background: linear-gradient(120deg, transparent, rgba(255,255,255,0.3), transparent);
            background-size: 200% 100%;
            background-position: -100% 0;
            -webkit-background-clip: text;
            background-clip: text;
        }
        
        .logo:hover {
            letter-spacing: 2px;
            transform: scale(1.05) translateY(-2px);
            text-shadow: 0 4px 12px rgba(255, 255, 255, 0.4);
            background-position: 100% 0;
            animation: shimmer 1.5s ease-in-out;
        }
        
        @keyframes shimmer {
            0% { background-position: -100% 0; }
            100% { background-position: 200% 0; }
        }
        
        /* Hover effect untuk user info dengan cahaya */
        .user-info span {
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            background: linear-gradient(120deg, transparent, rgba(255,255,255,0.3), transparent);
            background-size: 200% 100%;
            background-position: -100% 0;
            -webkit-background-clip: text;
            background-clip: text;
        }
        
        .user-info:hover span {
            letter-spacing: 1.5px;
            transform: scale(1.05) translateY(-2px);
            text-shadow: 0 2px 8px rgba(255, 255, 255, 0.4);
            background-position: 100% 0;
            animation: shimmer 1.5s ease-in-out;
        }
    </style>
</head>
<body>

    @php
        $hour = date('H');
        if ($hour >= 5 && $hour < 11) {
            $greeting = 'Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Sore';
        } else {
            $greeting = 'Malam';
        }
    @endphp

    <!-- Navbar dengan position fixed -->
    <div class="header fixed top-0 left-0 right-0 z-[100] flex justify-between items-center 
                bg-[#2954B0] text-white 
                py-[8px] px-3 md:px-[12px] rounded-b-[15px] shadow-[0_6px_15px_rgba(0,0,0,0.2)]">
        
        <div class="header-left flex items-center gap-[10px]">
            <div class="menu-icon text-lg cursor-pointer transition-all duration-300 hover:text-gray-200" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </div>
            <div class="logo font-bold italic tracking-[0.5px] text-base md:text-xl">
                SIPERDIN
            </div>
        </div>
        <!-- sapaan navbar/avatar-->
        <div class="user-info flex items-center gap-2 text-sm mr-3">
            <span class="hidden md:inline font-light tracking-wide font-semibold text-white/95">
                Selamat {{ $greeting }}, {{ Auth::user()->nama }}!
            </span>
            <!-- Avatar Mobile (hanya muncul di mobile) -->
            <div class="relative sm:hidden">
                <button id="mobileAvatarBtn"
                    class="w-9 h-9 rounded-full overflow-hidden bg-white/20 flex items-center justify-center transition-all duration-300 hover:scale-110">
                    @if(Auth::user()->foto_profil)
                        <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-user text-white text-lg"></i>
                    @endif
                </button>

                <!-- POPUP (ambil dari komponenmu) -->
                <div id="mobileProfilePopup" class="absolute right-0 top-[50px] bg-white text-gray-800 rounded-xl shadow-lg py-1.5 w-[160px] opacity-0 invisible transition-all duration-300 z-[9999]">

                    <!-- Profil Saya -->
                    <a href="{{ route('profile') }}"
                    class="flex items-center gap-2 px-3 py-1.5 text-xs hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-user"></i>
                        <span>Profil Saya</span>
                    </a>

                    <!-- Logout -->
                    <button id="popupLogout"
                        class="flex items-center gap-2 w-full text-left px-3 py-1.5 text-xs hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global function untuk toggle sidebar
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay');
            const menuIcon = document.querySelector('.menu-icon');
            const iconElement = menuIcon.querySelector('i');
            
            // Toggle sidebar & overlay
            if (sidebar && overlay) {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }
            
            menuIcon.classList.toggle('active');
            
            // Toggle icon antara bars dan bars-staggered
            if (menuIcon.classList.contains('active')) {
                iconElement.classList.remove('fa-bars');
                iconElement.classList.add('fa-bars-staggered');
            } else {
                iconElement.classList.remove('fa-bars-staggered');
                iconElement.classList.add('fa-bars');
            }
        }

        // Mobile Avatar & Popup
        document.addEventListener("DOMContentLoaded", () => {
            const mobileBtn = document.getElementById("mobileAvatarBtn");
            const mobilePopup = document.getElementById("mobileProfilePopup");
            const mobileLogout = document.getElementById("mobilePopupLogout");

            if (mobileBtn && mobilePopup) {
                // Click avatar to toggle popup
                mobileBtn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    mobilePopup.classList.toggle("opacity-0");
                    mobilePopup.classList.toggle("invisible");
                });

                // Stop propagation on popup
                mobilePopup.addEventListener("click", (e) => {
                    e.stopPropagation();
                });

                // Click outside to close
                document.addEventListener("click", (e) => {
                    if (!mobilePopup.contains(e.target) && !mobileBtn.contains(e.target)) {
                        mobilePopup.classList.add("opacity-0", "invisible");
                    }
                });
            }

            // Mobile logout button
            if (mobileLogout) {
                mobileLogout.addEventListener("click", (e) => {
                    e.stopPropagation();
                    mobilePopup.classList.add("opacity-0", "invisible");
                    
                    const logoutModal = document.getElementById("logoutModal");
                    if (logoutModal) {
                        logoutModal.classList.remove("opacity-0", "invisible");
                        logoutModal.classList.add("opacity-100", "visible");
                    }
                });
            }
        });
    </script>
</body>
</html>