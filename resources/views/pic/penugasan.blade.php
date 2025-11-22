@extends('layouts.appPIC')

@section('title', 'Penugasan Perjalanan Dinas')

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
        <form action="{{ route('pic.penugasan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Nomor Surat -->
            <div class="mb-5">
                <label for="nomor_surat" class="block text-gray-700 text-sm font-medium mb-2">Nomor Surat Tugas</label>
                <input type="text" id="nomor_surat" name="nomor_surat" 
                    value="{{ old('nomor_surat') }}"
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
                    value="{{ old('tanggal_surat') }}"
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
                    value="{{ old('tujuan') }}"
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
                    value="{{ old('tgl_mulai') }}"
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
                    value="{{ old('tgl_selesai') }}"
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
                    required>
                @error('surat_tugas')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Daftar Pegawai -->
            <div class="mt-8">
                <h3 class="text-gray-700 text-xl font-semibold mb-5">Daftar Pegawai</h3>

                <!-- Datalist di luar -->
                <datalist id="nipList">
                    @foreach($users as $user)
                        <option value="{{ $user->nip }}">
                            {{ $user->nama }} 
                            @if(isset($pegawaiStatus[$user->nip]) && $pegawaiStatus[$user->nip] == 2)
                                (Sedang Berlangsung)
                            @endif
                        </option>
                    @endforeach
                </datalist>

                <datalist id="namaList">
                    @foreach($users as $user)
                        <option value="{{ $user->nama }}">
                            {{ $user->nip }}
                            @if(isset($pegawaiStatus[$user->nip]) && $pegawaiStatus[$user->nip] == 2)
                                (Sedang Berlangsung)
                            @endif
                        </option>
                    @endforeach
                </datalist>

                <div id="pegawaiList">
                    <div class="pegawai-card bg-blue-50 border border-blue-200 rounded-xl p-6 mb-5 relative" data-index="0">
                        <div class="flex justify-end">
                            <button type="button" class="hapus-pegawai px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition text-sm shadow-sm flex items-center gap-2">Hapus</button>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">NIP</label>
                            <input list="nipList" name="pegawai[0][nip]" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition nip">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap</label>
                            <input list="namaList" name="pegawai[0][nama]" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition nama">
                        </div>
                    </div>
                </div>
                    <button type="button" id="btnTambahPegawai" 
                    class="w-full py-3.5 border-2 border-dashed border-blue-700 text-blue-700 rounded-lg font-bold hover:bg-blue-50 transition text-base">
                    + Tambah Pegawai
                </button>
            </div>

            <!-- Submit -->
            <div class="flex flex-col gap-3 mt-8">
                <button type="button" onclick="window.location.reload()" 
                    class="w-full py-3.5 bg-gray-300 text-gray-600 rounded-lg font-semibold hover:bg-gray-400 transition">
                    Batal
                </button>
                <button type="submit" 
                    class="w-full py-3.5 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const users = @json($users);

    document.addEventListener('DOMContentLoaded', function () {
        const pegawaiListEl = document.getElementById('pegawaiList');
        const btnTambah = document.getElementById('btnTambahPegawai');

        if (!pegawaiListEl) {
            console.error('Elemen #pegawaiList tidak ditemukan.');
            return;
        }
        if (!btnTambah) {
            console.error('Tombol #btnTambahPegawai tidak ditemukan.');
            return;
        }

        let pegawaiCount = document.querySelectorAll('.pegawai-card').length || 0;

        function tambahPegawaiInternal() {
            const index = pegawaiCount;
            const div = document.createElement('div');
            div.className = 'pegawai-card bg-blue-50 border border-blue-200 rounded-xl p-6 mb-5 relative';
            div.dataset.index = index;

            div.innerHTML = `
                <div class="flex justify-end">
                    <button 
                        type="button"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold 
                            hover:bg-red-700 transition text-sm shadow-sm flex items-center gap-2">
                        Hapus
                    </button>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-2">NIP</label>
                    <input list="nipList" name="pegawai[${index}][nip]" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition nip">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap</label>
                    <input list="namaList" name="pegawai[${index}][nama]" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition nama">
                </div>
            `;

            pegawaiListEl.appendChild(div);
            pegawaiCount++;
            const newNip = div.querySelector('.nip');
            if (newNip) newNip.focus();
        }

        function reindexPegawai() {
            const cards = document.querySelectorAll('.pegawai-card');
            cards.forEach((card, idx) => {
                card.dataset.index = idx;
                const nipInput = card.querySelector('.nip');
                const namaInput = card.querySelector('.nama');
                if (nipInput) nipInput.name = `pegawai[${idx}][nip]`;
                if (namaInput) namaInput.name = `pegawai[${idx}][nama]`;
            });
            pegawaiCount = cards.length;
        }

        // klik untuk tambah (dari tombol yang sekarang punya id)
        btnTambah.addEventListener('click', function () {
            tambahPegawaiInternal();
        });

        // event delegation: klik hapus
        pegawaiListEl.addEventListener('click', function (e) {
            const hapusBtn = e.target.closest('.hapus-pegawai');
            if (hapusBtn) {
                const card = hapusBtn.closest('.pegawai-card');
                if (card) {
                    card.remove();
                    reindexPegawai();
                }
            }
        });

        // event delegation: input auto-fill NIP <-> Nama
        pegawaiListEl.addEventListener('input', function (e) {
            const target = e.target;
            const card = target.closest('.pegawai-card');
            if (!card) return;

            const findByNip = (val) => users.find(u => String(u.nip).trim() === String(val).trim());
            const findByNama = (val) => users.find(u => String(u.nama).trim().toLowerCase() === String(val).trim().toLowerCase());

            if (target.classList.contains('nip')) {
                const match = findByNip(target.value || '');
                if (match) {
                    const namaEl = card.querySelector('.nama');
                    if (namaEl) namaEl.value = match.nama;
                }
            } else if (target.classList.contains('nama')) {
                const match = findByNama(target.value || '');
                if (match) {
                    const nipEl = card.querySelector('.nip');
                    if (nipEl) nipEl.value = match.nip;
                }
            }
        });

        // reindex awal (jika sudah ada card default)
        reindexPegawai();
    });
</script>
@endpush
@endsection
