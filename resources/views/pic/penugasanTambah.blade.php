@extends('layouts.appPIC')

@section('title', isset($perjalanan) ? 'Edit Penugasan Perjalanan Dinas' : 'Tambah Penugasan Perjalanan Dinas')

@section('content')
<div class="max-w-5xl mx-auto px-5 py-8">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-5">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-gray-700 text-2xl font-bold mb-5 pb-4 relative">
        Penugasan Perjalanan Dinas
        <span class="absolute bottom-0 left-0 w-64 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
    </h2>

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
                <label for="tujuan" class="block text-gray-700 text-sm font-medium mb-2">Tujuan</label>
                <input type="text" id="tujuan" name="tujuan"
                    value="{{ old('tujuan', $perjalanan->tujuan ?? '') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                    required>
                @error('tujuan')
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
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                    {{ isset($perjalanan) ? '' : 'required' }}>
                @error('surat_tugas')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror

                @if(isset($perjalanan) && !empty($perjalanan->surat_tugas))
                    <p class="text-sm text-gray-600 mt-2">File saat ini:
                        <a href="{{ asset('storage/'.$perjalanan->surat_tugas) }}" target="_blank" class="text-blue-600 underline">Lihat</a>
                    </p>
                @endif
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
                    class="w-full py-3.5 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                    {{ isset($perjalanan) ? 'Perbarui' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const users = @json($users ?? []);
    const pegawaiStatus = @json($pegawaiStatus ?? []);
    const initialPegawai = @json($pegawaiList ?? []);
    const pegawaiListEl = document.getElementById('pegawaiList');
    const btnTambah = document.getElementById('btnTambahPegawai');

    if (!pegawaiListEl || !btnTambah) {
        console.error('Elemen pegawaiList atau btnTambahPegawai tidak ditemukan.');
        return;
    }

    let pegawaiCount = 0;

    function statusClass(id) {
        id = Number(id || 0);
        if (id === 1) return 'bg-red-500';
        if (id === 2) return 'bg-yellow-500';
        if (id === 4) return 'bg-green-500';
        return 'bg-gray-300';
    }

    function makeUserListItem(user) {
        const st = pegawaiStatus[user.nip] ?? null;
        const dot = statusClass(st);

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

    function buildDropdownList(ulEl, filter) {
        ulEl.innerHTML = '';
        const q = filter.trim().toLowerCase();

        const matched = users.filter(u =>
            !q ||
            u.nip.toLowerCase().includes(q) ||
            u.nama.toLowerCase().includes(q)
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
        const st = pegawaiStatus[nip] ?? null;
        dot.className = 'status-dot w-3 h-3 rounded-full ' + statusClass(st);
    }

    function initCardDropdown(card) {
        const nipInput = card.querySelector('.nip');
        const namaInput = card.querySelector('.nama');
        const dropdown = card.querySelector('.user-dropdown');
        const ul = card.querySelector('.user-list');

        buildDropdownList(ul, '');

        nipInput.addEventListener('click', () => {
            dropdown.classList.remove('hidden');
            buildDropdownList(ul, nipInput.value);
        });

        nipInput.addEventListener('input', () => {
            dropdown.classList.remove('hidden');
            buildDropdownList(ul, nipInput.value);
        });

        ul.addEventListener('click', e => {
            const li = e.target.closest('.user-item');
            if (!li) return;
            nipInput.value = li.dataset.nip;
            namaInput.value = li.dataset.nama;
            dropdown.classList.add('hidden');
            updateStatusDot(card);
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
});
</script>
@endpush
@endsection
