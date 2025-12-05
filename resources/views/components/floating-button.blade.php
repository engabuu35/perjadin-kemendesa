<!-- Modal Notifikasi - Positioned near FAB -->
<div id="notificationModal" class="hidden fixed bottom-24 right-6 transform transition-all duration-300 scale-95 opacity-0" style="z-index: 1005 !important;">
    <div class="bg-white rounded-xl shadow-2xl w-96 max-w-[calc(100vw-2rem)]" id="modalContent" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 gap-3">
            <h3 class="text-lg font-bold text-gray-900">
                Notifikasi
            </h3>
            <div class="flex items-center gap-2">
                <button onclick="refreshNotifications()" class="text-gray-500 hover:text-gray-700 transition-colors p-1.5 hover:bg-gray-100 rounded-full" title="Refresh" id="refreshBtn">
                    <i class="fas fa-sync-alt text-sm"></i>
                </button>
                <button onclick="closeNotificationModal()" class="text-gray-500 hover:text-gray-700 transition-colors p-1.5 hover:bg-gray-100 rounded-full" title="Tutup">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        
        <!-- Tab Filter -->
        <div class="flex gap-2 px-4 py-2.5 border-b border-gray-200 bg-gray-50">
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

        <!-- Content - Dynamic notification container -->
        <div id="notificationContainer" class="overflow-y-auto p-4 space-y-2" style="height: 45vh; max-height: 45vh;">
            <!-- Loading State -->
            <div id="loadingState" class="text-center flex flex-col items-center justify-center" style="height: 100%;">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
                </div>
                <h4 class="text-gray-600 font-semibold text-sm mb-1">Memuat Notifikasi...</h4>
            </div>
            
            <!-- Empty State (hidden by default) -->
            <div id="emptyState" class="hidden text-center flex flex-col items-center justify-center" style="height: 100%;">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-bell-slash text-gray-400 text-2xl"></i>
                </div>
                <h4 class="text-gray-600 font-semibold text-sm mb-1">Tidak Ada Notifikasi</h4>
                <p class="text-gray-500 text-xs">Semua notifikasi sudah dibaca</p>
            </div>
            
            <!-- Notifications will be rendered here -->
            <div id="notificationList"></div>
        </div>
        
        <!-- Footer -->
        <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
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
                        <span id="notifBadge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center shadow-lg">0</span>
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
                <!-- Badge Counter untuk tombol bintang -->
                <span id="mainBtnBadge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center shadow-lg">0</span>
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
        background: #f9fafb !important;
        opacity: 0.85;
        border-color: #e5e7eb !important;
    }

    .notif-card[data-read="true"] h4,
    .notif-card[data-read="true"] p,
    .notif-card[data-read="true"] span {
        color: #9ca3af !important;
    }

    /* ICON WRAPPER - Background abu-abu */
    .notif-card[data-read="true"] .icon-wrapper {
        background: #9ca3af !important; /* Abu-abu */
    }

    /* ICON - Warna putih */
    .notif-card[data-read="true"] .icon-wrapper i,
    .notif-card[data-read="true"] .icon-wrapper span {
        color: #ffffff !important; /* Putih */
    }

    .notif-card[data-read="true"] button {
        color: #9ca3af !important;
    }

    /* Notification card hover effect */
    .notif-card:hover {
        transform: translateX(2px);
    }

    /* Tambah margin antar card notifikasi */
    .notif-card {
        margin-bottom: 12px;
    }
    
    .notif-card:last-child {
        margin-bottom: 0;
    }

    /* Refresh button spinning animation */
    .refreshing .fa-sync-alt {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Line clamp untuk message - gunakan CSS untuk truncation */
    .notif-message {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-break: break-word;
        text-align: justify; /* Tambah text justify */
        margin-right: 24px; /* Tambah margin-right agar tidak terlalu dekat dengan tombol X */
    }

    .notif-message.expanded {
        display: block;
        -webkit-line-clamp: unset;
        overflow: visible;
    }

    /* Tombol selengkapnya */
    .read-more-btn {
        transition: all 0.2s ease;
    }

    .read-more-btn:hover {
        text-decoration: underline;
    }
</style>

<script>
    const API_BASE_URL = '/notifications';
    
    let allNotifications = [];
    let currentFilter = 'thisWeek';
    let expandedNotifications = new Set();

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
    const mainBtnBadge = document.getElementById('mainBtnBadge');
    
    let isOpen = false;

    // Array menu items untuk animasi bertahap
    const menuItems = [
        { element: notifWrapper, delay: 0 },
        { element: faqWrapper, delay: 80 }
    ];

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    async function fetchNotifications() {
        try {
            const response = await fetch(`${API_BASE_URL}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) throw new Error('Failed to fetch notifications');
            
            const data = await response.json();
            
            if (data.success) {
                allNotifications = data.data || [];
                updateNotificationBadge(data.unread_count || 0);
                renderNotifications();
                updateMainBtnBadge(data.unread_count || 0);
            }
        } catch (error) {
            console.error('Error fetching notifications:', error);
            showErrorState();
        }
    }

    async function fetchUnreadCount() {
        try {
            const response = await fetch(`${API_BASE_URL}/unread-count`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) throw new Error('Failed to fetch unread count');
            
            const data = await response.json();
            
            if (data.success) {
                updateNotificationBadge(data.count || 0);
                updateMainBtnBadge(data.count || 0);
            }
        } catch (error) {
            console.error('Error fetching unread count:', error);
        }
    }

    function getColorClass(color) {
        const colors = {
            'blue': 'bg-blue-500',
            'green': 'bg-green-500',
            'red': 'bg-red-500',
            'orange': 'bg-orange-500',
            'yellow': 'bg-yellow-500',
            'purple': 'bg-purple-500',
            'gray': 'bg-gray-500'
        };
        return colors[color] || 'bg-blue-500';
    }

    function getNotificationPeriod(createdAt) {
        const now = new Date();
        const notifDate = new Date(createdAt);
        const diffTime = Math.abs(now - notifDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        // Get start of this week (Sunday)
        const startOfWeek = new Date(now);
        startOfWeek.setDate(now.getDate() - now.getDay());
        startOfWeek.setHours(0, 0, 0, 0);
        
        // Get start of last week
        const startOfLastWeek = new Date(startOfWeek);
        startOfLastWeek.setDate(startOfLastWeek.getDate() - 7);
        
        if (notifDate >= startOfWeek) {
            return 'thisWeek';
        } else if (notifDate >= startOfLastWeek) {
            return 'lastWeek';
        } else {
            return 'earlier';
        }
    }

    function needsReadMore(text) {
        // Estimasi: jika lebih dari 80 karakter, kemungkinan besar akan terpotong dengan line-clamp-2
        return text.length > 80;
    }

    function toggleExpand(notifId, event) {
        event.stopPropagation();
        
        if (expandedNotifications.has(notifId)) {
            expandedNotifications.delete(notifId);
        } else {
            expandedNotifications.add(notifId);
        }
        
        const messageEl = document.querySelector(`.notif-message[data-id="${notifId}"]`);
        const btnEl = document.querySelector(`.read-more-btn[data-id="${notifId}"]`);
        
        if (messageEl && btnEl) {
            if (expandedNotifications.has(notifId)) {
                messageEl.classList.add('expanded');
                btnEl.textContent = 'Sembunyikan';
            } else {
                messageEl.classList.remove('expanded');
                btnEl.textContent = 'Selengkapnya';
            }
        }
    }

    function renderNotificationCard(notification) {
        const period = getNotificationPeriod(notification.created_at);
        const colorClass = getColorClass(notification.color);
        const isRead = notification.is_read;
        const actionUrl = notification.action_url || '';
        const isExpanded = expandedNotifications.has(notification.id);
        
        const showReadMore = needsReadMore(notification.message);
        
        return `
            <div class="notif-card bg-white rounded-lg p-3 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer" 
                 data-id="${notification.id}" 
                 data-read="${isRead}"
                 data-period="${period}"
                 onclick="handleNotificationClick(${notification.id}, '${actionUrl.replace(/'/g, "\\'")}')"
                 style="display: ${period === currentFilter ? 'block' : 'none'};">
                <div class="flex items-start gap-3">
                    <div class="icon-wrapper w-8 h-8 ${colorClass} rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-sm">${notification.icon}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="text-sm font-semibold text-gray-900 leading-tight">${notification.title}</h4>
                            <button onclick="event.stopPropagation(); deleteNotification(${notification.id})" 
                                    class="text-gray-400 hover:text-red-500 transition-colors p-1 -mr-1 -mt-2.5 flex-shrink-0" 
                                    title="Hapus">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <!-- Selalu tampilkan teks penuh, CSS line-clamp yang handle truncation -->
                        <p class="notif-message text-xs text-gray-600 mt-1 ${isExpanded ? 'expanded' : ''}" data-id="${notification.id}">${notification.message}</p>
                        ${showReadMore ? `
                            <button onclick="toggleExpand(${notification.id}, event)" 
                                    class="read-more-btn text-xs text-blue-600 hover:text-blue-700 font-medium mt-1" 
                                    data-id="${notification.id}">
                                ${isExpanded ? 'Sembunyikan' : 'Selengkapnya'}
                            </button>
                        ` : ''}
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-400">${notification.time_ago}</span>
                            ${!isRead ? '<span class="w-2 h-2 bg-blue-500 rounded-full"></span>' : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function renderNotifications() {
        const loadingState = document.getElementById('loadingState');
        const emptyState = document.getElementById('emptyState');
        const notificationList = document.getElementById('notificationList');
        
        // Hide loading
        loadingState.classList.add('hidden');
        
        // Filter notifications by current period
        const filteredNotifications = allNotifications.filter(n => 
            getNotificationPeriod(n.created_at) === currentFilter
        );
        
        if (filteredNotifications.length === 0) {
            emptyState.classList.remove('hidden');
            notificationList.innerHTML = '';
        } else {
            emptyState.classList.add('hidden');
            notificationList.innerHTML = allNotifications.map(n => renderNotificationCard(n)).join('');
        }
    }

    function filterNotifications(period) {
        currentFilter = period;
        
        // Update tab styles
        document.querySelectorAll('.tab-btn').forEach(btn => {
            if (btn.dataset.tab === period) {
                btn.classList.add('bg-blue-600', 'text-white');
                btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
            } else {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('text-gray-600', 'hover:bg-gray-100');
            }
        });
        
        // Show/hide notifications based on period
        document.querySelectorAll('.notif-card').forEach(card => {
            if (card.dataset.period === period) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Check if there are any visible notifications
        const visibleCards = document.querySelectorAll(`.notif-card[data-period="${period}"]`);
        const emptyState = document.getElementById('emptyState');
        
        if (visibleCards.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }

    function updateNotificationBadge(count) {
        const badge = document.getElementById('notifBadge');
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    function updateMainBtnBadge(count) {
        if (count > 0) {
            mainBtnBadge.textContent = count > 99 ? '99+' : count;
            mainBtnBadge.classList.remove('hidden');
        } else {
            mainBtnBadge.classList.add('hidden');
        }
    }

    async function handleNotificationClick(id, actionUrl) {
        await markAsRead(id);
        if (actionUrl) {
            window.location.href = actionUrl;
        }
    }

    async function markAsRead(id) {
        try {
            const response = await fetch(`${API_BASE_URL}/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                const card = document.querySelector(`.notif-card[data-id="${id}"]`);
                if (card) {
                    card.dataset.read = 'true';
                    const dot = card.querySelector('.bg-blue-500.rounded-full');
                    if (dot) dot.remove();
                }
                fetchUnreadCount();
            }
        } catch (error) {
            console.error('Error marking as read:', error);
        }
    }

    async function markAllAsRead() {
        try {
            const response = await fetch(`${API_BASE_URL}/read-all`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                document.querySelectorAll('.notif-card').forEach(card => {
                    card.dataset.read = 'true';
                    const dot = card.querySelector('.bg-blue-500.rounded-full');
                    if (dot) dot.remove();
                });
                updateNotificationBadge(0);
                updateMainBtnBadge(0);
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    }

    async function deleteNotification(id) {
        try {
            const response = await fetch(`${API_BASE_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                const card = document.querySelector(`.notif-card[data-id="${id}"]`);
                if (card) {
                    card.remove();
                }
                allNotifications = allNotifications.filter(n => n.id !== id);
                fetchUnreadCount();
                
                // Check if list is empty
                const visibleCards = document.querySelectorAll(`.notif-card[data-period="${currentFilter}"]`);
                if (visibleCards.length === 0) {
                    document.getElementById('emptyState').classList.remove('hidden');
                }
            }
        } catch (error) {
            console.error('Error deleting notification:', error);
        }
    }

    function refreshNotifications() {
        const refreshBtn = document.getElementById('refreshBtn');
        refreshBtn.classList.add('refreshing');
        
        const loadingState = document.getElementById('loadingState');
        const notificationList = document.getElementById('notificationList');
        
        loadingState.classList.remove('hidden');
        notificationList.innerHTML = '';
        
        fetchNotifications().finally(() => {
            setTimeout(() => {
                refreshBtn.classList.remove('refreshing');
            }, 500);
        });
    }

    function showErrorState() {
        const loadingState = document.getElementById('loadingState');
        const notificationList = document.getElementById('notificationList');
        
        loadingState.classList.add('hidden');
        notificationList.innerHTML = `
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
                <h4 class="text-gray-600 font-semibold text-sm mb-1">Gagal Memuat</h4>
                <p class="text-gray-500 text-xs mb-3">Terjadi kesalahan saat memuat notifikasi</p>
                <button onclick="refreshNotifications()" class="text-blue-600 text-xs font-semibold hover:underline">
                    <i class="fas fa-redo mr-1"></i> Coba Lagi
                </button>
            </div>
        `;
    }

    // Toggle menu
    function toggleMenu() {
        if (isOpen) {
            closeMenu();
        } else {
            openMenu();
        }
    }

    function openMenu() {
        isOpen = true;
        backdrop.classList.remove('hidden');
        
        // Rotate icon
        mainIcon.style.transform = 'rotate(180deg)';
        
        // Hide badge when menu is open
        mainBtnBadge.classList.add('hidden');
        
        // Expand background
        menuBackground.style.height = '180px';
        menuBackground.style.opacity = '1';
        
        // Animate menu items with stagger
        menuItems.forEach((item, index) => {
            setTimeout(() => {
                item.element.style.opacity = '1';
                item.element.style.transform = 'scale(1)';
            }, item.delay);
        });
        
        // Move container up
        menuItemsContainer.style.bottom = '80px';
    }

    function closeMenu() {
        isOpen = false;
        backdrop.classList.add('hidden');
        
        // Reset icon rotation only
        mainIcon.style.transform = 'rotate(0deg)';
        
        // Show badge again if there are unread notifications
        fetchUnreadCount();
        
        // Collapse background
        menuBackground.style.height = '0';
        menuBackground.style.opacity = '0';
        
        // Hide menu items with reverse stagger
        [...menuItems].reverse().forEach((item, index) => {
            setTimeout(() => {
                item.element.style.opacity = '0';
                item.element.style.transform = 'scale(0.5)';
            }, index * 50);
        });
        
        // Move container down
        menuItemsContainer.style.bottom = '0';
    }

    function openNotificationModal() {
        const modal = document.getElementById('notificationModal');
        modal.classList.remove('hidden');
        
        // Trigger animation
        setTimeout(() => {
            modal.classList.remove('scale-95', 'opacity-0');
            modal.classList.add('scale-100', 'opacity-100');
        }, 10);
        
        // Fetch notifications
        fetchNotifications();
        
        // Close menu
        closeMenu();
    }

    function closeNotificationModal() {
        const modal = document.getElementById('notificationModal');
        modal.classList.remove('scale-100', 'opacity-100');
        modal.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Event listeners
    mainBtn.addEventListener('click', toggleMenu);
    notifBtn.addEventListener('click', openNotificationModal);
    faqBtn.addEventListener('click', () => {
        window.location.href = '/faq';
    });

    // Close modal when clicking outside
    document.addEventListener('click', (e) => {
        const modal = document.getElementById('notificationModal');
        if (!modal.classList.contains('hidden') && !modal.contains(e.target) && !notifBtn.contains(e.target)) {
            closeNotificationModal();
        }
    });

    // Initial fetch for unread count
    fetchUnreadCount();
</script>
