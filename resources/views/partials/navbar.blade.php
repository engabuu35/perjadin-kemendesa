<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar - SIPERDIN (Tailwind)</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: sans-serif;
            background-color: #e8e9f0;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><circle cx="10" cy="10" r="2" fill="%23d0d0dc" opacity="0.3"/></svg>');
        }
        
        /* Animasi untuk hamburger menu */
        .menu-icon {
            transition: all 0.3s ease;
            border-radius: 12px;
            padding: 8px;
        }
        
        .menu-icon:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .menu-icon i {
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        /* Hover effect untuk logo */
        .logo {
            transition: all 0.3s ease;
        }
        
        .logo:hover {
            letter-spacing: 2px;
            transform: scale(1.02);
        }
        
        /* Hover effect untuk user info */
        .user-info:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .user-info .user-icon {
            transition: all 0.3s ease;
        }
        
        .user-info:hover .user-icon {
            transform: scale(1.1);
        }
        
        /* Aturan untuk status .active pada dropdown */
        .profile-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Aturan untuk status .active pada overlay */
        .dropdown-overlay.active {
            display: block;
        }
    </style>
</head>
<body>

    <div class="header sticky top-0 left-0 right-0 z-[100] flex justify-between items-center 
                bg-[#2954B0] text-white 
                py-[15px] px-10 md:px-[50px] rounded-b-[30px] shadow-[0_8px_20px_rgba(0,0,0,0.2)]">
        
        <div class="header-left flex items-center gap-[15px]">
            <div class="menu-icon text-2xl cursor-pointer transition-all duration-300 hover:text-gray-200" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </div>
            <div class="logo font-bold italic tracking-[1px] text-xl md:text-2xl">
                SIPERDIN
            </div>
        </div>

        <div class="user-info flex items-center gap-6 text-xl cursor-pointer py-[6px] pl-3 pr-2 
                    rounded-full transition-all duration-300 active:bg-black/20" 
             onclick="toggleProfileDropdown()">
            
            <span class="hidden md:inline">Reza Anu</span>
            <div class="user-icon w-10 h-10 bg-white rounded-full flex items-center justify-center text-[#1e5bb8] text-xl">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </div>

    <div class="dropdown-overlay fixed inset-0 w-full h-full z-[99] hidden" onclick="toggleProfileDropdown()"></div>

    <div class="profile-dropdown fixed top-[70px] right-[15px] md:right-[30px] bg-white rounded-xl 
                shadow-[0_4px_12px_rgba(0,0,0,0.15)] min-w-[220px] opacity-0 invisible 
                -translate-y-2.5 transition-all duration-300 z-[101]">
        
        <div class="dropdown-header p-5 border-b border-gray-200 flex items-center gap-3">
            <div class="avatar w-[50px] h-[50px] bg-gradient-to-br from-[#1e5bb8] to-[#2d74da] 
                        rounded-full flex items-center justify-center text-xl text-white">
                <i class="fas fa-user"></i>
            </div>
            <div class="info">
                <div class="name text-[#2c3e50] text-[15px] font-semibold mb-[2px]">Reza Anu</div>
                <div class="role text-gray-500 text-[13px]">Administrator</div>
            </div>
        </div>
        
        <ul class="dropdown-menu list-none py-2.5">
            <li>
                <a href="#" class="flex items-center gap-3 py-3 px-5 text-[#2c3e50] no-underline transition-colors duration-200 text-sm hover:bg-gray-100">
                    <i class="fas fa-user-circle w-5 text-gray-500"></i> 
                    Profile Saya
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-3 py-3 px-5 text-[#2c3e50] no-underline transition-colors duration-200 text-sm hover:bg-gray-100">
                    <i class="fas fa-cog w-5 text-gray-500"></i> 
                    Pengaturan
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-3 py-3 px-5 text-[#2c3e50] no-underline transition-colors duration-200 text-sm hover:bg-gray-100">
                    <i class="fas fa-question-circle w-5 text-gray-500"></i> 
                    Bantuan
                </a>
            </li>
            <div class="dropdown-divider h-px bg-gray-200 my-2"></div>
            <li>
                <a href="#" class="logout flex items-center gap-3 py-3 px-5 no-underline transition-colors duration-200 text-sm hover:bg-gray-100 text-red-600">
                    <i class="fas fa-sign-out-alt w-5 text-red-600"></i> 
                    Logout
                </a>
            </li>
        </ul>
    </div>


    <script>
        function toggleSidebar() {
            const menuIcon = document.querySelector('.menu-icon');
            const iconElement = menuIcon.querySelector('i');
            
            menuIcon.classList.toggle('active');
            
            // Toggle icon antara bars dan bars-staggered
            if (menuIcon.classList.contains('active')) {
                iconElement.classList.remove('fa-bars');
                iconElement.classList.add('fa-bars-staggered');
            } else {
                iconElement.classList.remove('fa-bars-staggered');
                iconElement.classList.add('fa-bars');
            }
            
            // Fungsi ini akan terhubung dengan sidebar
            console.log('Toggle sidebar');
        }

        function toggleProfileDropdown() {
            const dropdown = document.querySelector('.profile-dropdown');
            const overlay = document.querySelector('.dropdown-overlay');
            
            dropdown.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userInfo = document.querySelector('.user-info');
            const dropdown = document.querySelector('.profile-dropdown');
            
            // Cek jika klik BUKAN di area user-info DAN BUKAN di dalam dropdown
            if (!userInfo.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
                document.querySelector('.dropdown-overlay').classList.remove('active');
            }
        });
    </script>
</body>
</html>