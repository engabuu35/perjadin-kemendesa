<script>
    const sidebar = document.querySelector('.sidebar');
    const logoutForm = document.getElementById('logoutForm');
    const logoutModal = document.getElementById('logoutModal');
    const cancelLogout = document.getElementById('cancelLogout');
    const confirmLogout = document.getElementById('confirmLogout');
    const sidebarEl = document.querySelector('.sidebar');
    const avatarBtn = document.getElementById('avatarBtn');
    const profilePopup = document.getElementById('profilePopup');
    const popupLogout = document.getElementById('popupLogout');

    // Handle SEMUA button logout (sidebar, popup desktop, popup mobile)
    const logoutBtns = document.querySelectorAll('.logout-btn, #popupLogout, #mobilePopupLogout');

    logoutBtns.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                
                // Tutup semua popup yang mungkin terbuka
                const profilePopup = document.getElementById('profilePopup');
                const mobileProfilePopup = document.getElementById('mobileProfilePopup');
                
                if (profilePopup) {
                    profilePopup.classList.add('opacity-0', 'invisible');
                    profilePopup.classList.remove('opacity-100', 'visible');
                }
                if (mobileProfilePopup) {
                    mobileProfilePopup.classList.add('opacity-0', 'invisible');
                    mobileProfilePopup.classList.remove('opacity-100', 'visible');
                }
                
                // Tampilkan modal logout
                if (logoutModal) {
                    logoutModal.classList.remove('opacity-0', 'invisible');
                    logoutModal.classList.add('opacity-100', 'visible');
                }
            });
        }
    });

    // Tutup modal saat klik Batal
    if (cancelLogout) {
        cancelLogout.addEventListener('click', (e) => {
            e.preventDefault();
            if (logoutModal) {
                logoutModal.classList.add('opacity-0', 'invisible');
                logoutModal.classList.remove('opacity-100', 'visible');
            }
        });
    }

    // Logout sesungguhnya saat klik Logout
    if (confirmLogout) {
        confirmLogout.addEventListener('click', () => {
            // Tutup modal logout
            if (logoutModal) {
                logoutModal.classList.add('opacity-0', 'invisible');
                logoutModal.classList.remove('opacity-100', 'visible');
            }

            // === Buat modal sukses ===
            const successDiv = document.createElement("div");
            successDiv.innerHTML = `
                <div class="fixed inset-0 bg-black/50 flex items-center justify-center 
                    opacity-0 transition-opacity duration-300 z-[9999]" id="logoutSuccess">
                    <div class="bg-white rounded-lg shadow-lg w-[90%] max-w-sm p-6 text-center">
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

            setTimeout(() => {
                document.getElementById("logoutSuccess").classList.remove("opacity-0");
            }, 50);

            setTimeout(() => {
                if (logoutForm) {
                    logoutForm.submit();
                }
            }, 1300);
        });
    }

    // Tutup modal jika klik di luar dialog
    if (logoutModal) {
        logoutModal.addEventListener('click', (e) => {
            if (e.target === logoutModal) {
                logoutModal.classList.add('opacity-0', 'invisible');
                logoutModal.classList.remove('opacity-100', 'visible');
            }
        });
    }

    // Ripple effect saat klik di sidebar
    if (sidebar) {
        sidebar.addEventListener('click', function(e) {

            const rect = sidebar.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            

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
    }

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

            // Di mobile, tutup sidebar setelah klik menu
            if (window.innerWidth < 640 && sidebar) {
                setTimeout(() => {
                    toggleSidebar();
                }, 200);
            }

            // Redirect ke URL setelah delay animasi
            const url = this.getAttribute('href');
            setTimeout(() => {
                window.location.href = url;
            }, 150);
        });
    });

    // FUNCTION toggleSidebar - HARUS SAMA dengan yang di navbar
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.overlay');
        
        if (sidebar && overlay) {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }
        
        // Sinkronisasi dengan hamburger icon di navbar
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

        // DESKTOP: Geser konten (OPTIONAL - jika ada contentWrapper)
        const content = document.getElementById("contentWrapper");
        if (content && window.innerWidth >= 640) {
            if (content.classList.contains("ml-[210px]")) {
                content.classList.remove("ml-[210px]");
                content.classList.add("ml-[60px]");
            } else {
                content.classList.remove("ml-[60px]");
                content.classList.add("ml-[210px]");
            }
        }
    }

    // Profile popup (Desktop Sidebar)
    if (profilePopup) {
        profilePopup.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Klik avatar (Desktop Sidebar)
    if (avatarBtn && profilePopup && sidebarEl) {
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
            // Jika sidebar expanded, biarkan link href bekerja (ke profile page)
        });

        // Klik di luar popup → tutup
        document.addEventListener('click', function (e) {
            if (!profilePopup.contains(e.target) && !avatarBtn.contains(e.target)) {
                profilePopup.classList.add('opacity-0', 'invisible');
                profilePopup.classList.remove('opacity-100', 'visible');
            }
        });
    }

    // Handle popup logout button (sudah dihandle di logoutBtns forEach di atas)
    // Tapi tambahkan lagi untuk memastikan
    if (popupLogout && logoutModal) {
        popupLogout.addEventListener('click', function (e) {
            e.stopPropagation();
            
            // Tutup popup profile
            if (profilePopup) {
                profilePopup.classList.add('opacity-0', 'invisible');
                profilePopup.classList.remove('opacity-100', 'visible');
            }
            
            // Tampilkan modal logout
            logoutModal.classList.remove('opacity-0', 'invisible');
            logoutModal.classList.add('opacity-100', 'visible');
        });
    }
</script>