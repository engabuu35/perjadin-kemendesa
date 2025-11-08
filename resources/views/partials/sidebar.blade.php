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
    
    <style type="text/tailwindcss">
        @layer base {
            body {
                font-family: 'Poppins', sans-serif;
            }
        }
        
        @layer components {
            /* Sidebar Active State */
            .sidebar.active {
                @apply w-[250px];
            }

            .sidebar.active .sidebar-menu a span:last-child {
                @apply opacity-100;
            }

            .sidebar.active .user-profile {
                @apply items-center px-[30px] pt-5 pb-5 bottom-[30px] mb-0;
            }

            .sidebar.active .user-profile .name,
            .sidebar.active .user-profile .role {
                @apply opacity-100;
            }
            
            .sidebar.active .logout-btn {
                @apply opacity-100;
            }

            /* Overlay Active State */
            .overlay.active {
                @apply opacity-100 visible pointer-events-auto;
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
                @apply relative overflow-visible;
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
                transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 0;
            }

            .sidebar-menu a:hover::before {
                width: 60px;
                height: 60px;
            }

            /* Hover memanjang saat sidebar aktif */
            .sidebar.active .sidebar-menu a {
                @apply px-[40px];
            }

            .sidebar.active .sidebar-menu a:hover::before {
                width: calc(100% - 40px);
                height: 50px;
                border-radius: 20px;
            }

            .sidebar-menu a .icon {
                @apply relative z-10 w-8 h-8 flex items-center justify-center text-2xl;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .sidebar-menu a:hover .icon {
                @apply scale-110;
                color: #2954B0;
            }

            /* Text jadi biru pas hover */
            .sidebar-menu a span:last-child {
                @apply relative z-10;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                transform: translateX(0);
            }

            /* Smooth text slide in saat sidebar aktif */
            .sidebar.active .sidebar-menu a span:last-child {
                @apply opacity-100;
                transform: translateX(0);
            }

            .sidebar-menu a span:last-child {
                transform: translateX(-10px);
            }

            .sidebar-menu a:hover span:last-child {
                color: #2954B0;
            }

            /* Hover Effect untuk Logout Button - Lebih Bagus */
            .logout-btn {
                @apply relative overflow-hidden;
                transform-origin: center;
                transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            }

            .logout-btn::before {
                content: '';
                @apply absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-0 h-0 
                       bg-gradient-to-r from-red-500/30 to-red-600/30 rounded-full 
                       transition-all duration-700 ease-out -z-10;
            }

            .logout-btn:hover {
                @apply scale-105 bg-red-500/20;
                box-shadow: 0 5px 20px rgba(239, 68, 68, 0.3);
            }

            .logout-btn:hover::before {
                @apply w-[200px] h-[200px];
            }

            .logout-btn:active {
                @apply scale-95;
                transition: all 0.1s ease-in-out;
            }

            .logout-btn i {
                transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            }

            .logout-btn:hover i {
                transform: translateX(-3px) rotate(-10deg);
            }
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
                <a href="#" class="flex items-center py-[15px] px-[26px] text-white no-underline 
                                   transition-colors duration-300 gap-[15px] text-xl whitespace-nowrap">
                    <span class="icon w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-home"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Beranda</span>
                </a>
            </li>
            
            <!-- Menu Penugasan -->
            <li class="my-2.5">
                <a href="#" class="flex items-center py-[15px] px-[26px] text-white no-underline 
                                   transition-colors duration-300 gap-[15px] text-xl whitespace-nowrap">
                    <span class="icon w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-briefcase"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Penugasan</span>
                </a>
            </li>
            
            <!-- Menu Riwayat -->
            <li class="my-2.5">
                <a href="#" class="flex items-center py-[15px] px-[26px] text-white no-underline 
                                   transition-colors duration-300 gap-[15px] text-xl whitespace-nowrap">
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
                Fernanda Aditia Putra
            </div>
            
            <!-- User Role/NPM -->
            <div class="role text-white/70 text-xs mb-[15px] opacity-0 transition-opacity duration-300 whitespace-nowrap">
                NPM: 2021011
            </div>
            
            <!-- Logout Button -->
            <button class="logout-btn flex items-center justify-center gap-2.5 py-2.5 px-[15px] 
                           bg-white/10 border-none rounded-lg text-white cursor-pointer w-full 
                           transition-all duration-300 text-sm opacity-0 hover:bg-white/20">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </div>
    </aside>

    <!-- JavaScript -->
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay');
            
            // Tambahkan efek scale smooth
            sidebar.style.transform = sidebar.classList.contains('active') ? 'scale(0.98)' : 'scale(1)';
            
            setTimeout(() => {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                sidebar.style.transform = 'scale(1)';
            }, 100);
        }

        // Tambahkan smooth scroll untuk menu
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Efek ripple saat klik
                const ripple = document.createElement('span');
                ripple.style.cssText = `
                    position: absolute;
                    left: ${e.offsetX}px;
                    top: ${e.offsetY}px;
                    width: 0;
                    height: 0;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.5);
                    transform: translate(-50%, -50%);
                    animation: ripple-effect 0.6s ease-out;
                    pointer-events: none;
                    z-index: 100;
                `;
                
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });
        });

        // Tambahkan animasi ripple
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple-effect {
                to {
                    width: 150px;
                    height: 150px;
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>