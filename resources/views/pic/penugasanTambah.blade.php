@extends('layouts.appPIC')

@section('title', isset($perjalanan) ? 'Edit Penugasan Perjalanan Dinas' : 'Tambah Penugasan Perjalanan Dinas')

@section('content')
<div class="max-w-5xl mx-auto px-5 py-8">

    @php
        $disabled = isset($perjalanan) && in_array($perjalanan->id_status, [4,5,6,7,8]);
    @endphp

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-5">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-4">
    <x-page-title title="Penugasan Perjalanan Dinas" />
    <x-back-button />
    </div>

    

    <div class="bg-white rounded-xl p-8" style="box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1),0 4px 6px -2px rgba(0,0,0,0.2);">
        @if(isset($perjalanan))
            <form action="{{ route('pic.penugasan.update', $perjalanan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
        @else
            <form action="{{ route('pic.penugasan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @endif

            <!-- Nomor Surat -->
            <div class="mb-5">
                <label for="nomor_surat" class="block text-gray-700 text-sm font-medium mb-2">Nomor Surat Tugas</label>
                <input type="text" id="nomor_surat" name="nomor_surat"
                    value="{{ old('nomor_surat', $perjalanan->nomor_surat ?? '') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                    required>
                @error('nomor_surat')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tanggal Surat -->
            <div class="mb-5">
                <label for="tanggal_surat" class="block text-gray-700 text-sm font-medium mb-2">Tanggal Surat</label>
                <input type="date" id="tanggal_surat" name="tanggal_surat"
                    value="{{ old('tanggal_surat', isset($perjalanan) ? $perjalanan->tanggal_surat->format('Y-m-d') : '') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                    required>
                @error('tanggal_surat')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tujuan -->
            <div class="mb-5">
                <label for="tujuan" class="block text-gray-700 text-sm font-medium mb-2">Kota Tujuan</label>
                <input type="text" id="tujuan" name="tujuan"
                    value="{{ old('tujuan', $perjalanan->tujuan ?? '') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                    placeholder="Contoh: Antang Kalang, Kalimantan Tengah"
                    required>
                @error('tujuan')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Dalam Rangka (baru) -->
            <div class="mb-5">
                <label for="dalam_rangka" class="block text-gray-700 text-sm font-medium mb-2">Dalam Rangka</label>
                <input type="text" id="dalam_rangka" name="dalam_rangka"
                    value="{{ old('dalam_rangka', $perjalanan->dalam_rangka ?? '') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                    placeholder="Contoh: Kegiatan Koordinasi / Workshop / Supervisi" 
                    required>
                @error('dalam_rangka')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tanggal Mulai -->
            <div class="mb-5">
                <label for="tgl_mulai" class="block text-gray-700 text-sm font-medium mb-2">Tanggal Mulai</label>
                <input type="date" id="tgl_mulai" name="tgl_mulai"
                    value="{{ old('tgl_mulai', isset($perjalanan) ? $perjalanan->tgl_mulai->format('Y-m-d') : '') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                    required>
                @error('tgl_mulai')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tanggal Selesai -->
            <div class="mb-5">
                <label for="tgl_selesai" class="block text-gray-700 text-sm font-medium mb-2">Tanggal Selesai</label>
                <input type="date" id="tgl_selesai" name="tgl_selesai"
                    value="{{ old('tgl_selesai', isset($perjalanan) ? $perjalanan->tgl_selesai->format('Y-m-d') : '') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                    required>
                @error('tgl_selesai')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Surat Tugas (PDF) -->
            <div class="mb-5">
                <label for="surat_tugas" class="block text-gray-700 text-sm font-medium mb-2">Surat Tugas (PDF)</label>
                <input type="file" id="surat_tugas" name="surat_tugas"
                    accept="application/pdf"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                @error('surat_tugas')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror

                @if(isset($perjalanan) && !empty($perjalanan->surat_tugas))
                    <p class="text-sm text-gray-600 mt-2">File saat ini:
                        <a href="{{ asset('storage/'.$perjalanan->surat_tugas) }}" target="_blank" class="text-blue-600 underline">Lihat File</a>
                    </p>
                @endif
            </div>

            <!-- Pimpinan yang Menyetujui / Menandatangani -->
            <div class="mb-5">
                <label for="approved_by" class="block text-gray-700 text-sm font-medium mb-2">Pimpinan yang Menyetujui</label>
                <select id="approved_by" name="approved_by"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                    <option value="">- Pilih Nama Pimpinan -</option>
                    @foreach($pimpinans as $pimpinan)
                        <option value="{{ $pimpinan->nip }}"
                            {{ old('approved_by', $perjalanan->approved_by ?? '') == $pimpinan->nip ? 'selected' : '' }}>
                            {{ $pimpinan->nama }}
                        </option>
                    @endforeach
                </select>
                @error('approved_by')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Daftar Pegawai -->
            <div class="mt-8">
                <h3 class="text-gray-700 text-xl font-semibold mb-5">Daftar Pegawai</h3>

                <div id="pegawaiList">
                </div>

                <button type="button" id="btnTambahPegawai"
                    class="w-full py-3.5 border-2 border-dashed border-blue-700 text-blue-700 rounded-lg font-bold hover:bg-blue-50 transition text-base mt-2">
                    + Tambah Pegawai
                </button>
            </div>

            <!-- Submit -->
            <div class="flex flex-col gap-3 mt-8">
                <a href="{{ route('pic.penugasan') }}" class="w-full py-3.5 bg-gray-300 text-gray-600 rounded-lg font-semibold hover:bg-gray-400 transition text-center">Batal</a>
                <button type="submit"
                    @if($disabled) disabled @endif
                    class="w-full py-3.5 bg-blue-700 text-white rounded-lg font-semibold
                        {{ $disabled ? 'opacity-50 cursor-not-allowed hover:bg-blue-700' : 'hover:bg-blue-800' }} transition">
                    {{ isset($perjalanan) ? 'Perbarui' : 'Simpan' }}
                </button>
            </div>

            @if(isset($perjalanan))
                <div class="flex flex-row gap-3 mt-4">
                    <button type="button" id="btnSelesaikan"
                        @if($disabled) disabled @endif
                        class="w-full py-3.5 bg-yellow-600 text-white rounded-lg font-semibold
                            {{ $disabled ? 'opacity-50 cursor-not-allowed hover:bg-yellow-600' : 'hover:bg-yellow-700' }} transition">
                        Selesaikan Manual
                    </button>
                    <button type="button" id="btnBatalkan"
                        @if($disabled) disabled @endif
                        class="w-full py-3.5 bg-red-600 text-white rounded-lg font-semibold
                            {{ $disabled ? 'opacity-50 cursor-not-allowed hover:bg-red-600' : 'hover:bg-red-700' }} transition">
                        Batalkan Perjalanan
                    </button>
                </div>
            @endif
        </form>

        @if(isset($perjalanan))
        <!-- Modal Konfirmasi -->
        <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg w-96 p-6 relative">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-700 mb-4">Konfirmasi</h3>
                <p id="modalMessage" class="text-gray-600 mb-6">Apakah Anda yakin?</p>
                <div class="flex justify-end gap-3">
                    <button id="modalCancel" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">Batal</button>
                    <button id="modalConfirm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Ya</button>
                </div>
                <button id="modalClose" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const users = @json($users ?? []);
    const pegawaiStatus = @json($pegawaiStatus ?? []);
    const initialPegawai = @json($pegawaiList ?? []);
    const activeNips = @json($pegawaiActive ?? []); // <-- daftar NIP yang punya perjalanan aktif
    const pegawaiListEl = document.getElementById('pegawaiList');
    const btnTambah = document.getElementById('btnTambahPegawai');
    let pegawaiCount = 0;
    let statusToUpdate = '';

    if (!pegawaiListEl || !btnTambah) {
        console.error('Elemen pegawaiList atau btnTambahPegawai tidak ditemukan.');
        return;
    }

    // helper: apakah nip punya perjalanan aktif sekarang?
    function isActiveByNip(nip) {
        if (!nip) return false;
        return activeNips.includes(nip);
    }

    // fallback: jika kita hanya punya id_status di pegawaiStatus, gunakan mapping lama
    function statusClassFromId(id) {
        if ([1,2,3,4,6].includes(Number(id))) return 'bg-red-500';
        return 'bg-blue-500';
    }

    // Utama: tentukan class berdasarkan nip (prioritas) lalu fallback ke id status jika tersedia
    function statusClassForUser(nip) {
        if (isActiveByNip(nip)) return 'bg-red-500';
        const id = pegawaiStatus[nip] ?? null;
        return id ? statusClassFromId(id) : 'bg-blue-500';
    }

    function makeUserListItem(user) {
        const dot = statusClassForUser(user.nip);

        const li = document.createElement('li');
        li.className = 'user-item px-3 py-2 hover:bg-gray-50 cursor-pointer flex items-center gap-3';
        li.dataset.nip = user.nip;
        li.dataset.nama = user.nama;
        li.innerHTML = `
            <span class="w-3 h-3 rounded-full inline-block ${dot}"></span>
            <div class="text-sm">
                <div class="font-medium text-gray-700">${user.nama}</div>
                <div class="text-xs text-gray-500">${user.nip}</div>
            </div>`;
        return li;
    }

    function buildDropdownList(ulEl, filter, currentNip) {
        ulEl.innerHTML = '';
        const q = filter.trim().toLowerCase();
        const selectedNips = Array.from(document.querySelectorAll('.nip'))
            .map(input => input.value.trim())
            .filter(nip => nip && nip !== currentNip);

        const matched = users.filter(u =>
            (!q || u.nip.toLowerCase().includes(q) || u.nama.toLowerCase().includes(q)) &&
            !selectedNips.includes(u.nip)
        );

        if (matched.length === 0) {
            ulEl.innerHTML = `<li class="px-3 py-2 text-gray-500 text-sm">Tidak ada hasil</li>`;
            return;
        }

        matched.forEach(u => ulEl.appendChild(makeUserListItem(u)));
    }

    function makePegawaiCard(index) {
        const div = document.createElement('div');
        div.className = 'pegawai-card bg-blue-50 border border-blue-200 rounded-xl p-6 mb-5';
        div.dataset.index = index;

        div.innerHTML = `
            <div class="flex justify-end">
                <button type="button" class="hapus-pegawai px-4 py-2 bg-red-600 text-white rounded-lg">Hapus</button>
            </div>

            <div class="mb-4">
                <label class="text-sm ml-1 font-medium text-gray-700">NIP</label>
                <div class="relative">
                    <div class="flex items-center gap-3">
                        <input type="text"
                            name="pegawai[${index}][nip]"
                            class="nip w-full text-sm px-4 py-3 mt-2 border rounded-lg"
                            placeholder="Ketik NIP atau Nama..."
                            autocomplete="off">
                        <span class="status-dot w-3 h-3 rounded-full bg-gray-300"></span>
                    </div>

                    <div class="user-dropdown hidden absolute z-30 left-0 right-6 mt-2 
                                bg-white border rounded-lg shadow-lg">
                        <ul class="user-list max-h-56 overflow-auto"></ul>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-sm ml-1 font-medium text-gray-700">Nama Lengkap</label>
                <div class="flex items-center gap-3">
                    <input type="text"
                        name="pegawai[${index}][nama]"
                        placeholder="Nama Lengkap Tanpa Gelar"
                        class="nama w-full text-sm px-4 py-3 mt-2 bg-gray-50 border rounded-lg cursor-not-allowed"
                        readonly>
                    <span class="w-3 h-3"></span>
                </div>
            </div>
        `;
        return div;
    }

    function updateStatusDot(card) {
        const nip = card.querySelector('.nip').value.trim();
        const dot = card.querySelector('.status-dot');

        const cls = statusClassForUser(nip);
        dot.className = 'status-dot w-3 h-3 rounded-full ' + cls;
    }

    function initCardDropdown(card) {
        const nipInput = card.querySelector('.nip');
        const namaInput = card.querySelector('.nama');
        const dropdown = card.querySelector('.user-dropdown');
        const ul = card.querySelector('.user-list');

        buildDropdownList(ul, '', nipInput.value);

        nipInput.addEventListener('click', () => {
            dropdown.classList.remove('hidden');
            buildDropdownList(ul, nipInput.value, nipInput.value);
        });

        nipInput.addEventListener('input', () => {
            dropdown.classList.remove('hidden');
            buildDropdownList(ul, nipInput.value, nipInput.value);
        });

        ul.addEventListener('click', e => {
            const li = e.target.closest('.user-item');
            if (!li) return;

            nipInput.value = li.dataset.nip;
            namaInput.value = li.dataset.nama;
            dropdown.classList.add('hidden');

            // set dot by nip (activeNips) + refresh other dropdowns
            updateStatusDot(card);

            document.querySelectorAll('.pegawai-card').forEach(c => {
                if (c !== card) {
                    const ulOther = c.querySelector('.user-list');
                    const nipOther = c.querySelector('.nip').value;
                    buildDropdownList(ulOther, '', nipOther);
                }
            });
        });

        document.addEventListener('click', e => {
            if (!card.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }

    function renderInitialPegawai() {
        pegawaiListEl.innerHTML = '';

        if (!initialPegawai.length) {
            const c = makePegawaiCard(0);
            pegawaiListEl.appendChild(c);
            initCardDropdown(c);
            pegawaiCount = 1;
            return;
        }

        initialPegawai.forEach((p, idx) => {
            const card = makePegawaiCard(idx);
            pegawaiListEl.appendChild(card);

            card.querySelector('.nip').value = p.nip;
            card.querySelector('.nama').value = p.nama;

            initCardDropdown(card);
            updateStatusDot(card);
        });

        pegawaiCount = initialPegawai.length;
    }

    btnTambah.addEventListener('click', () => {
        const card = makePegawaiCard(pegawaiCount);
        pegawaiListEl.appendChild(card);
        initCardDropdown(card);
        pegawaiCount++;
        card.querySelector('.nip').focus();
    });

    pegawaiListEl.addEventListener('click', e => {
        const btn = e.target.closest('.hapus-pegawai');
        if (!btn) return;
        btn.closest('.pegawai-card').remove();
    });

    renderInitialPegawai();

    // ---------------- Modal Konfirmasi ----------------
    @if(isset($perjalanan))
    const perjalananId = {{ $perjalanan->id }};
    const csrfToken = '{{ csrf_token() }}';

    const modal = document.createElement('div');
    modal.id = 'statusModal';
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center opacity-0 invisible transition-opacity duration-300 z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-lg w-[90%] max-w-sm p-5 text-center relative">
            <h3 id="modalTitle" class="text-lg font-bold mb-4 text-gray-800"></h3>
            <p id="modalMessage" class="text-gray-600 mb-5"></p>
            <div class="flex justify-between gap-3">
                <button id="modalCancel" class="flex-1 py-2 px-4 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Batal</button>
                <button id="modalConfirm" class="flex-1 py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Ya</button>
            </div>
            <button id="modalClose" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>`;
    document.body.appendChild(modal);

    const modalTitle = modal.querySelector('#modalTitle');
    const modalMessage = modal.querySelector('#modalMessage');
    const modalConfirm = modal.querySelector('#modalConfirm');
    const modalCancel = modal.querySelector('#modalCancel');
    const modalClose = modal.querySelector('#modalClose');

    function showModal(status) {
        statusToUpdate = status;
        modalTitle.textContent = status === 'Diselesaikan Manual' ? 'Selesaikan Perjalanan' : 'Batalkan Perjalanan';
        modalMessage.textContent = status === 'Diselesaikan Manual'
            ? 'Apakah Anda yakin ingin menandai perjalanan ini sebagai Diselesaikan Manual?'
            : 'Apakah Anda yakin ingin membatalkan perjalanan ini?';
        modal.classList.remove('opacity-0', 'invisible');
        modal.classList.add('opacity-100', 'visible');
    }

    function hideModal() {
        modal.classList.remove('opacity-100', 'visible');
        modal.classList.add('opacity-0', 'invisible');
        statusToUpdate = '';
    }

    const btnSelesaikan = document.getElementById('btnSelesaikan');
    const btnBatalkan = document.getElementById('btnBatalkan');

    if (btnSelesaikan) btnSelesaikan.addEventListener('click', () => showModal('Diselesaikan Manual'));
    if (btnBatalkan) btnBatalkan.addEventListener('click', () => showModal('Dibatalkan'));
    modalCancel.addEventListener('click', hideModal);
    modalClose.addEventListener('click', hideModal);

    modalConfirm.addEventListener('click', function () {
        if (!statusToUpdate) return;

        fetch(`/pic/penugasan-perjadin/${perjalananId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ status: statusToUpdate })
        })
        .then(async res => {
            const text = await res.text();
            let json = null;
            try { json = text ? JSON.parse(text) : null; } catch(e) {}
            if (!res.ok) {
                const msg = (json && json.message) ? json.message : text || 'Terjadi kesalahan';
                throw new Error(msg);
            }
            return json;
        })
        .then(data => {
            alert(data?.message || 'Status berhasil diubah');
            location.reload();
        })
        .catch(err => {
            console.error(err);
            alert(err.message || 'Terjadi kesalahan');
        });

        hideModal();
    });

    @endif
});
</script>
@endpush
@endsection
