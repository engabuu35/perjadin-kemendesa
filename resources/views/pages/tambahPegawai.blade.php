<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPERDIN - Tambah Pegawai</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .navbar-gradient {
            background: linear-gradient(135deg, #1e5bb8 0%, #2d74da 100%);
        }
        
        .sidebar-gradient {
            background: linear-gradient(180deg, #2d5ba8 0%, #1e4b98 100%);
        }
        
        .bg-pattern {
            background-color: #e8e9f0;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><circle cx="10" cy="10" r="2" fill="%23d0d0dc" opacity="0.3"/></svg>');
            background-repeat: repeat;
        }
        
        .sidebar {
            transition: width 0.3s ease;
        }
        
        .sidebar-text {
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .sidebar.active .sidebar-text {
            opacity: 1;
        }
    </style>
</head>
<body class="bg-pattern pt-20">
    <!-- Navbar -->
    <nav class="navbar-gradient fixed top-0 left-0 right-0 z-50 px-8 py-4 rounded-b-[30px] shadow-lg flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Menu Icon -->
            <div class="w-6 h-5 flex flex-col justify-between cursor-pointer" onclick="toggleSidebar()">
                <div class="w-full h-0.5 bg-white rounded transition-all hover:bg-gray-200"></div>
                <div class="w-full h-0.5 bg-white rounded transition-all hover:bg-gray-200"></div>
                <div class="w-full h-0.5 bg-white rounded transition-all hover:bg-gray-200"></div>
            </div>
            
            <!-- Logo -->
            <div class="text-white text-3xl font-bold italic tracking-wide">SIPERDIN</div>
        </div>
        
        <!-- User Info -->
        <div class="flex items-center gap-4 cursor-pointer px-3 py-1.5 rounded-full transition-all hover:bg-white/10 active:bg-black/20 text-white" onclick="toggleProfileDropdown()">
            <span class="text-sm hidden md:block">Reza Anu</span>
            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-blue-700">
                <i class="fas fa-user text-sm"></i>
            </div>
        </div>
    </nav>

    <!-- Dropdown Overlay -->
    <div class="fixed inset-0 z-40 hidden" id="dropdownOverlay" onclick="toggleProfileDropdown()"></div>

    <!-- Profile Dropdown -->
    <div id="profileDropdown" class="fixed top-[70px] right-8 bg-white rounded-xl shadow-lg min-w-[220px] opacity-0 invisible transform -translate-y-2 transition-all z-50">
        <div class="p-5 border-b border-gray-200 flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-400 rounded-full flex items-center justify-center text-white text-xl">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <div class="text-gray-800 text-sm font-semibold">Reza Anu</div>
                <div class="text-gray-500 text-xs">Administrator</div>
            </div>
        </div>
        <ul class="py-2">
            <li><a href="#" class="flex items-center gap-3 px-5 py-3 text-gray-800 hover:bg-gray-100 transition text-sm">
                <i class="fas fa-user-circle w-5 text-gray-500"></i> Profile Saya
            </a></li>
            <li><a href="#" class="flex items-center gap-3 px-5 py-3 text-gray-800 hover:bg-gray-100 transition text-sm">
                <i class="fas fa-cog w-5 text-gray-500"></i> Pengaturan
            </a></li>
            <li><a href="#" class="flex items-center gap-3 px-5 py-3 text-gray-800 hover:bg-gray-100 transition text-sm">
                <i class="fas fa-question-circle w-5 text-gray-500"></i> Bantuan
            </a></li>
            <li class="h-px bg-gray-200 my-2"></li>
            <li><a href="#" class="flex items-center gap-3 px-5 py-3 text-red-600 hover:bg-gray-100 transition text-sm">
                <i class="fas fa-sign-out-alt w-5"></i> Logout
            </a></li>
        </ul>
    </div>

    <!-- Overlay for Sidebar -->
    <div id="overlay" class="fixed inset-0 bg-black/50 opacity-0 invisible transition-all z-30" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar-gradient fixed left-0 top-[100px] w-20 h-[calc(100vh-80px)] z-40 pt-5 rounded-r-[30px] shadow-xl overflow-hidden sidebar">
        <ul class="py-5">
            <li class="my-2">
                <a href="#" class="flex items-center px-8 py-4 text-white hover:bg-white/10 transition gap-4">
                    <span class="w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fas fa-home"></i>
                    </span>
                    <span class="sidebar-text whitespace-nowrap">Beranda</span>
                </a>
            </li>
            <li class="my-2">
                <a href="#" class="flex items-center px-8 py-4 text-white hover:bg-white/10 transition gap-4">
                    <span class="w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-briefcase"></i>
                    </span>
                    <span class="sidebar-text whitespace-nowrap">Penugasan</span>
                </a>
            </li>
            <li class="my-2">
                <a href="#" class="flex items-center px-8 py-4 text-white hover:bg-white/10 transition gap-4">
                    <span class="w-6 h-6 flex items-center justify-center text-xl">
                        <i class="fas fa-history"></i>
                    </span>
                    <span class="sidebar-text whitespace-nowrap">Riwayat</span>
                </a>
            </li>
        </ul>

        <!-- User Profile at Bottom -->
        <div class="absolute bottom-8 left-0 right-0 px-4 pt-5 border-t border-white/20 flex flex-col items-center">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-white text-2xl mb-2">
                <i class="fas fa-user"></i>
            </div>
            <div class="sidebar-text text-white text-sm font-bold mb-1 whitespace-nowrap text-center">Fernanda Aditia Putra</div>
            <div class="sidebar-text text-white/70 text-xs mb-4 whitespace-nowrap">NPM: 2021011</div>
            <button class="sidebar-text flex items-center justify-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-white text-sm w-full transition">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="max-w-lg mx-auto px-5 py-8">
        <h2 class="text-gray-700 text-2xl font-bold mb-5 pb-4 relative">
            Tambah Pegawai
            <span class="absolute bottom-0 left-0 w-48 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
        </h2>
        
        <div class="bg-white rounded-xl p-8 shadow">
            <form>
                <div class="mb-5">
                    <label for="nama" class="block text-gray-700 text-sm font-medium mb-2">Nama Pegawai</label>
                    <input type="text" id="nama" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                </div>

                <div class="mb-5">
                    <label for="nip" class="block text-gray-700 text-sm font-medium mb-2">NIP</label>
                    <input type="text" id="nip" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                </div>

                <div class="mb-5">
                    <label for="jabatan" class="block text-gray-700 text-sm font-medium mb-2">Jabatan</label>
                    <input type="text" id="jabatan" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                </div>

                <div class="mb-5">
                    <label for="uke1" class="block text-gray-700 text-sm font-medium mb-2">Nama UKE-1</label>
                    <input type="text" id="uke1" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                </div>

                <div class="mb-5">
                    <label for="uke2" class="block text-gray-700 text-sm font-medium mb-2">Nama UKE-2</label>
                    <input type="text" id="uke2" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                </div>

                <div class="mb-5">
                    <label for="pangkat" class="block text-gray-700 text-sm font-medium mb-2">Pangkat Golongan</label>
                    <input type="text" id="pangkat" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                </div>

                <div class="flex flex-col gap-3 mt-8">
                    <button type="button" class="w-full py-3.5 bg-gray-300 text-gray-600 rounded-lg font-semibold hover:bg-gray-400 transition">
                        Batal
                    </button>
                    <button type="submit" class="w-full py-3.5 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                        Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const dropdown = document.getElementById('profileDropdown');
            const dropdownOverlay = document.getElementById('dropdownOverlay');
            
            // Close dropdown if open
            dropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
            dropdown.classList.add('opacity-0', 'invisible', '-translate-y-2');
            dropdownOverlay.classList.add('hidden');
            
            // Toggle sidebar
            if (sidebar.classList.contains('active')) {
                sidebar.classList.remove('active', 'w-64');
                sidebar.classList.add('w-20');
                overlay.classList.remove('opacity-100', 'visible');
                overlay.classList.add('opacity-0', 'invisible');
            } else {
                sidebar.classList.add('active', 'w-64');
                sidebar.classList.remove('w-20');
                overlay.classList.remove('opacity-0', 'invisible');
                overlay.classList.add('opacity-100', 'visible');
            }
        }

        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            const overlay = document.getElementById('dropdownOverlay');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('overlay');
            
            // Close sidebar if open
            sidebar.classList.remove('active', 'w-64');
            sidebar.classList.add('w-20');
            sidebarOverlay.classList.remove('opacity-100', 'visible');
            sidebarOverlay.classList.add('opacity-0', 'invisible');
            
            // Toggle dropdown
            if (dropdown.classList.contains('opacity-100')) {
                dropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
                dropdown.classList.add('opacity-0', 'invisible', '-translate-y-2');
                overlay.classList.add('hidden');
            } else {
                dropdown.classList.remove('opacity-0', 'invisible', '-translate-y-2');
                dropdown.classList.add('opacity-100', 'visible', 'translate-y-0');
                overlay.classList.remove('hidden');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userInfo = event.target.closest('[onclick="toggleProfileDropdown()"]');
            const dropdown = document.getElementById('profileDropdown');
            
            if (!userInfo && !dropdown.contains(event.target)) {
                dropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
                dropdown.classList.add('opacity-0', 'invisible', '-translate-y-2');
                document.getElementById('dropdownOverlay').classList.add('hidden');
            }
        });
    </script>
</body>
</html>