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
            <x-back-button />
        </div>

        {{-- ================= FORM UTAMA (EDIT/CREATE) ================= --}}
        <div class="bg-white rounded-xl p-8 shadow-lg mb-8">
            @if(isset($perjalanan))
                <form action="{{ route('pic.penugasan.update', $perjalanan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
            @else
                <form action="{{ route('pic.penugasan.store') }}" method="POST" enctype="multipart/form-data">
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
                    <button type="submit" @if($disabled) disabled @endif class="w-full py-3.5 bg-blue-700 text-white rounded-lg font-semibold {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-800' }} transition">{{ isset($perjalanan) ? 'Perbarui' : 'Simpan' }}</button>
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

        {{-- ================= FORM KHUSUS INPUT KEUANGAN MANUAL (PERSISTENT DATA & UPLOAD FILE) ================= --}}
        @if($isManual)
        <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="flex items-center gap-4 mb-6 bg-yellow-50 p-4 rounded-xl border border-yellow-100">
                <div class="w-12 h-12 rounded-full bg-yellow-200 flex items-center justify-center text-yellow-700 shadow-sm">
                    <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Input Keuangan Manual (PIC)</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Status Perjalanan: <span class="font-bold text-yellow-700 uppercase tracking-wider">Diselesaikan Manual</span>.
                    </p>
                    <p class="text-xs text-gray-500 italic mt-0.5">
                        Alasan: "{{ $perjalanan->selesaikan_manual ?? '-' }}"
                    </p>
                </div>
            </div>

            {{-- GUNAKAN ROUTE pic.penugasan.simpanManual SESUAI GROUP WEB.PHP --}}
            <form action="{{ route('pic.penugasan.simpanManual', $perjalanan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-10">
                    @foreach($pegawaiList as $index => $peserta)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        
                        <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-md ring-2 ring-blue-100">
                                    {{ $loop->iteration }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800 text-lg leading-tight">{{ $peserta->nama }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100 font-mono font-medium">
                                            {{ $peserta->nip }}
                                        </span>
                                        <span class="text-[10px] text-gray-400">|</span>
                                        <span class="text-xs text-gray-500">{{ $peserta->role_perjadin ?? 'Anggota' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 grid grid-cols-1 xl:grid-cols-3 gap-8">
                            
                            <div class="xl:col-span-2 space-y-5">
                                <h4 class="font-bold text-gray-700 text-xs uppercase tracking-widest border-b border-gray-100 pb-2 mb-4 flex items-center gap-2">
                                    <i class="fa-solid fa-wallet text-yellow-500"></i> Rincian Biaya & Bukti
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                                    @foreach($catBiaya as $kategori)
                                        @php 
                                            $existingData = $peserta->buktiMap[$kategori] ?? null;
                                            $existingNominal = $existingData ? $existingData->nominal : 0;
                                            // ID unik untuk JS manipulasi file
                                            $uniqueId = $peserta->nip . '-' . str_replace([' ', '(', ')'], '', $kategori);
                                        @endphp
                                        <div class="bg-gray-50/50 p-3 rounded-lg border border-gray-200 relative group hover:border-blue-400 hover:shadow-sm transition-all duration-200">
                                            <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase tracking-wide group-hover:text-blue-600 transition-colors">{{ $kategori }}</label>
                                            
                                            <div class="relative mb-2">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold group-focus-within:text-blue-500">Rp</span>
                                                <input type="text" 
                                                    name="items[{{ $peserta->nip }}][{{ $kategori }}][nominal]" 
                                                    value="{{ $existingNominal ? number_format($existingNominal, 0, ',', '.') : '' }}"
                                                    class="format-rupiah w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all font-semibold text-gray-700"
                                                    placeholder="0">
                                            </div>

                                            <div class="pt-2 border-t border-gray-200">
                                                <div class="flex items-center gap-2">
                                                    <label class="cursor-pointer bg-white border border-gray-300 text-gray-600 text-[10px] font-bold py-1 px-3 rounded hover:bg-gray-100 transition">
                                                        Choose File
                                                        <input type="file" 
                                                            id="file-{{ $uniqueId }}"
                                                            name="items[{{ $peserta->nip }}][{{ $kategori }}][file]" 
                                                            class="hidden"
                                                            onchange="updateFileName('{{ $uniqueId }}')">
                                                    </label>
                                                    <span id="filename-{{ $uniqueId }}" class="text-[10px] text-gray-500 truncate max-w-[150px]">
                                                        Belum ada file baru
                                                    </span>
                                                </div>

                                                @if($existingData && $existingData->path_file)
                                                    <div class="mt-1 flex items-center justify-between bg-green-50 px-2 py-1 rounded border border-green-100">
                                                        <span class="text-[10px] text-green-700 truncate max-w-[120px]">
                                                            <i class="fa-solid fa-check-circle"></i> Tersimpan
                                                        </span>
                                                        <a href="{{ asset('storage/'.$existingData->path_file) }}" target="_blank" 
                                                        class="text-[10px] font-bold text-green-700 hover:underline">Lihat</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="xl:col-span-1 space-y-5">
                                <h4 class="font-bold text-gray-700 text-xs uppercase tracking-widest border-b border-gray-100 pb-2 mb-4 flex items-center gap-2">
                                    <i class="fa-solid fa-circle-info text-blue-500"></i> Data Pendukung
                                </h4>

                                @php
                                    $transportOptions = ['Pesawat', 'Kereta', 'Bus', 'Kapal', 'Dinas', 'Pribadi'];
                                @endphp

                                <div class="space-y-4">
                                    @foreach($catPendukung as $kategori)
                                        @php
                                            $existingData = $peserta->buktiMap[$kategori] ?? null;
                                            $existingText = $existingData ? $existingData->keterangan : '';
                                        @endphp
                                        <div class="group">
                                            <label class="block text-[11px] font-bold text-gray-500 mb-1.5 group-hover:text-blue-600 transition-colors">{{ $kategori }}</label>
                                            
                                            @if(str_contains($kategori, 'Jenis Transportasi'))
                                                <div class="relative">
                                                    <select name="items[{{ $peserta->nip }}][{{ $kategori }}][text]" class="appearance-none w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-100 focus:border-blue-500 bg-white text-gray-700">
                                                        <option value="">- Pilih Jenis -</option>
                                                        @foreach($transportOptions as $opt)
                                                            <option value="{{ $opt }}" {{ $existingText == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                                                </div>
                                            @else
                                                <input type="text" name="items[{{ $peserta->nip }}][{{ $kategori }}][text]" value="{{ $existingText }}" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700" placeholder="-">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-6 p-4 bg-blue-50 text-blue-800 text-xs rounded-lg border border-blue-100 leading-relaxed shadow-sm">
                                    <div class="flex gap-2">
                                        <i class="fa-solid fa-circle-info mt-0.5"></i>
                                        <div>
                                            <strong>Info Pengisian:</strong>
                                            <p class="mt-1 opacity-90">Anda mengisi data ini sebagai pengganti pegawai. Pastikan nominal sesuai dengan bukti fisik yang dipegang.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-10 flex justify-end sticky bottom-4 z-30">
                    <div class="bg-white/80 backdrop-blur-md p-2 rounded-xl border border-gray-200 shadow-xl">
                        <button type="submit" class="bg-blue-700 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-800 transition transform active:scale-95 flex items-center gap-3 text-base shadow-md">
                            <i class="fa-solid fa-check-double"></i> SIMPAN & PROSES VERIFIKASI
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <script>
            // SCRIPT HELPER UNTUK FILE NAME & RUPIAH
            function updateFileName(id) {
                const input = document.getElementById('file-' + id);
                const label = document.getElementById('filename-' + id);
                if (input.files && input.files.length > 0) {
                    label.textContent = input.files[0].name;
                    label.classList.add('text-blue-600', 'font-bold');
                } else {
                    label.textContent = 'Belum ada file baru';
                    label.classList.remove('text-blue-600', 'font-bold');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.format-rupiah').forEach(inp => {
                    inp.addEventListener('input', function() {
                        let v = this.value.replace(/[^0-9]/g, '');
                        this.value = v ? new Intl.NumberFormat('id-ID').format(v) : '';
                    });
                });
            });
        </script>
        @endif
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // === 1. LOGIKA PEGAWAI (ASLI) ===
        const users = @json($users ?? []);
        const pegawaiStatus = @json($pegawaiStatus ?? []);
        const initialPegawai = @json($pegawaiList ?? []);
        const activeNips = @json($pegawaiActive ?? []); 
        const pegawaiListEl = document.getElementById('pegawaiList');
        const btnTambah = document.getElementById('btnTambahPegawai');
        let pegawaiCount = 0;

        if (pegawaiListEl && btnTambah) {
            function isActiveByNip(nip) { return nip && activeNips.includes(nip); }
            function statusClassForUser(nip) {
                if (isActiveByNip(nip)) return 'bg-red-500';
                const id = pegawaiStatus[nip] ?? null;
                return ([1,2,3,4,6].includes(Number(id))) ? 'bg-red-500' : 'bg-blue-500';
            }
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
                const refreshDot = () => { const cls = statusClassForUser(nipInput.value.trim()); dot.className = 'status-dot w-3 h-3 rounded-full ' + cls; };
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

        // === 2. LOGIKA MODAL MANUAL (EDIT ONLY) ===
        @if(isset($perjalanan))
            const perjalananId = {{ $perjalanan->id }};
            const csrfToken = '{{ csrf_token() }}';

            const modal = document.createElement('div');
            modal.id = 'dynamicStatusModal';
            modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center opacity-0 invisible transition-opacity duration-300 z-[9999]';
            modal.innerHTML = `
                <div class="bg-white rounded-lg shadow-lg w-[90%] max-w-sm p-5 text-center relative">
                    <h3 id="dynModalTitle" class="text-lg font-bold mb-4 text-gray-800"></h3>
                    <p id="dynModalMessage" class="text-gray-600 mb-5"></p>
                    <div id="dynAlasanContainer" class="hidden mb-4 text-left">
                        <label class="text-sm font-semibold text-gray-700 block mb-1">Alasan:</label>
                        <textarea id="dynAlasanManual" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600" placeholder="Contoh: Pembatalan sebagian anggota..."></textarea>
                    </div>
                    <div class="flex justify-between gap-3">
                        <button id="dynModalCancel" class="flex-1 py-2 px-4 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Batal</button>
                        <button id="dynModalConfirm" class="flex-1 py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Ya</button>
                    </div>
                    <button id="dynModalClose" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                </div>`;
            document.body.appendChild(modal);

            const modalTitle = modal.querySelector('#dynModalTitle');
            const modalMessage = modal.querySelector('#dynModalMessage');
            const modalConfirm = modal.querySelector('#dynModalConfirm');
            const modalCancel = modal.querySelector('#dynModalCancel');
            const modalClose = modal.querySelector('#dynModalClose');
            const alasanContainer = modal.querySelector('#dynAlasanContainer');
            const alasanInput = modal.querySelector('#dynAlasanManual');
            let statusToUpdate = '';

            function showModal(status) {
                statusToUpdate = status;
                alasanInput.value = ''; 
                if (status === 'Diselesaikan Manual') {
                    modalTitle.textContent = 'Selesaikan Perjalanan';
                    modalMessage.textContent = 'Mohon isi alasan penyelesaian manual:';
                    alasanContainer.classList.remove('hidden');
                } else {
                    modalTitle.textContent = 'Batalkan Perjalanan';
                    modalMessage.textContent = 'Apakah Anda yakin ingin membatalkan perjalanan ini?';
                    alasanContainer.classList.add('hidden');
                }
                modal.classList.remove('opacity-0', 'invisible');
                modal.classList.add('opacity-100', 'visible');
            }
            function hideModal() { modal.classList.remove('opacity-100', 'visible'); modal.classList.add('opacity-0', 'invisible'); statusToUpdate = ''; }

            const btnSelesaikan = document.getElementById('btnSelesaikan');
            const btnBatalkan = document.getElementById('btnBatalkan');

            if (btnSelesaikan) { btnSelesaikan.addEventListener('click', (e) => { e.preventDefault(); showModal('Diselesaikan Manual'); }); }
            if (btnBatalkan) { btnBatalkan.addEventListener('click', (e) => { e.preventDefault(); showModal('Dibatalkan'); }); }

            modalCancel.addEventListener('click', hideModal);
            modalClose.addEventListener('click', hideModal);

            modalConfirm.addEventListener('click', function () {
                if (!statusToUpdate) return;
                const alasanValue = alasanInput.value;
                if (statusToUpdate === 'Diselesaikan Manual' && !alasanValue.trim()) { alert('Wajib mengisi alasan!'); return; }
                
                modalConfirm.textContent = 'Memproses...'; modalConfirm.disabled = true;

                // Route AJAX ke controller updateStatus (pastikan route di web.php benar)
                // Default: /pic/penugasan-perjadin/{id}/status
                fetch(`/pic/penugasan-perjadin/${perjalananId}/status`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ status: statusToUpdate, alasan: alasanValue })
                })
                .then(async res => {
                    const text = await res.text(); let json = null;
                    try { json = text ? JSON.parse(text) : null; } catch(e) {}
                    if (!res.ok) throw new Error((json && json.message) ? json.message : 'Gagal memperbarui status');
                    return json;
                })
                .then(data => { alert(data?.message || 'Berhasil!'); location.reload(); })
                .catch(err => { console.error(err); alert('Error: ' + err.message); modalConfirm.textContent = 'Ya'; modalConfirm.disabled = false; });
            });
        @endif
    });
    </script>
    @endpush
    </div>
</main>
@endsection