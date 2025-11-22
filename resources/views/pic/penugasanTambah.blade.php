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
                    {{-- Placeholder card akan digantikan oleh JS jika ada initialPegawai --}}
                    <div class="pegawai-card bg-blue-50 border border-blue-200 rounded-xl p-6 mb-5 relative" data-index="0">
                        <div class="flex justify-end">
                            <button type="button" class="hapus-pegawai px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition text-sm shadow-sm flex items-center gap-2">Hapus</button>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">NIP</label>
                            <div class="relative">
                                <div class="flex items-center gap-3">
                                    <input type="text" name="pegawai[0][nip]" placeholder="Ketik NIP atau cari..." class="nip w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition" autocomplete="off">
                                    <span class="status-dot w-3 h-3 rounded-full inline-block" title="Status"></span>
                                </div>

                                <div class="user-dropdown hidden absolute z-30 left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-56 overflow-auto">
                                    <ul class="user-list divide-y divide-gray-100"></ul>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap</label>
                            <input type="text" name="pegawai[0][nama]" placeholder="Nama lengkap" class="nama w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition" autocomplete="off">
                        </div>
                    </div>
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
    // data dari server
    const users = @json($users ?? []);
    const pegawaiStatus = @json($pegawaiStatus ?? []);
    const initialPegawai = @json($pegawaiList ?? []); // array of {nip,nama} bila edit

    document.addEventListener('DOMContentLoaded', function () {
        const pegawaiListEl = document.getElementById('pegawaiList');
        const btnTambah = document.getElementById('btnTambahPegawai');

        if (!pegawaiListEl || !btnTambah) {
            console.error('Elemen pegawaiList atau btnTambahPegawai tidak ditemukan.');
            return;
        }

        // hitung awal berdasarkan cards yang ada (placeholder 1)
        let pegawaiCount = 0;

        // Utility: mapping status -> kelas warna dot
        function statusClass(id) {
            id = Number(String(id || '').trim() || 0);
            if (id === 1) return 'bg-red-500';     // menunggu/draft
            if (id === 2) return 'bg-yellow-500';  // sedang berlangsung
            if (id === 4) return 'bg-green-500';   // selesai
            return 'bg-gray-300';
        }

        // buat item dropdown user
        function makeUserListItem(user) {
            const statusVal = pegawaiStatus[String(user.nip)] ?? null;
            const dotClass = statusClass(statusVal);
            const titleText = (Number(statusVal) === 2) ? 'Sedang Berlangsung' : (Number(statusVal) === 4) ? 'Selesai' : 'Tidak ada';
            const li = document.createElement('li');
            li.className = 'user-item px-3 py-2 hover:bg-gray-50 cursor-pointer flex items-center gap-3';
            li.dataset.nip = user.nip;
            li.dataset.nama = user.nama;

            li.innerHTML = `
                <span class="w-3 h-3 rounded-full inline-block ${dotClass} flex-shrink-0" title="${titleText}"></span>
                <div class="text-sm">
                    <div class="font-medium text-gray-700">${user.nama}</div>
                    <div class="text-xs text-gray-500">${user.nip}</div>
                </div>
            `;
            return li;
        }

        function buildDropdownList(ulEl, filter) {
            ulEl.innerHTML = '';
            const q = (filter || '').trim().toLowerCase();
            const matched = users.filter(u => {
                if (!q) return true;
                return String(u.nip).toLowerCase().includes(q) || String(u.nama).toLowerCase().includes(q);
            });
            if (matched.length === 0) {
                const empty = document.createElement('li');
                empty.className = 'px-3 py-2 text-sm text-gray-500';
                empty.textContent = 'Tidak ada hasil';
                ulEl.appendChild(empty);
                return;
            }
            const frag = document.createDocumentFragment();
            matched.forEach(u => frag.appendChild(makeUserListItem(u)));
            ulEl.appendChild(frag);
        }

        function makePegawaiCard(index) {
            const div = document.createElement('div');
            div.className = 'pegawai-card bg-blue-50 border border-blue-200 rounded-xl p-6 mb-5 relative';
            div.dataset.index = index;

            div.innerHTML = `
                <div class="flex justify-end">
                    <button type="button" class="hapus-pegawai px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition text-sm shadow-sm">Hapus</button>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-2">NIP</label>
                    <div class="relative">
                        <div class="flex items-center gap-3">
                            <input type="text" name="pegawai[${index}][nip]" placeholder="Ketik NIP atau cari..." class="nip w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition" autocomplete="off">
                            <span class="status-dot w-3 h-3 rounded-full inline-block" title="Status"></span>
                        </div>

                        <div class="user-dropdown hidden absolute z-30 left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-56 overflow-auto">
                            <div class="px-3 py-2">
                                <input type="text" class="dropdown-search w-full px-3 py-2 border border-gray-200 rounded text-sm focus:outline-none" placeholder="Cari nama atau NIP...">
                            </div>
                            <ul class="user-list divide-y divide-gray-100 max-h-48 overflow-auto"></ul>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap</label>
                    <input type="text" name="pegawai[${index}][nama]" placeholder="Nama lengkap" class="nama w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition" autocomplete="off">
                </div>
            `;
            return div;
        }

        // reindex & update names & dots
        function reindexPegawai() {
            const cards = document.querySelectorAll('.pegawai-card');
            cards.forEach((card, idx) => {
                card.dataset.index = idx;
                const nip = card.querySelector('.nip');
                const nama = card.querySelector('.nama');
                if (nip) nip.name = `pegawai[${idx}][nip]`;
                if (nama) nama.name = `pegawai[${idx}][nama]`;
                updateStatusDot(card);
            });
            pegawaiCount = cards.length;
        }

        function updateStatusDot(card) {
            const nipEl = card.querySelector('.nip');
            const namaEl = card.querySelector('.nama');
            const dot = card.querySelector('.status-dot');
            if (!dot) return;

            let key = nipEl && nipEl.value ? String(nipEl.value).trim() : null;

            if (!key && namaEl && namaEl.value) {
                const q = String(namaEl.value).trim().toLowerCase();
                const match = users.find(u => String(u.nama).trim().toLowerCase().includes(q));
                if (match) key = match.nip;
            }

            const st = key && (pegawaiStatus.hasOwnProperty(String(key)) ? pegawaiStatus[String(key)] : null);
            dot.classList.remove('bg-red-500','bg-yellow-500','bg-green-500','bg-gray-300');
            dot.classList.add(statusClass(st));
            if (Number(st) === 2) dot.title = 'Sedang Berlangsung';
            else if (Number(st) === 4) dot.title = 'Selesai';
            else dot.title = 'Tidak sedang perjalanan dinas / Tidak tersedia';
        }

        // initialize dropdown behaviors for a card
        function initCardDropdown(card) {
            const nipInput = card.querySelector('.nip');
            const namaInput = card.querySelector('.nama');
            const dropdown = card.querySelector('.user-dropdown');
            const search = card.querySelector('.dropdown-search');
            const ul = card.querySelector('.user-list');

            // build initial list
            buildDropdownList(ul, '');

            // when nip input focus -> open dropdown
            nipInput.addEventListener('focus', function () {
                dropdown.classList.remove('hidden');
                search.value = '';
                buildDropdownList(ul, nipInput.value || '');
                setTimeout(()=> search.focus(), 30);
            });

            // typing in nip filters
            nipInput.addEventListener('input', function (e) {
                dropdown.classList.remove('hidden');
                buildDropdownList(ul, e.target.value || '');
            });

            // search input filters list
            search.addEventListener('input', function (e) {
                buildDropdownList(ul, e.target.value || '');
            });

            // select user from list
            ul.addEventListener('click', function (e) {
                const li = e.target.closest('li.user-item');
                if (!li) return;
                const chosenNip = li.dataset.nip;
                const chosenNama = li.dataset.nama;
                nipInput.value = chosenNip;
                namaInput.value = chosenNama;

                // trigger input events so status updates
                nipInput.dispatchEvent(new Event('input', { bubbles: true }));
                namaInput.dispatchEvent(new Event('input', { bubbles: true }));

                dropdown.classList.add('hidden');
                updateStatusDot(card);
            });

            // nama manual input tries to autofill nip (fuzzy)
            namaInput.addEventListener('input', function () {
                const q = namaInput.value.trim().toLowerCase();
                if (!q) { updateStatusDot(card); return; }
                const match = users.find(u => String(u.nama).trim().toLowerCase().includes(q));
                if (match) {
                    nipInput.value = match.nip;
                    nipInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
                updateStatusDot(card);
            });

            // update dot on blur after small delay to allow click
            nipInput.addEventListener('blur', function () {
                setTimeout(()=> updateStatusDot(card), 150);
            });

            // init dot
            updateStatusDot(card);
        }

        // single global click handler: close any open dropdowns if click outside
        document.addEventListener('click', function (ev) {
            document.querySelectorAll('.user-dropdown').forEach(dd => {
                if (!dd.contains(ev.target) && !dd.closest('.nip')) {
                    // if clicked outside dropdown and outside associated card input, hide
                    dd.classList.add('hidden');
                }
            });
        });

        // render initialPegawai if provided (edit flow)
        function renderInitialPegawai() {
            if (!Array.isArray(initialPegawai) || initialPegawai.length === 0) {
                // ensure at least one card exists
                if (pegawaiListEl.querySelectorAll('.pegawai-card').length === 0) {
                    const c = makePegawaiCard(0);
                    pegawaiListEl.appendChild(c);
                    initCardDropdown(c);
                    pegawaiCount = 1;
                }
                return;
            }

            // clear existing placeholder(s)
            pegawaiListEl.innerHTML = '';
            initialPegawai.forEach((p, idx) => {
                const card = makePegawaiCard(idx);
                pegawaiListEl.appendChild(card);

                // set values
                const nipEl = card.querySelector('.nip');
                const namaEl = card.querySelector('.nama');
                if (nipEl) nipEl.value = p.nip;
                if (namaEl) namaEl.value = p.nama;

                initCardDropdown(card);
                updateStatusDot(card);
            });
            pegawaiCount = initialPegawai.length;
        }

        // create new card and init
        btnTambah.addEventListener('click', function () {
            const card = makePegawaiCard(pegawaiCount);
            pegawaiListEl.appendChild(card);
            initCardDropdown(card);
            pegawaiCount++;
            const nip = card.querySelector('.nip');
            if (nip) nip.focus();
        });

        // delegation: handle delete button in cards
        pegawaiListEl.addEventListener('click', function (e) {
            const hapus = e.target.closest('.hapus-pegawai');
            if (hapus) {
                const card = hapus.closest('.pegawai-card');
                if (card) {
                    card.remove();
                    reindexPegawai();
                }
            }
        });

        // init: render initial pegawai (or placeholder)
        renderInitialPegawai();

        // also initialize any remaining cards (if placeholder exists)
        document.querySelectorAll('.pegawai-card').forEach(card => {
            // avoid double-init: only init if not already initialized (check dropdown-search listener)
            if (!card.dataset._inited) {
                initCardDropdown(card);
                card.dataset._inited = '1';
            }
        });

        // final reindex to ensure correct names
        reindexPegawai();
    });
</script>
@endpush
@endsection
