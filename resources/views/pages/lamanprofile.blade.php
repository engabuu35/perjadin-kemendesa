@extends('layouts.app')

@section('content')
{{-- Konten <main> dengan class="ml-[80px]" memberi ruang untuk sidebar --}}

<style>
/* ============================================
   MOBILE OPTIMIZATION (≤ 768px)
   ============================================ */
@media (max-width: 768px) {

    /* === MAIN WRAPPER === */
    main {
        padding: 0 !important;
    }

    /* === PAGE TITLE === */
    .page-title h1,
    .page-title h2,
    .page-title-title {
        font-size: 1.35rem !important;
        line-height: 1.3 !important;
        text-align: left !important;
    }

    .page-title-subtitle {
        font-size: 0.85rem !important;
        margin-top: 2px !important;
        text-align: left !important;
    }

    /* === HEADER AREA === */
    .mb-3.flex.items-center.justify-between {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 10px;
    }

    #toggleEditMode {
        width: 100% !important;
        justify-content: center !important;
        padding: 12px !important;
        font-size: 0.9rem !important;
    }

    /* === PROFILE LEFT CARD === */
    .lg\:col-span-1 .bg-white {
        padding: 16px !important;
    }

    .w-48.h-48 {
        width: 150px !important;
        height: 150px !important;
        margin: 0 auto !important;
    }

    /* Username Heading */
    .text-xl.font-bold {
        font-size: 1.1rem !important;
    }

    /* Subtext (NIP) */
    .text-sm.text-gray-500 {
        font-size: 0.8rem !important;
    }

    /* === PROFILE INFO CARD (Right) === */
    .lg\:col-span-2 .bg-white {
        padding: 16px !important;
    }

    .lg\:col-span-2 h3 {
        font-size: 1rem !important;
        padding-bottom: 8px !important;
    }

    /* === FORM LABEL === */
    label.text-sm {
        font-size: 0.85rem !important;
    }

    /* === INPUTS === */
    .edit-mode,
    .view-mode {
        padding: 10px !important;
        font-size: 0.9rem !important;
    }

    /* === BUTTONS INSIDE CARD === */
    #actionButtons {
        flex-direction: column !important;
        gap: 8px !important;
    }

    #actionButtons button {
        width: 100% !important;
        padding: 12px !important;
    }

    /* === MODAL === */
    #modalContent {
        width: 92% !important;
        padding: 20px !important;
    }
    #actionButtons {
        justify-content: center !important; /* center di mobile */
        width: 100% !important;
    }

    #actionButtons button {
        width: 100% !important; /* tombol full width */
        justify-content: center !important;
    }
}

</style>

<main class="mt-[25px] sm:mt-0 pl-0 sm:pl-[80px] max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
    
        <!-- ALERT SECTION -->
        @if($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg shadow-sm" role="alert">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                    <div>
                        <strong class="font-bold">Gagal menyimpan perubahan!</strong>
                        <ul class="mt-1 list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg shadow-sm" role="alert">
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                    <div>
                        <strong class="font-bold">Berhasil!</strong>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- HEADER -->
        <div class="mb-3 flex items-center justify-between">
            <div>
                <!-- Judul -->
                <x-page-title 
                    title="Profile"
                    subtitle="Kelola informasi profile Anda." />
            </div>
            <button type="button" 
            id="toggleEditMode" 
            class="text-white px-5 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2 transition-all duration-300 shadow-sm hover:shadow-md"
            style="background-color: #4169C8;">
            <i class="fas fa-edit"></i>
            <span id="editButtonText">Edit Profil</span>
            </button>
        </div>

        {{-- FORM PROFIL --}}
        <form id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- LEFT SIDE - Profile Picture Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 h-full flex flex-col">
                        <div class="flex flex-col items-center flex-grow">
                            
                            <!-- Profile Picture -->
                            <div class="w-48 h-48 bg-gray-200 rounded-2xl flex items-center justify-center mb-4 overflow-hidden relative">
                                <!-- Default Icon -->
                                <div id="defaultIcon" class="{{ $user->foto_profil ? 'hidden' : 'flex' }} flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-user text-6xl mb-2"></i>
                                    <p class="text-sm">Tidak ada foto</p>
                                </div>

                                <!-- Image Preview -->
                                <img id="imagePreview" 
                                     src="{{ $user->foto_profil ? asset('storage/'.$user->foto_profil) : '#' }}" 
                                     class="{{ $user->foto_profil ? 'block' : 'hidden' }} w-full h-full object-cover" 
                                     alt="Profile Preview">
                            </div>

                            <!-- Input File Hidden -->
                            <input type="file" name="foto" id="fotoInput" class="hidden" accept="image/*" onchange="previewImage(this)">

                            <!-- User Name -->
                            <h2 class="text-xl font-bold text-gray-800 text-center mb-1">{{ $user->nama }}</h2>
                            <p class="text-sm text-gray-500 mb-0">NIP: {{ $user->nip }}</p>

                            <!-- Change Photo Button (Only in Edit Mode) -->
                            <div id="photoButtons" class="hidden w-full space-y-3">
                                <button type="button" 
                                        onclick="document.getElementById('fotoInput').click()" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-300 shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                                    <i class="fas fa-camera"></i>
                                    <span>Pilih Foto</span>
                                </button>
                                
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
                                    <p class="text-xs text-gray-600 flex items-center justify-center gap-2">
                                        <i class="fas fa-info-circle text-blue-500"></i>
                                        Max. 2MB (JPG, PNG)
                                    </p>
                                    <p id="fileSuccess" class="text-green-600 text-xs mt-2 hidden font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i>Foto berhasil dipilih!
                                    </p>
                                </div>
                            </div>

                            <!-- Profile Stats (View Mode) - Push to bottom -->
                            <div id="profileStats" class="w-full mt-4 pt-3 border-t border-gray-200">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 flex items-center gap-2">
                                            <i class="fas fa-briefcase w-4 text-blue-600"></i>
                                            Jabatan
                                        </span>
                                        @php
                                            // Ambil jabatan dari tabel penugasanperan dan roles
                                            $penugasan = DB::table('penugasanperan')
                                                ->join('roles', 'penugasanperan.role_id', '=', 'roles.id')
                                                ->where('penugasanperan.user_id', $user->nip)
                                                ->select('roles.nama')
                                                ->first();
                                            $jabatan = $penugasan ? ucfirst($penugasan->nama) : 'Belum ditugaskan';
                                        @endphp
                                        <span class="text-gray-800 text-xs font-semibold">{{ $jabatan }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 flex items-center gap-2">
                                            <i class="fas fa-id-badge w-4 text-purple-600"></i>
                                            Pangkat/Golongan
                                        </span>
                                        @php
                                            // Ambil nama pangkat dari tabel pangkatgolongan
                                            $pangkat = null;
                                            if ($user->pangkat_gol_id) {
                                                $pangkatData = DB::table('pangkatgolongan')
                                                    ->where('id', $user->pangkat_gol_id)
                                                    ->first();
                                                $pangkat = $pangkatData ? $pangkatData->nama_pangkat : 'Belum diisi';
                                            } else {
                                                $pangkat = 'Belum diisi';
                                            }
                                        @endphp
                                        <span class="text-gray-800 text-xs font-semibold">{{ $pangkat }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 flex items-center gap-2">
                                            <i class="fas fa-plane-departure w-4 text-orange-600"></i>
                                            Perjalanan Dinas
                                        </span>
                                        @php
                                            // Cek apakah user punya perjalanan dinas yang belum selesai (is_finished = 0)
                                            $hasActiveTrip = DB::table('pegawaiperjadin')
                                                ->where('id_user', $user->id_uke)
                                                ->where('is_finished', 0)
                                                ->exists();
                                        @endphp
                                        @if($hasActiveTrip)
                                            <span class="text-green-600 text-xs font-semibold flex items-center gap-1">
                                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                                Sedang Dinas
                                            </span>
                                        @else
                                            <span class="text-gray-500 text-xs font-semibold">Tidak Aktif</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE - Profile Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 h-full flex flex-col">
                        <h3 class="text-lg font-bold text-gray-800 mb-5 pb-3 border-b border-gray-200">Informasi Pribadi</h3>
                        
                        <div class="space-y-5 flex-grow">
                            
                            <!-- Nama Lengkap -->
                            <div>
                                <label class="flex items-center text-gray-700 font-semibold mb-2 text-sm">
                                    <i class="fas fa-user-circle text-blue-600 mr-2 w-4"></i>
                                    Nama Lengkap
                                </label>
                                <!-- View Mode -->
                                <div id="namaView" class="view-mode px-4 py-3 bg-gray-50 rounded-lg text-gray-800 border border-gray-200">
                                    {{ $user->nama }}
                                </div>
                                <!-- Edit Mode -->
                                <input type="text" 
                                       id="namaEdit"
                                       name="nama" 
                                       value="{{ old('nama', $user->nama) }}" 
                                       class="edit-mode hidden w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                       placeholder="Masukkan nama lengkap">
                            </div>

                            <!-- NIP -->
                            <div>
                                <label class="flex items-center text-gray-700 font-semibold mb-2 text-sm">
                                    <i class="fas fa-id-card text-blue-600 mr-2 w-4"></i>
                                    NIP
                                </label>
                                <div class="px-4 py-3 bg-gray-50 rounded-lg text-gray-500 border border-gray-200 flex items-center justify-between">
                                    <span>{{ $user->nip }}</span>
                                    <span class="text-xs text-gray-400 italic">Tidak dapat diubah</span>
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="flex items-center text-gray-700 font-semibold mb-2 text-sm">
                                    <i class="fas fa-envelope text-blue-600 mr-2 w-4"></i>
                                    Email
                                </label>
                                <div class="px-4 py-3 bg-gray-50 rounded-lg text-gray-500 border border-gray-200 flex items-center justify-between">
                                    <span>{{ $user->email }}</span>
                                    <span class="text-xs text-gray-400 italic">Tidak dapat diubah</span>
                                </div>
                            </div>

                            <!-- Nomor Telepon -->
                            <div>
                                <label class="flex items-center text-gray-700 font-semibold mb-2 text-sm">
                                    <i class="fas fa-phone text-blue-600 mr-2 w-4"></i>
                                    Nomor Telepon
                                </label>
                                <!-- View Mode -->
                                <div id="telpView" class="view-mode px-4 py-3 bg-gray-50 rounded-lg text-gray-800 border border-gray-200">
                                    {{ $user->no_telp ?: 'Belum diisi' }}
                                </div>
                                <!-- Edit Mode -->
                                <input type="text" 
                                       id="telpEdit"
                                       name="no_telp" 
                                       value="{{ old('no_telp', $user->no_telp) }}" 
                                       class="edit-mode hidden w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                       placeholder="08xxxxxxxxxx">
                            </div>

                        </div>

                        {{-- TOMBOL AKSI (Only in Edit Mode) --}}
                        <div id="actionButtons" class="hidden flex justify-end gap-3 mt-auto pt-5 border-t border-gray-200">
                            <button type="button" 
                                    id="cancelEdit" 
                                    class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-200 transition-all duration-300 border border-gray-300">
                                <i class="fas fa-times mr-2"></i>Batal
                            </button>
                            <button type="button" 
                                    id="openSaveModal" 
                                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm flex items-center gap-2 transition-all duration-300 shadow-sm hover:shadow-md">
                                <i class="fas fa-save"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>    
</main>

{{-- MODAL KONFIRMASI SIMPAN PERUBAHAN --}}
<div id="saveModal"
     class="fixed inset-0 bg-black/50 flex items-center justify-center opacity-0 invisible transition-opacity duration-300 z-[999]">
    <div class="bg-white rounded-xl shadow-xl w-[90%] max-w-md p-6 text-center transform transition-transform duration-300 scale-95" id="modalContent">
        <div class="w-16 h-16 bg-blue-100 rounded-full mx-auto mb-4 flex items-center justify-center">
            <i class="fas fa-save text-blue-600 text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold mb-2 text-gray-800">Konfirmasi Perubahan</h3>
        <p class="text-gray-600 mb-6 text-sm">
            Apakah Anda yakin ingin menyimpan perubahan pada profil Anda?
        </p>
        <div class="flex justify-center gap-3">
            <button id="cancelSave"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-semibold text-sm border border-gray-300">
                Batal
            </button>
            <button id="confirmSave"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm shadow-sm">
                Ya, Simpan
            </button>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script>
    let isEditMode = false;

    // Toggle Edit Mode
    function toggleEditMode() {
        isEditMode = !isEditMode;
        const editBtn = document.getElementById('toggleEditMode');
        const editBtnText = document.getElementById('editButtonText');
        const viewElements = document.querySelectorAll('.view-mode');
        const editElements = document.querySelectorAll('.edit-mode');
        const actionButtons = document.getElementById('actionButtons');

        if (isEditMode) {
            // Switch to Edit Mode
            editBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            editBtn.classList.add('bg-gray-600', 'hover:bg-gray-700');
            editBtnText.textContent = 'Batal Edit';
            editBtn.innerHTML = '<i class="fas fa-times"></i><span id="editButtonText">Batal Edit</span>';
            
            viewElements.forEach(el => el.classList.add('hidden'));
            editElements.forEach(el => el.classList.remove('hidden'));
            actionButtons.classList.remove('hidden');
            document.getElementById('photoButtons').classList.remove('hidden');
            document.getElementById('profileStats').classList.add('hidden');
        } else {
            // Switch to View Mode
            editBtn.classList.remove('bg-gray-600', 'hover:bg-gray-700');
            editBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            editBtnText.textContent = 'Edit Profil';
            editBtn.innerHTML = '<i class="fas fa-edit"></i><span id="editButtonText">Edit Profil</span>';
            
            viewElements.forEach(el => el.classList.remove('hidden'));
            editElements.forEach(el => el.classList.add('hidden'));
            actionButtons.classList.add('hidden');
            document.getElementById('photoButtons').classList.add('hidden');
            document.getElementById('profileStats').classList.remove('hidden');
            
            // Reset form
            document.getElementById('profileForm').reset();
            document.getElementById('fileSuccess').classList.add('hidden');
        }
    }

    // Preview Image
    function previewImage(input) {
        const defaultIcon = document.getElementById('defaultIcon');
        const imagePreview = document.getElementById('imagePreview');
        const fileSuccess = document.getElementById('fileSuccess');
        
        fileSuccess.classList.add('hidden');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxSize = 2 * 1024 * 1024; // 2 MB

            if (file.size > maxSize) {
                alert("Ukuran gambar maksimal 2 MB. Silakan pilih foto yang lebih kecil!");
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

    // Modal Functions
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('toggleEditMode');
        const cancelBtn = document.getElementById('cancelEdit');
        const openModalBtn = document.getElementById('openSaveModal');
        const modal = document.getElementById('saveModal');
        const modalContent = document.getElementById('modalContent');
        const cancelSaveBtn = document.getElementById('cancelSave');
        const confirmBtn = document.getElementById('confirmSave');
        const form = document.getElementById('profileForm');

        function openModal() {
            modal.classList.remove('opacity-0', 'invisible');
            modal.classList.add('opacity-100', 'visible');
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function closeModal() {
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('opacity-0', 'invisible');
                modal.classList.remove('opacity-100', 'visible');
            }, 200);
        }

        toggleBtn.addEventListener('click', toggleEditMode);
        cancelBtn.addEventListener('click', toggleEditMode);
        openModalBtn.addEventListener('click', openModal);
        cancelSaveBtn.addEventListener('click', closeModal);

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        confirmBtn.addEventListener('click', function () {
            form.submit();
        });
    });
</script>
@endsection