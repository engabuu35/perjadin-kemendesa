<style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Sidebar Active State */
        .sidebar.active {
            width: 210px;
        }

        .sidebar.active .sidebar-menu a span:last-child {
            flex: 0;
            text-align: center;
            opacity: 1;
        }

        .sidebar.active .user-profile {
            align-items: center;
            padding-left: 20px;
            padding-right: 20px;
            padding-top: 12px;
            padding-bottom: 20px;
            bottom: 15px;
            margin-bottom: 0;
        }

        /* Tambahkan setelah line 42 */
        .sidebar:not(.active) .user-profile {
            bottom: -10px;
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
                url('/img/sidebar-pattern.png'),
                linear-gradient(to bottom, #2954B0, #24519D);
            background-repeat: repeat, no-repeat;
            background-size: 200px, cover;
            background-position: 7px 20px, center;
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
            width: 42px;
            height: 42px;
            opacity: 1;
        }
        
        .sidebar-menu a:active::before {
            width: 42px;
            height: 42px;
            opacity: 1;
        }

        .sidebar-menu a.active::before {
            content: "";
            position: absolute;
            width: 38px;
            height: 38px;
            background: #ffffffd0;
            border-radius: 50%;
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
            justify-content: flex-start;
            padding-left: 50px;
            padding-right: 30px;
        }

        .sidebar.active .sidebar-menu a:hover::before {
            width: calc(100% - 30px);
            height: 40px;
            border-radius: 15px;
            opacity: 1;
        }
        
        .sidebar.active .sidebar-menu a:active::before {
            width: calc(100% - 30px);
            height: 40px;
            border-radius: 15px;
            opacity: 1;
        }

        .sidebar.active .sidebar-menu a.active::before {
            width: calc(100% - 30px);
            height: 40px;
            border-radius: 15px;
            opacity: 1;
        }

        /* Ripple Animation */
        @keyframes ripple-effect-1 {
            0% {
                width: 0;
                height: 0;
                opacity: 1;
            }
            100% {
                width: 100px;
                height: 100px;
                opacity: 0;
            }
        }

        .sidebar-menu a .icon {
            position: relative;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: color 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 30;
        }

        .sidebar-menu a:hover .icon {
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
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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
            width: 180px;
            height: 180px;
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

        /* Mobile: Sidebar keluar dari kiri */
        @media (max-width: 640px) {
            .sidebar {
                transform: translateX(-100%);
                display: block !important; /* Override hidden */
                top: 60px !important; /* Turun dari top navbar (50px tinggi navbar) */
                height: calc(100vh - 60px) !important; /* Kurangi tinggi sesuai navbar */
                padding-top: 1rem; /* Tambah padding top */
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
        }

        /* Ripple effect animation */
        @keyframes ripple-effect-1 {
            to {
                width: 500px;
                height: 500px;
                opacity: 0;
            }
        }
    </style>

                <!-- Modal Konfirmasi Logout -->
                <div id="logoutModal" class="fixed inset-0 bg-black/50 flex items-center justify-center opacity-0 invisible transition-opacity duration-300 z-[999]">
                    <div class="bg-white rounded-lg shadow-lg w-[90%] max-w-sm p-5 text-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-2 text-gray-800">Konfirmasi Logout</h3>
                        <p class="text-gray-600 mb-6">Apakah kamu yakin ingin keluar dari akun ini?</p>
                        <div class="flex justify-between gap-3">
                            <button id="cancelLogout" class="flex-1 py-2 px-4 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                                Batal
                            </button>
                            <button id="confirmLogout" class="flex-1 py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Logout
                            </button>
                        </div>
                    </div>
                </div>