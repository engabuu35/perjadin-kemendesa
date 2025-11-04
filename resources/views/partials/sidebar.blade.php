<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar - SIPERDIN (Tailwind)</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style type="text/tailwindcss">
        @layer base {
            body {
                /* Menerapkan font Poppins ke seluruh halaman */
                font-family: 'Poppins', sans-serif;
            }
        }
        
        @layer components {
            /* Aturan untuk status .active pada Sidebar */
            .sidebar.active {
                @apply w-[250px];
            }

            .sidebar.active .sidebar-menu a span:last-child {
                @apply opacity-100;
            }

            /* Aturan untuk status .active pada User Profile */
            .sidebar.active .user-profile {
                @apply items-center px-[30px] py-5; /* Mengubah padding horizontal saat aktif */
            }

            .sidebar.active .user-profile .name,
            .sidebar.active .user-profile .role {
                @apply opacity-100;
            }
            
            .sidebar.active .logout-btn {
                @apply opacity-100;
            }

            /* Aturan untuk status .active pada Overlay */
            .overlay.active {
                @apply opacity-100 visible pointer-events-auto;
            }
        }
    </style>
</head>
<body class="bg-[#e8e9f0]"> <div class="overlay fixed top-0 left-0 w-full h-full bg-black/50 opacity-0 invisible 
                transition-all duration-300 z-[98] pointer-events-none" 
         onclick="toggleSidebar()"></div>

    <aside class="sidebar fixed left-0 top-[80px] w-[80px] h-[calc(100vh-80px)] 
                  bg-gradient-to-b from-[#2d5ba8] to-[#1e4b98] transition-all duration-300 ease-in-out 
                  z-[99] pt-5 rounded-r-[30px] shadow-[5px_0_15px_rgba(0,0,0,0.2)] overflow-hidden">
        
        <ul class="sidebar-menu list-none py-5">
            <li class="my-2.5">
                <a href="#" class="flex items-center py-[15px] px-[30px] text-white no-underline 
                                transition-colors duration-300 gap-[15px] text-base whitespace-nowrap hover:bg-white/10">
                    <span class="icon w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fas fa-home"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Beranda</span>
                </a>
            </li>
            <li class="my-2.5">
                <a href="#" class="flex items-center py-[15px] px-[30px] text-white no-underline 
                                transition-colors duration-300 gap-[15px] text-base whitespace-nowrap hover:bg-white/10">
                    <span class="icon w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-briefcase"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Penugasan</span>
                </a>
            </li>
            <li class="my-2.5">
                <a href="#" class="flex items-center py-[15px] px-[30px] text-white no-underline 
                                transition-colors duration-300 gap-[15px] text-base whitespace-nowrap hover:bg-white/10">
                    <span class="icon w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fas fa-history"></i>
                    </span>
                    <span class="opacity-0 transition-opacity duration-300">Riwayat</span>
                </a>
            </li>
        </ul>

        <div class="user-profile absolute bottom-[30px] left-0 right-0 px-[15px] py-5 
                        border-t border-white/20 flex flex-col items-center transition-all duration-300">
            
            <div class="avatar w-[50px] h-[50px] bg-white/20 rounded-full flex items-center 
                          justify-center mb-2.5 text-2xl text-white">
                <i class="fas fa-user"></i>
            </div>
            
            <div class="name text-white text-sm font-bold mb-[5px] opacity-0 transition-opacity duration-300 whitespace-nowrap">
                Fernanda Aditia Putra
            </div>
            <div class="role text-white/70 text-xs mb-[15px] opacity-0 transition-opacity duration-300 whitespace-nowrap">
                NPM: 2021011
            </div>
            
            <button class="logout-btn flex items-center justify-center gap-2.5 py-2.5 px-[15px] 
                           bg-white/10 border-none rounded-lg text-white cursor-pointer w-full 
                           transition-all duration-300 text-sm opacity-0 hover:bg-white/20">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </div>
    </aside>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }
    </script>
</body>
</html>