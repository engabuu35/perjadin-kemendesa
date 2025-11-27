@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<main class="max-w-6xl mx-auto px-5 py-8 min-h-screen">
    
    <!-- ALERT SECTION -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Berhasil!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Gagal!</strong>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-back-button />
    <x-page-title title="Edit Profil" />

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <!-- Profile Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-10 mb-10">
            <div class="flex flex-col lg:flex-row gap-12 items-start">
                
                <!-- Profile Picture Section -->
                <div class="flex flex-col items-center lg:items-start flex-shrink-0">
                    <!-- Container Foto dengan ID agar bisa diakses JS -->
                    <div class="w-48 h-48 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-6 overflow-hidden shadow-lg border-4 border-white relative group">
                        
                        <!-- 1. GAMBAR DEFAULT (ICON) -->
                        <div id="defaultIcon" class="{{ $user->foto_profil ? 'hidden' : 'block' }}">
                            <i class="fas fa-user text-8xl text-gray-400"></i>
                        </div>

                        <!-- 2. GAMBAR PREVIEW (FOTO ASLI/BARU) -->
                        <img id="imagePreview" 
                             src="{{ $user->foto_profil ? asset('storage/'.$user->foto_profil) : '#' }}" 
                             class="{{ $user->foto_profil ? 'block' : 'hidden' }} w-full h-full object-cover" 
                             alt="Profile Preview">
                    </div>

                    <!-- Input File Hidden -->
                    <input type="file" name="foto" id="fotoInput" class="hidden" accept="image/*" onchange="previewImage(this)">

                    <button type="button" onclick="document.getElementById('fotoInput').click()" 
                        class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-12 py-3 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-camera mr-2"></i>PILIH FOTO
                    </button>
                    
                    <p id="fileSuccess" class="text-green-600 text-sm mt-2 hidden font-bold text-center">
                        <i class="fas fa-check-circle mr-1"></i>Foto dipilih!
                    </p>
                </div>

                <!-- Profile Information Inputs -->
                <div class="flex-1 space-y-5 w-full">
                    <!-- NIP (Readonly) -->
                    <div class="flex flex-col sm:flex-row rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 font-bold flex items-center justify-center sm:w-48 text-center text-lg">
                            <i class="fas fa-id-card mr-2"></i>NIP
                        </div>
                        <div class="bg-blue-50 px-8 py-4 flex-1 flex items-center">
                            <input type="text" value="{{ $user->nip }}" readonly class="bg-transparent w-full text-gray-500 font-medium text-lg focus:outline-none cursor-not-allowed">
                        </div>
                    </div>

                    <!-- Email (Readonly) -->
                    <div class="flex flex-col sm:flex-row rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 font-bold flex items-center justify-center sm:w-48 text-center text-lg">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </div>
                        <div class="bg-blue-50 px-8 py-4 flex-1 flex items-center">
                            <input type="email" value="{{ $user->email }}" readonly class="bg-transparent w-full text-gray-500 font-medium text-lg focus:outline-none cursor-not-allowed">
                        </div>
                    </div>

                    <!-- Nomor HP (Editable) -->
                    <div class="flex flex-col sm:flex-row rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 focus-within:ring-2 focus-within:ring-blue-400">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 font-bold flex items-center justify-center sm:w-48 text-center text-lg">
                            <i class="fas fa-phone mr-2"></i>No. Telp
                        </div>
                        <div class="bg-blue-50 px-8 py-4 flex-1 flex items-center">
                            <input type="text" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}" 
                                class="bg-transparent w-full text-gray-800 font-medium text-lg focus:outline-none placeholder-gray-400"
                                placeholder="08xxxxxxxx">
                        </div>
                    </div>

                    <!-- Nama Lengkap (Editable) -->
                    <div class="flex flex-col sm:flex-row rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 focus-within:ring-2 focus-within:ring-blue-400">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 font-bold flex items-center justify-center sm:w-48 text-center text-lg">
                            <i class="fas fa-user-circle mr-2"></i>Nama
                        </div>
                        <div class="bg-blue-50 px-8 py-4 flex-1 flex items-center">
                            <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" 
                                class="bg-transparent w-full text-gray-800 font-medium text-lg focus:outline-none placeholder-gray-400"
                                placeholder="Nama Lengkap">
                        </div>
                    </div>

                    <!-- Ganti Password dengan Fitur Toggle Eye -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-gray-600 font-bold mb-4">Ganti Password (Opsional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <!-- Password Baru -->
                            <div class="relative">
                                <input type="password" name="password" id="password" placeholder="Password Baru" 
                                    class="bg-white border border-gray-300 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none w-full pr-10">
                                <button type="button" onclick="togglePassword('password', 'eyeIconPass')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-blue-600 focus:outline-none">
                                    <i id="eyeIconPass" class="fas fa-eye"></i>
                                </button>
                            </div>

                            <!-- Konfirmasi Password -->
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Konfirmasi Password" 
                                    class="bg-white border border-gray-300 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none w-full pr-10">
                                <button type="button" onclick="togglePassword('password_confirmation', 'eyeIconConf')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-blue-600 focus:outline-none">
                                    <i id="eyeIconConf" class="fas fa-eye"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-10 py-4 rounded-xl font-bold text-lg flex items-center gap-3 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="fas fa-save text-lg"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</main>

<!-- SCRIPTS: PREVIEW IMAGE & TOGGLE PASSWORD -->
<script>
    // 1. Script Preview Image
    function previewImage(input) {
        const defaultIcon = document.getElementById('defaultIcon');
        const imagePreview = document.getElementById('imagePreview');
        const fileSuccess = document.getElementById('fileSuccess');
        
        fileSuccess.classList.add('hidden');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxSize = 2 * 1024 * 1024; // 2 MB

            if (file.size > maxSize) {
                alert("Ukuran gambar Max. 2 MB. Silahkan upload ulang foto profil Anda!");
                input.value = ""; 
                return;
            }

            const reader = new FileReader();
            
            reader.onload = function(e) {
                defaultIcon.classList.add('hidden');
                imagePreview.classList.remove('hidden');
                imagePreview.src = e.target.result;
                fileSuccess.classList.remove('hidden');
            }
            
            reader.readAsDataURL(file);
        }
    }

    // 2. Script Toggle Password Visibility (Fitur Baru)
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection