<div class="relative">

    <!-- Popup Menu -->
    <div id="profilePopup"
        class="absolute bottom-[120px] left-[120%] -translate-x-1/2 bg-white text-gray-800 
                rounded-xl shadow-lg py-1.5 w-[140px] opacity-0 invisible 
                transition-all duration-300 z-[9999]">

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

    <!-- User Name -->
    <div class="name text-white text-xs font-bold mb-[3px] opacity-0 transition-opacity duration-300 whitespace-nowrap">
        {{ Auth::user()->nama }}
    </div>

    <!-- User Role/NPM -->
    <div class="role text-white/70 text-[10px] mb-[10px] opacity-0 transition-opacity duration-300 whitespace-nowrap">
        {{ Auth::user()->nip }}
    </div>

    <!-- Logout Button -->
    <form id="logoutForm" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
            class="logout-btn flex items-center justify-center gap-1.5 
                py-2 px-4 w-full
                bg-white/10 border-none rounded-lg text-white cursor-pointer 
                transition-all duration-300 hover:bg-white/20 text-xs">
            <i class="fas fa-sign-out-alt text-xs"></i>
            <span>Logout</span>
        </button>
    </form>
</div>

        </div>
