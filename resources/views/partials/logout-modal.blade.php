<!-- Tambahkan Modal Konfirmasi Logout yang hilang -->
<div id="logoutModal" class="fixed inset-0 bg-black/50 flex items-center justify-center 
           opacity-0 invisible transition-all duration-300 z-[9999]">
    <div
        class="bg-white rounded-xl shadow-lg w-[90%] max-w-sm p-6 text-center transform scale-95 transition-transform duration-300">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
            <i class="fas fa-sign-out-alt text-2xl text-red-500"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-800">Konfirmasi Logout</h3>
        <p class="text-gray-600 mt-2 text-sm">Apakah kamu yakin ingin keluar dari akun?</p>
        <div class="flex gap-3 mt-6">
            <button id="cancelLogout" class="flex-1 py-2.5 px-4 bg-gray-100 text-gray-700 rounded-lg 
                       hover:bg-gray-200 transition-colors duration-200 font-medium text-sm">
                Batal
            </button>
            <button id="confirmLogout" class="flex-1 py-2.5 px-4 bg-red-500 text-white rounded-lg 
                       hover:bg-red-600 transition-colors duration-200 font-medium text-sm">
                Logout
            </button>
        </div>
    </div>
</div>
<!-- End Modal Logout -->