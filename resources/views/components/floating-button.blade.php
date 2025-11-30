<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Floating Action Button</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Modal Notifikasi - Positioned near FAB -->
<div id="notificationModal" class="hidden fixed bottom-24 right-6 transform transition-all duration-300 scale-95 opacity-0" style="z-index: 1005 !important;">
    <div class="bg-white rounded-xl shadow-2xl w-80 max-w-[calc(100vw-3rem)]" id="modalContent" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">
                Notifikasi
            </h3>
            <div class="flex items-center gap-2">
                <button onclick="refreshNotifications()" class="text-gray-500 hover:text-gray-700 transition-colors p-1.5 hover:bg-gray-100 rounded-full" title="Refresh">
                    <i class="fas fa-sync-alt text-sm"></i>
                </button>
                <button onclick="closeNotificationModal()" class="text-gray-500 hover:text-gray-700 transition-colors p-1.5 hover:bg-gray-100 rounded-full" title="Tutup">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        
        <!-- Tab Filter -->
        <div class="flex gap-2 px-3 py-2 border-b border-gray-200 bg-gray-50">
            <button onclick="filterNotifications('thisWeek')" class="tab-btn flex-1 py-2 px-3 text-xs font-semibold rounded-lg transition-all bg-blue-600 text-white" data-tab="thisWeek">
                This Week
            </button>
            <button onclick="filterNotifications('lastWeek')" class="tab-btn flex-1 py-2 px-3 text-xs font-semibold rounded-lg transition-all text-gray-600 hover:bg-gray-100" data-tab="lastWeek">
                Last Week
            </button>
            <button onclick="filterNotifications('earlier')" class="tab-btn flex-1 py-2 px-3 text-xs font-semibold rounded-lg transition-all text-gray-600 hover:bg-gray-100" data-tab="earlier">
                Earlier
            </button>
        </div>

        <!-- Content -->
        <div class="overflow-y-auto p-3 space-y-2.5" style="height: 45vh; max-height: 45vh;">
            <!-- Empty State -->
            <div class="text-center flex flex-col items-center justify-center" style="height: 100%;">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-bell-slash text-gray-400 text-2xl"></i>
                </div>
                <h4 class="text-gray-600 font-semibold text-sm mb-1">Tidak Ada Notifikasi</h4>
                <p class="text-gray-500 text-xs">Semua notifikasi sudah dibaca</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="p-3 border-t border-gray-200 bg-gray-50 rounded-b-xl">
            <button onclick="markAllAsRead()" class="w-full bg-blue-600 text-white py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-semibold text-sm shadow-sm hover:shadow-md">
                <i class="fas fa-check-double mr-1.5 text-xs"></i>
                Tandai Semua Sudah Dibaca
            </button>
        </div>
    </div>
</div>

<!-- Floating Action Button dengan Menu Vertikal -->
<div class="fixed bottom-6 right-6" style="z-index: 1005 !important;">
    <div class="relative">
        <!-- Container Background -->
        <div 
            id="menuBackground"
            class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-white rounded-full shadow-2xl transition-all duration-300 ease-out h-0 w-20 opacity-0"
        ></div>

        <!-- Menu Items Container -->
        <div id="menuItemsContainer" class="absolute left-1/2 -translate-x-1/2 transition-all duration-300 bottom-0">
            <!-- Tombol FAQ -->
            <div id="faqWrapper" class="mb-4 transition-all duration-300 opacity-0 scale-50">
                <div class="relative group">
                    <!-- Tooltip -->
                    <span class="absolute right-20 top-1/2 -translate-y-1/2 bg-gray-800 text-white text-xs px-3 py-2 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-lg">
                        FAQ
                    </span>
                    <!-- Button -->
                    <button 
                        id="faqBtn" 
                        class="w-14 h-14 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-300 hover:scale-110 relative z-10"
                        style="background: linear-gradient(to bottom, #4A7FD0, #3A6FBD);"
                        aria-label="FAQ">
                        <i class="fas fa-question text-xl transition-all duration-300"></i>
                    </button>
                </div>
            </div>

            <!-- Tombol Notifikasi -->
            <div id="notifWrapper" class="mb-4 transition-all duration-300 opacity-0 scale-50">
                <div class="relative group">
                    <!-- Tooltip -->
                    <span class="absolute right-20 top-1/2 -translate-y-1/2 bg-gray-800 text-white text-xs px-3 py-2 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-lg">
                        Notifikasi
                    </span>
                    <!-- Button -->
                    <button 
                        id="notifBtn" 
                        class="w-14 h-14 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-300 hover:scale-110 relative z-10"
                        style="background: linear-gradient(to bottom, #4A7FD0, #3A6FBD);"
                        aria-label="Notifikasi">
                        <i class="fas fa-bell text-xl transition-all duration-300"></i>
                        <!-- Badge Counter -->
                        <span id="notifBadge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center shadow-lg animate-pulse">0</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tombol Utama -->
        <div class="relative group z-20">
            <!-- Tooltip untuk tombol utama -->
            <span class="absolute right-16 top-1/2 -translate-y-1/2 bg-gradient-to-r from-gray-800 to-gray-900 text-white text-xs font-medium px-3 py-2 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none shadow-xl border border-gray-700">
                <span class="flex items-center gap-2">
                    Bantuan dan Notifikasi
                </span>
                <!-- Arrow -->
                <span class="absolute right-[-6px] top-1/2 -translate-y-1/2 w-0 h-0 border-t-[6px] border-t-transparent border-b-[6px] border-b-transparent border-l-[6px] border-l-gray-900"></span>
            </span>
            <!-- Button -->
            <button 
                id="mainBtn" 
                class="w-14 h-14 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-300 relative z-10 hover:scale-105"
                style="background: linear-gradient(to bottom, #366bbcff, #3A6FBD);"
                aria-label="Menu Utama">
                <i id="mainIcon" class="fas fa-star text-xl transition-all duration-500"></i>
            </button>
        </div>

        <!-- Backdrop -->
        <div 
            id="backdrop"
            class="hidden fixed inset-0 -z-10"
            onclick="closeMenu()"
        ></div>
    </div>
</div>

<style>
    /* Efek hover untuk tombol FAQ */
    #faqBtn:hover {
        background: linear-gradient(to bottom, #5A8FE0, #4A7FD0) !important;
    }

    /* Efek hover untuk tombol Notifikasi */
    #notifBtn:hover {
        background: linear-gradient(to bottom, #5A8FE0, #4A7FD0) !important;
    }

    /* Efek hover untuk tombol utama */
    #mainBtn:hover {
        background: linear-gradient(to bottom, #4A7FD0, #366bbcff) !important;
    }

    /* Custom Scrollbar */
    #modalContent > div::-webkit-scrollbar {
        width: 4px;
    }

    #modalContent > div::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #modalContent > div::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 10px;
    }

    #modalContent > div::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    /* Tab Button Styles */
    .tab-btn {
        transition: all 0.3s ease;
    }

    .tab-btn.active {
        background: #2563eb;
        color: white;
    }

    /* Notifikasi yang sudah dibaca */
    .notif-card[data-read="true"] {
        background: #f3f4f6 !important;
        opacity: 0.7;
        border-color: #d1d5db !important;
    }

    .notif-card[data-read="true"] h4,
    .notif-card[data-read="true"] p,
    .notif-card[data-read="true"] span {
        color: #9ca3af !important;
    }

    .notif-card[data-read="true"] .w-8 {
        background: #9ca3af !important;
    }

    .notif-card[data-read="true"] button {
        color: #9ca3af !important;
    }
</style>

<script>
    // Inisialisasi elemen DOM
    const mainBtn = document.getElementById('mainBtn');
    const mainIcon = document.getElementById('mainIcon');
    const menuBackground = document.getElementById('menuBackground');
    const menuItemsContainer = document.getElementById('menuItemsContainer');
    const faqWrapper = document.getElementById('faqWrapper');
    const notifWrapper = document.getElementById('notifWrapper');
    const backdrop = document.getElementById('backdrop');
    const faqBtn = document.getElementById('faqBtn');
    const notifBtn = document.getElementById('notifBtn');
    
    let isOpen = false;

    // Array menu items untuk animasi bertahap
    const menuItems = [
        { element: notifWrapper, delay: 0 },    // Muncul pertama (paling bawah)
        { element: faqWrapper, delay: 80 }      // Muncul kedua (paling atas)
    ];

    // Fungsi toggle menu
    function toggleMenu() {
        isOpen = !isOpen;
        
        if (isOpen) {
            openMenu();
        } else {
            closeMenu();
        }
    }

    // Fungsi buka menu
    function openMenu() {
        isOpen = true;
        
        // Rotasi ikon utama
        mainIcon.style.transform = 'rotate(45deg)';
        
        // Tampilkan backdrop
        backdrop.classList.remove('hidden');
        
        // Animasi background container
        menuBackground.style.height = '220px';
        menuBackground.style.opacity = '1';
        
        // Pindahkan container menu items ke atas
        menuItemsContainer.style.bottom = '70px';
        
        // Animasi menu items dari bawah ke atas dengan delay
        menuItems.forEach((item, index) => {
            setTimeout(() => {
                item.element.style.opacity = '1';
                item.element.style.transform = 'scale(1)';
            }, item.delay);
        });
    }

    // Fungsi tutup menu
    function closeMenu() {
        isOpen = false;
        
        // Rotasi ikon utama kembali
        mainIcon.style.transform = 'rotate(0deg)';
        
        // Sembunyikan backdrop
        backdrop.classList.add('hidden');
        
        // Animasi background container
        menuBackground.style.height = '0';
        menuBackground.style.opacity = '0';
        
        // Kembalikan posisi container menu items
        menuItemsContainer.style.bottom = '0';
        
        // Animasi menu items dari atas ke bawah dengan delay (reverse)
        const reversedItems = [...menuItems].reverse();
        reversedItems.forEach((item, index) => {
            setTimeout(() => {
                item.element.style.opacity = '0';
                item.element.style.transform = 'scale(0.5)';
            }, index * 80);
        });
    }

    // Event listeners
    mainBtn.addEventListener('click', toggleMenu);

    faqBtn.addEventListener('click', function() {
        window.location.href = '/laman-bantuan';
    });

    notifBtn.addEventListener('click', function() {
        openNotificationModal();
    });

    // Fungsi untuk membuka modal notifikasi
    function openNotificationModal() {
        const modal = document.getElementById('notificationModal');
        const badge = document.getElementById('notifBadge');
        
        // Tampilkan modal
        modal.classList.remove('hidden');
        
        // Trigger animation
        setTimeout(() => {
            modal.classList.remove('scale-95', 'opacity-0');
            modal.classList.add('scale-100', 'opacity-100');
        }, 10);

        // Sembunyikan badge saat modal dibuka
        if (badge) {
            badge.classList.add('opacity-0');
            setTimeout(() => {
                badge.classList.add('hidden');
            }, 300);
        }

        // Tutup menu FAB jika terbuka
        if (isOpen) {
            closeMenu();
        }

        // Tambahkan event listener untuk menutup modal saat klik di luar
        setTimeout(() => {
            document.addEventListener('click', closeModalOnClickOutside);
        }, 100);
    }

    // Fungsi untuk menutup modal saat klik di luar
    function closeModalOnClickOutside(event) {
        const modal = document.getElementById('notificationModal');
        const modalContent = document.getElementById('modalContent');
        
        // Cek apakah klik di luar modal content dan bukan tombol notif
        if (modal && !modal.classList.contains('hidden') && 
            !modalContent.contains(event.target) && 
            event.target.id !== 'notifBtn' &&
            !event.target.closest('#notifBtn')) {
            closeNotificationModal();
        }
    }

    // Fungsi untuk menutup modal notifikasi
    function closeNotificationModal() {
        const modal = document.getElementById('notificationModal');
        
        // Tutup modal
        modal.classList.remove('scale-100', 'opacity-100');
        modal.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);

        // Hapus event listener
        document.removeEventListener('click', closeModalOnClickOutside);
    }

    // Fungsi untuk tandai semua sebagai dibaca
    function markAllAsRead() {
        const badge = document.getElementById('notifBadge');
        if (badge) {
            badge.classList.add('hidden');
        }
        
        // Tandai semua notifikasi sebagai dibaca
        const allNotifs = document.querySelectorAll('.notif-card');
        allNotifs.forEach(notif => {
            notif.setAttribute('data-read', 'true');
        });
        
        alert('Semua notifikasi telah ditandai sebagai dibaca');
        
        // Update badge menjadi 0
        updateNotificationBadge(0);
    }

    // Fungsi refresh notifikasi
    function refreshNotifications() {
        alert('Refresh notifikasi...');
    }

    // Fungsi filter notifikasi berdasarkan periode
    let currentFilter = 'thisWeek';
    
    function filterNotifications(period) {
        currentFilter = period;
        
        // Update active tab
        const tabs = document.querySelectorAll('.tab-btn');
        tabs.forEach(tab => {
            if (tab.dataset.tab === period) {
                tab.classList.add('active', 'bg-blue-600', 'text-white');
                tab.classList.remove('text-gray-600', 'hover:bg-gray-100');
            } else {
                tab.classList.remove('active', 'bg-blue-600', 'text-white');
                tab.classList.add('text-gray-600', 'hover:bg-gray-100');
            }
        });
        
        // Filter notifikasi cards
        const notifications = document.querySelectorAll('[data-period]');
        notifications.forEach(notif => {
            if (notif.dataset.period === period) {
                notif.style.display = 'block';
            } else {
                notif.style.display = 'none';
            }
        });
    }

    // Update badge counter (panggil fungsi ini dari Laravel)
    function updateNotificationBadge(count) {
        const badge = document.getElementById('notifBadge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    }

    // Demo: Set badge counter untuk testing
    // Uncomment baris di bawah untuk testing
    // updateNotificationBadge(5);
</script>

</body>
</html>