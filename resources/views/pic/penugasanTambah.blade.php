@extends('layouts.appPIC')

@section('title', isset($perjalanan) ? 'Edit Penugasan Perjalanan Dinas' : 'Tambah Penugasan Perjalanan Dinas')

@section('content')
<main class="transition-all duration-300 ml-0 sm:ml-[60px]">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
        <div class="max-w-5xl mx-auto">

        @php
            $disabled = isset($perjalanan) && in_array($perjalanan->id_status, [4,5,6,7,8]);
            
            // Logika Ketat: Sedang Berlangsung = Hari ini >= Tgl Mulai DAN Hari ini <= Tgl Selesai
            // ATAU status di DB memang sudah 'Sedang Berlangsung'
            $today = \Carbon\Carbon::now()->startOfDay();
            $mulai = isset($perjalanan) ? \Carbon\Carbon::parse($perjalanan->tgl_mulai)->startOfDay() : null;
            $selesai = isset($perjalanan) ? \Carbon\Carbon::parse($perjalanan->tgl_selesai)->endOfDay() : null;

            $isTimeValid = $mulai && $selesai && $today->between($mulai, $selesai);
            $isStatusValid = isset($perjalanan) && ($perjalanan->status->nama_status ?? '') === 'Sedang Berlangsung';
            
            // Gabungan: Boleh manual jika WAKTUNYA pas ATAU STATUSNYA sudah Sedang Berlangsung
            $canManual = $isTimeValid || $isStatusValid;

            // Jika sudah manual (ID 7), form input muncul
            $isManual = isset($perjalanan) && $perjalanan->id_status == 7;
        @endphp

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-5">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-5">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center justify-between mb-2">
            <x-page-title title="Penugasan Perjalanan Dinas" />
            <div class="flex justify-end mb-8">
                <a href="{{ url('/pic/manajemen-perjadin')}}" 
                class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
            </div>
        </div>

        {{-- ================= FORM UTAMA (EDIT/CREATE) ================= --}}
        <div class="bg-white rounded-xl p-8 shadow-lg mb-8">
            @if(isset($perjalanan))
                {{-- TAMBAHKAN id="formPenugasan" DI SINI --}}
                <form id="formPenugasan" action="{{ route('pic.penugasan.update', $perjalanan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
            @else
                {{-- TAMBAHKAN id="formPenugasan" JUGA DI SINI --}}
                <form id="formPenugasan" action="{{ route('pic.penugasan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
            @endif

                <div class="mb-5">
                    <label for="nomor_surat" class="block text-gray-700 text-sm font-medium mb-2">Nomor Surat Tugas</label>
                    <input type="text" id="nomor_surat" name="nomor_surat" value="{{ old('nomor_surat', $perjalanan->nomor_surat ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600 transition" required>
                </div>

                <div class="mb-5">
                    <label for="tanggal_surat" class="block text-gray-700 text-sm font-medium mb-2">Tanggal Surat</label>
                    <input type="date" id="tanggal_surat" name="tanggal_surat" value="{{ old('tanggal_surat', isset($perjalanan) ? $perjalanan->tanggal_surat->format('Y-m-d') : '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600 transition" required>
                </div>

                <div class="mb-5">
                    <label for="tujuan" class="block text-gray-700 text-sm font-medium mb-2">Kota Tujuan</label>
                    <input type="text" id="tujuan" name="tujuan" value="{{ old('tujuan', $perjalanan->tujuan ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600 transition" required>
                </div>

                <div class="mb-5">
                    <label for="dalam_rangka" class="block text-gray-700 text-sm font-medium mb-2">Dalam Rangka</label>
                    <input type="text" id="dalam_rangka" name="dalam_rangka" value="{{ old('dalam_rangka', $perjalanan->dalam_rangka ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600 transition" required>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="tgl_mulai" class="block text-gray-700 text-sm font-medium mb-2">Tanggal Mulai</label>
                        <input type="date" id="tgl_mulai" name="tgl_mulai" value="{{ old('tgl_mulai', isset($perjalanan) ? $perjalanan->tgl_mulai->format('Y-m-d') : '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600 transition" required>
                    </div>
                    <div>
                        <label for="tgl_selesai" class="block text-gray-700 text-sm font-medium mb-2">Tanggal Selesai</label>
                        <input type="date" id="tgl_selesai" name="tgl_selesai" value="{{ old('tgl_selesai', isset($perjalanan) ? $perjalanan->tgl_selesai->format('Y-m-d') : '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600 transition" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="surat_tugas" class="block text-gray-700 text-sm font-medium mb-2">Surat Tugas (PDF)</label>
                    <input type="file" id="surat_tugas" name="surat_tugas" accept="application/pdf" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600 transition">
                    @if(isset($perjalanan) && !empty($perjalanan->surat_tugas))
                        <p class="text-sm text-gray-600 mt-2">File saat ini: <a href="{{ asset('storage/'.$perjalanan->surat_tugas) }}" target="_blank" class="text-blue-600 underline">Lihat File</a></p>
                    @endif
                </div>

                <div class="mb-5">
                    <label for="approved_by" class="block text-gray-700 text-sm font-medium mb-2">Pimpinan yang Menyetujui</label>
                    <select id="approved_by" name="approved_by" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600 transition">
                        <option value="">- Pilih Nama Pimpinan -</option>
                        @foreach($pimpinans as $pimpinan)
                            <option value="{{ $pimpinan->nip }}" {{ old('approved_by', $perjalanan->approved_by ?? '') == $pimpinan->nip ? 'selected' : '' }}>{{ $pimpinan->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-8">
                    <h3 class="text-gray-700 text-xl font-semibold mb-5">Daftar Pegawai</h3>
                    <div id="pegawaiList"></div>
                    <button type="button" id="btnTambahPegawai" class="w-full py-3.5 border-2 border-dashed border-blue-700 text-blue-700 rounded-lg font-bold hover:bg-blue-50 transition text-base mt-2">+ Tambah Pegawai</button>
                </div>

                <div class="flex flex-col gap-3 mt-8">
                    <a href="{{ route('pic.penugasan') }}" class="w-full py-3.5 bg-gray-300 text-gray-600 rounded-lg font-semibold hover:bg-gray-400 transition text-center">Batal</a>
                    <button id="{{ isset($perjalanan) ? 'btnPerbarui' : '' }}" type="submit" @if($disabled) disabled @endif 
                        class="w-full py-3.5 bg-blue-700 text-white rounded-lg font-semibold {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-800' }} transition">
                        {{ isset($perjalanan) ? 'Perbarui' : 'Simpan' }}
                    </button>
                </div>

                @if(isset($perjalanan))
                    <div class="flex flex-row gap-3 mt-4">
                        {{-- TOMBOL HANYA AKTIF JIKA WAKTUNYA SUDAH MASUK (SEDANG BERLANGSUNG) --}}
                        <button type="button" id="btnSelesaikan" 
                            @if($disabled || !$canManual) disabled @endif 
                            class="w-full py-3.5 bg-yellow-600 text-white rounded-lg font-semibold transition {{ ($disabled || !$canManual) ? 'opacity-50 cursor-not-allowed hover:bg-yellow-600' : 'hover:bg-yellow-700' }}">
                            Selesaikan Manual
                        </button>
                        
                        <button type="button" id="btnBatalkan" 
                            @if($disabled) disabled @endif 
                            class="w-full py-3.5 bg-red-600 text-white rounded-lg font-semibold {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-700' }} transition">
                            Batalkan Perjalanan
                        </button>
                        
                        
                    </div>
                    
                    @if(!$canManual && !$disabled)
                        <p class="text-xs text-yellow-700 mt-2 text-center bg-yellow-50 p-2 rounded border border-yellow-200">
                            <i class="fa-solid fa-clock"></i> Tombol "Selesaikan Manual" hanya aktif jika tanggal hari ini sudah memasuki periode perjalanan dinas.
                        </p>
                    @endif
                @endif
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // === 1. LOGIKA PEGAWAI (MODIFIED UNTUK CEK TANGGAL) - DARI KODE KAMU ===
        const users = @json($users ?? []);
        const initialPegawai = @json($pegawaiList ?? []);
        
        // Menggunakan data jadwal dari kode pertama agar fitur bentrok tanggal berjalan
        const allSchedules = @json($allSchedules ?? []);
        const currentPerjadinId = {{ isset($perjalanan) ? $perjalanan->id : 'null' }};

        const pegawaiListEl = document.getElementById('pegawaiList');
        const btnTambah = document.getElementById('btnTambahPegawai');
        const inputMulai = document.getElementById('tgl_mulai');
        const inputSelesai = document.getElementById('tgl_selesai');

        let pegawaiCount = 0;

        // FUNGSI CEK BENTROK (DARI KODE PERTAMA)
        function checkConflict(nip) {
            const valMulai = inputMulai.value;
            const valSelesai = inputSelesai.value;

            if (!valMulai || !valSelesai) return false;

            const formStart = new Date(valMulai);
            const formEnd = new Date(valSelesai);

            const userTrips = allSchedules.filter(s => s.nip === nip);

            for (let trip of userTrips) {
                if (trip.id_status == 8) continue; 
                if (currentPerjadinId && trip.id_perjadin == currentPerjadinId) continue;

                const tripStart = new Date(trip.tgl_mulai);
                const tripEnd = new Date(trip.tgl_selesai);

                if (formStart <= tripEnd && formEnd >= tripStart) {
                    return true; // BENTROK
                }
            }
            return false; // AMAN
        }

        // FUNGSI WARNA DOT
        function statusClassForUser(nip) {
            if (!nip) return 'bg-gray-300';
            const isConflict = checkConflict(nip);
            return isConflict ? 'bg-red-500' : 'bg-blue-500';
        }

        function refreshAllDots() {
            const allDots = document.querySelectorAll('.status-dot');
            allDots.forEach(dot => {
                const card = dot.closest('.pegawai-card');
                if (card) {
                    const nipInput = card.querySelector('.nip');
                    const cls = statusClassForUser(nipInput.value.trim());
                    dot.className = 'status-dot w-3 h-3 rounded-full ' + cls;
                }
            });
        }

        if (inputMulai) inputMulai.addEventListener('change', refreshAllDots);
        if (inputSelesai) inputSelesai.addEventListener('change', refreshAllDots);

        if (pegawaiListEl && btnTambah) {
            
            function makeUserListItem(user) {
                const dot = statusClassForUser(user.nip);
                const li = document.createElement('li');
                li.className = 'user-item px-3 py-2 hover:bg-gray-50 cursor-pointer flex items-center gap-3';
                li.dataset.nip = user.nip; li.dataset.nama = user.nama;
                li.innerHTML = `<span class="w-3 h-3 rounded-full inline-block ${dot}"></span><div class="text-sm"><div class="font-medium text-gray-700">${user.nama}</div><div class="text-xs text-gray-500">${user.nip}</div></div>`;
                return li;
            }

            function buildDropdownList(ulEl, filter, currentNip) {
                ulEl.innerHTML = '';
                const q = filter.trim().toLowerCase();
                const inputs = document.querySelectorAll('.nip'); 
                const selectedNips = Array.from(inputs).map(i => i.value.trim()).filter(n => n && n !== currentNip);
                
                const matched = users.filter(u => (!q || u.nip.toLowerCase().includes(q) || u.nama.toLowerCase().includes(q)) && !selectedNips.includes(u.nip));
                
                if (matched.length === 0) { ulEl.innerHTML = `<li class="px-3 py-2 text-gray-500 text-sm">Tidak ada hasil</li>`; return; }
                
                matched.forEach(u => ulEl.appendChild(makeUserListItem(u)));
            }

            function makePegawaiCard(index) {
                const div = document.createElement('div');
                div.className = 'pegawai-card bg-blue-50 border border-blue-200 rounded-xl p-6 mb-5';
                div.dataset.index = index;
                div.innerHTML = `
                    <div class="flex justify-end"><button type="button" class="hapus-pegawai px-4 py-2 bg-red-600 text-white rounded-lg">Hapus</button></div>
                    <div class="mb-4"><label class="text-sm ml-1 font-medium text-gray-700">NIP</label><div class="relative"><div class="flex items-center gap-3"><input type="text" name="pegawai[${index}][nip]" class="nip w-full text-sm px-4 py-3 mt-2 border rounded-lg" placeholder="Ketik NIP atau Nama..." autocomplete="off"><span class="status-dot w-3 h-3 rounded-full bg-gray-300"></span></div><div class="user-dropdown hidden absolute z-30 left-0 right-6 mt-2 bg-white border rounded-lg shadow-lg"><ul class="user-list max-h-56 overflow-auto"></ul></div></div></div>
                    <div class="mb-4"><label class="text-sm ml-1 font-medium text-gray-700">Nama Lengkap</label><div class="flex items-center gap-3"><input type="text" name="pegawai[${index}][nama]" placeholder="Nama Lengkap Tanpa Gelar" class="nama w-full text-sm px-4 py-3 mt-2 bg-gray-50 border rounded-lg cursor-not-allowed" readonly><span class="w-3 h-3"></span></div></div>
                `;
                return div;
            }

            function initCardDropdown(card) {
                const nipInput = card.querySelector('.nip');
                const namaInput = card.querySelector('.nama');
                const dropdown = card.querySelector('.user-dropdown');
                const ul = card.querySelector('.user-list');
                const dot = card.querySelector('.status-dot');
                
                const refreshDot = () => { 
                    const cls = statusClassForUser(nipInput.value.trim()); 
                    dot.className = 'status-dot w-3 h-3 rounded-full ' + cls; 
                };

                nipInput.addEventListener('focus', () => { dropdown.classList.remove('hidden'); buildDropdownList(ul, nipInput.value, nipInput.value); });
                nipInput.addEventListener('input', () => { dropdown.classList.remove('hidden'); buildDropdownList(ul, nipInput.value, nipInput.value); });
                
                ul.addEventListener('click', e => {
                    const li = e.target.closest('.user-item'); if (!li) return;
                    nipInput.value = li.dataset.nip; namaInput.value = li.dataset.nama; dropdown.classList.add('hidden'); refreshDot();
                });
                
                document.addEventListener('click', e => { if (!card.contains(e.target)) dropdown.classList.add('hidden'); });
                refreshDot();
            }

            function renderInitialPegawai() {
                pegawaiListEl.innerHTML = '';
                if (!initialPegawai.length) { const c = makePegawaiCard(0); pegawaiListEl.appendChild(c); initCardDropdown(c); pegawaiCount = 1; return; }
                initialPegawai.forEach((p, idx) => {
                    const card = makePegawaiCard(idx); pegawaiListEl.appendChild(card);
                    card.querySelector('.nip').value = p.nip; card.querySelector('.nama').value = p.nama;
                    initCardDropdown(card);
                });
                pegawaiCount = initialPegawai.length;
            }

            btnTambah.addEventListener('click', () => { const card = makePegawaiCard(pegawaiCount); pegawaiListEl.appendChild(card); initCardDropdown(card); pegawaiCount++; });
            pegawaiListEl.addEventListener('click', e => { if (e.target.classList.contains('hapus-pegawai')) { e.target.closest('.pegawai-card').remove(); } });
            renderInitialPegawai();
        }

        // === 2. LOGIKA MODAL MANUAL (EDIT ONLY) - MENGGUNAKAN UI MODAL TEMANMU ===
        @if(isset($perjalanan))
            const perjalananId = {{ $perjalanan->id }};
            const csrfToken = '{{ csrf_token() }}';
            const mainForm = document.getElementById('formPenugasan');

            // --- STRUKTUR MODAL BARU (DARI KODE TEMAN) ---
            const modal = document.createElement('div');
            modal.id = 'dynamicStatusModal';
            modal.className = 'fixed inset-0 bg-black/60 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 z-[9999]';
            modal.innerHTML = `
                <div class="bg-white rounded-lg shadow-2xl w-[90%] max-w-md p-6 text-center relative transform scale-90 transition-transform duration-300" onclick="event.stopPropagation()">
                    <button id="dynModalClose" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                    
                    <div id="dynModalIcon" class="mb-3"></div>
                    <h3 id="dynModalTitle" class="text-xl font-bold mb-3 text-gray-800"></h3>
                     <p id="dynModalMessage" class="text-gray-600 mb-4"></p>
                    <div id="dynAlasanContainer" class="hidden mb-5 text-left">
                        <label class="text-sm font-semibold text-gray-700 block mb-2">Alasan:</label>
                        <textarea id="dynAlasanManual" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Contoh: Pembatalan sebagian anggota..."></textarea>
                    </div>
                    <div class="flex justify-center gap-4">
                        <button id="dynModalCancel" class="flex-1 max-w-[150px] py-3 px-6 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400 transition">Batal</button>
                        <button id="dynModalConfirm" class="flex-1 max-w-[150px] py-3 px-6 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">Ya</button>
                    </div>
                </div>`;
            document.body.appendChild(modal);

            // Modal Response (Success/Error)
            const responseModal = document.createElement('div');
            responseModal.id = 'responseModal';
            responseModal.className = 'fixed inset-0 bg-black/60 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 z-[9999]';
            responseModal.innerHTML = `
                <div class="bg-white rounded-lg shadow-2xl w-[90%] max-w-sm p-6 text-center transform scale-90 transition-transform duration-300" onclick="event.stopPropagation()">
                    <div id="responseIcon" class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center"></div>
                    <h3 id="responseTitle" class="text-xl font-bold mb-2 text-gray-800"></h3>
                    <p id="responseMessage" class="text-gray-600 mb-6"></p>
                    <button id="responseOk" class="w-full py-3 px-6 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">OK</button>
                </div>`;
            document.body.appendChild(responseModal);

            const modalTitle = modal.querySelector('#dynModalTitle');
            const modalMessage = modal.querySelector('#dynModalMessage');
            const modalIcon = modal.querySelector('#dynModalIcon');
            const modalConfirm = modal.querySelector('#dynModalConfirm');
            const modalCancel = modal.querySelector('#dynModalCancel');
            const modalClose = modal.querySelector('#dynModalClose');
            const alasanContainer = modal.querySelector('#dynAlasanContainer');
            const alasanInput = modal.querySelector('#dynAlasanManual');
            const modalBox = modal.querySelector('div'); // inner container

            const responseIcon = responseModal.querySelector('#responseIcon');
            const responseTitle = responseModal.querySelector('#responseTitle');
            const responseMessage = responseModal.querySelector('#responseMessage');
            const responseOk = responseModal.querySelector('#responseOk');
            const responseBox = responseModal.querySelector('div');

            let statusToUpdate = '';

            // FUNGSI UTILITY UI (DARI KODE TEMAN)
            function showModal(status) {
                statusToUpdate = status;
                alasanInput.value = ''; 
                
                if (status === 'Diselesaikan Manual') {
                    modalTitle.textContent = 'Selesaikan Perjalanan';
                    modalMessage.textContent = 'Mohon isi alasan penyelesaian manual:';
                    modalIcon.innerHTML = ''; 
                    alasanContainer.classList.remove('hidden');
                } else if (status === 'Perbarui') {
                    //  Logika Perbarui 
                    modalTitle.textContent = 'Perbarui Penugasan';
                    modalMessage.textContent = 'Apakah Anda yakin ingin menyimpan perubahan data ini?';
                    modalIcon.innerHTML = '<i class="fa-solid fa-circle-info text-blue-600 text-3xl"></i>';
                    alasanContainer.classList.add('hidden');

                } else {
                    // Logika Batalkan Perjalanan
                    modalTitle.textContent = 'Batalkan Perjalanan';
                    modalMessage.textContent = 'Apakah Anda yakin ingin membatalkan perjalanan ini?';
                    modalIcon.innerHTML = '<i class="fa-solid fa-exclamation-triangle text-red-600 text-3xl"></i>';
                    alasanContainer.classList.add('hidden');
                }
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100', 'pointer-events-auto');
                setTimeout(() => {
                    modalBox.classList.remove('scale-90');
                    modalBox.classList.add('scale-100');
                }, 10);
            }

            function hideModal() {
                modalBox.classList.remove('scale-100');
                modalBox.classList.add('scale-90');
                setTimeout(() => {
                    modal.classList.remove('opacity-100', 'pointer-events-auto');
                    modal.classList.add('opacity-0', 'pointer-events-none');
                    statusToUpdate = '';
                }, 300);
            }

            function showResponse(type, title, message) {
                if (type === 'success') {
                    responseIcon.className = 'w-16 h-16 bg-green-100 rounded-full mx-auto mb-4 flex items-center justify-center';
                    responseIcon.innerHTML = '<i class="fa-solid fa-check text-green-600 text-2xl"></i>';
                    responseTitle.className = 'text-xl font-bold mb-2 text-green-600';
                } else {
                    responseIcon.className = 'w-16 h-16 bg-red-100 rounded-full mx-auto mb-4 flex items-center justify-center';
                    responseIcon.innerHTML = '<i class="fa-solid fa-times text-red-600 text-2xl"></i>';
                    responseTitle.className = 'text-xl font-bold mb-2 text-red-600';
                }
                
                responseTitle.textContent = title;
                responseMessage.textContent = message;
                
                responseModal.classList.remove('opacity-0', 'pointer-events-none');
                responseModal.classList.add('opacity-100', 'pointer-events-auto');
                setTimeout(() => {
                    responseBox.classList.remove('scale-90');
                    responseBox.classList.add('scale-100');
                }, 10);
            }

            function hideResponse() {
                responseBox.classList.remove('scale-100');
                responseBox.classList.add('scale-90');
                setTimeout(() => {
                    responseModal.classList.remove('opacity-100', 'pointer-events-auto');
                    responseModal.classList.add('opacity-0', 'pointer-events-none');
                }, 300);
            }

            // Event Listeners
            const btnSelesaikan = document.getElementById('btnSelesaikan');
            const btnBatalkan = document.getElementById('btnBatalkan');
            const btnPerbarui = document.getElementById('btnPerbarui');

            if (btnPerbarui) {
                btnPerbarui.addEventListener('click', function(e) {
                    // 1. CEK VALIDASI FORM TERLEBIH DAHULU
                    if (mainForm && !mainForm.checkValidity()) {
                        // Jika ada field kosong/invalid, biarkan browser menampilkan error
                        mainForm.reportValidity();
                        return; // Stop, jangan tampilkan modal
                    }

                    // 2. Jika valid, hentikan submit asli dan tampilkan modal
                    e.preventDefault();
                    showModal('Perbarui');
                });
            }


            if (btnSelesaikan) {
                btnSelesaikan.addEventListener('click', (e) => { e.preventDefault(); showModal('Diselesaikan Manual'); });
            }
            if (btnBatalkan) {
                btnBatalkan.addEventListener('click', (e) => { e.preventDefault(); showModal('Dibatalkan'); });
            }

            modalCancel.addEventListener('click', hideModal);
            modalClose.addEventListener('click', hideModal);
            // Klik luar modal untuk menutup
            modal.addEventListener('click', (e) => { if (e.target === modal) hideModal(); });

            responseOk.addEventListener('click', () => {
                hideResponse();
                location.reload(); // Reload halaman setelah sukses
            });

    // Logic Fetch/Submit (Gabungan)
            modalConfirm.addEventListener('click', function () {
                if (!statusToUpdate) return;
                
                const alasanValue = alasanInput.value;

                // --- LOGIKA 1: SELESAIKAN MANUAL (BUTUH ALASAN) ---
                if (statusToUpdate === 'Diselesaikan Manual' && !alasanValue.trim()) {
                    hideModal();
                    setTimeout(() => showResponse('error', 'Gagal!', 'Wajib mengisi alasan penyelesaian manual!'), 350);
                    return;
                }

                // Siapkan URL dan Body Data
                let fetchUrl = `/pic/penugasan-perjadin/${perjalananId}/status`;
                let fetchMethod = 'PATCH';
                let fetchBody = JSON.stringify({ status: statusToUpdate, alasan: alasanValue });
                let fetchHeaders = { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json', 
                    'X-CSRF-TOKEN': csrfToken 
                };

                // --- LOGIKA 2: PERBARUI (FORM DATA) ---
                if (statusToUpdate === 'Perbarui') {
                    // Gunakan URL dari action form
                    fetchUrl = mainForm.action;
                    
                    // Gunakan FormData untuk menangani File Upload & Input
                    const formData = new FormData(mainForm);
                    
                    // Metode tetap POST di fetch, karena Laravel membaca '_method': 'PATCH' dari dalam FormData
                    fetchMethod = 'POST'; 
                    
                    // Body menggunakan formData, Header Content-Type dihapus agar browser set otomatis boundary-nya
                    fetchBody = formData;
                    fetchHeaders = {
                        'Accept': 'application/json', // Penting agar Laravel membalas JSON jika ada error validasi
                        'X-CSRF-TOKEN': csrfToken
                    };
                }

                // --- EKSEKUSI FETCH (BERLAKU UNTUK SEMUA: UPDATE, SELESAI, BATAL) ---
                modalConfirm.textContent = 'Memproses...';
                modalConfirm.disabled = true;

                fetch(fetchUrl, {
                    method: fetchMethod,
                    headers: fetchHeaders,
                    body: fetchBody
                })
                .then(async res => {
                    const text = await res.text();
                    let json = null;
                    try { json = text ? JSON.parse(text) : null; } catch(e) {}
                    
                    if (!res.ok) {
                        // Jika error validasi Laravel (422)
                        if (res.status === 422 && json && json.errors) {
                             // Ambil error pertama untuk ditampilkan
                             const firstError = Object.values(json.errors)[0][0];
                             throw new Error(firstError);
                        }
                        throw new Error((json && json.message) ? json.message : 'Gagal memproses permintaan');
                    }
                    return json;
                })
                .then(data => {
                    hideModal();
                    setTimeout(() => {
                        const successMsg = data?.message || (statusToUpdate === 'Perbarui' ? 'Data berhasil diperbarui!' : 'Status berhasil diperbarui!');
                        showResponse('success', 'Berhasil!', successMsg);
                    }, 350);
                })
                .catch(err => {
                    console.error(err);
                    hideModal();
                    setTimeout(() => {
                        showResponse('error', 'Gagal!', err.message || 'Terjadi kesalahan sistem');
                    }, 350);
                    modalConfirm.textContent = 'Ya';
                    modalConfirm.disabled = false;
                });
            });
        @endif
    });
    </script>
    @endpush
    </div>
</main>
@endsection