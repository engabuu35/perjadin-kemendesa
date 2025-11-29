@extends('layouts.appPIC')
@section('title','Manajemen Pegawai')

@section('content')
<main class="ml-0 sm:ml-[80px] max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header + Search + Buttons -->
     <div class="flex flex-col gap-0.5 mb-1">
        <x-page-title 
        title="Manajemen Pegawai"
        subtitle="Kelola data pegawai: tambah, lihat detail, atau hapus." />
     </div>

       <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-3">
           <!-- Search -->
            <form action="{{ route('pic.pegawai.index') }}" method="GET" class="flex-grow">
                <div class="flex items-center gap-2 w-full">
                    <input type="search" name="q" value="{{ $q ?? '' }}"
                        placeholder="Cari NIP atau Nama"
                        class="px-3 py-2 border rounded-lg text-sm w-full">
                    <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm">
                        Cari
                    </button>
                </div>
            </form>

            <!-- Tambah (PERBAIKAN DI SINI) -->
            <!-- Ditambahkan class: whitespace-nowrap (biar teks gak turun) & flex-shrink-0 (biar tombol gak kegencet) -->
            <a href="{{ route('pic.pegawai.create') }}"
            class="px-4 py-2 border-2 border-dashed border-blue-600 text-blue-700 rounded-2xl hover:bg-blue-50">
                + Tambah
            </a>

            <!-- Bulk delete button -->
            <form id="bulkDeleteForm" action="{{ route('pic.pegawai.bulkDelete') }}" method="POST" class="m-0 p-0">
                @csrf
                <input type="hidden" name="nips[]" id="bulkNips">

                <button type="button" id="openBulkModal"
                    class="flex items-center gap-2 px-4 py-2 border-2 border-red-600 text-white bg-red-600 rounded-2xl hover:bg-red-700">

                    <i class="fas fa-trash"></i>
                    Hapus
                </button>
            </form>
        </div>


    <!-- Form Bulk Delete (Hidden logic) -->
    <form id="bulkDeleteForm" action="{{ route('pic.pegawai.bulkDelete') }}" method="POST" class="hidden">
        @csrf
        <!-- Input hidden NIPs akan diisi via JS -->
    </form>


    <!-- List of users -->
    <div id="userListContainer">
        @forelse($users as $user)
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm flex justify-between items-center mb-3 transition-all">
                <div class="flex items-start gap-4">
                    <input type="checkbox" name="nips[]" value="{{ $user->nip }}" class="select-checkbox opacity-0 transition-all duration-300">
                    <div>
                        <div class="text-blue-700 font-semibold text-lg">{{ $user->nama }}</div>
                        <div class="text-gray-600 text-sm">NIP {{ $user->nip }}</div>
                        <div class="text-gray-600 text-sm">{{ $user->email ?? '-' }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('pic.pegawai.edit', $user->nip) }}" class="text-blue-700">Edit</a>

                    <!-- Single delete button -->
                    <button type="button"
                            class="text-red-600 openSingleDeleteModal"
                            data-nip="{{ $user->nip }}"
                            data-nama="{{ $user->nama }}">
                        Hapus
                    </button>
                </div>
            </div>
        @empty
            <div class="text-gray-500 text-sm text-center py-8">Tidak ada data pegawai.</div>
        @endforelse
    </div>

    <!-- Pagination-->   
    @if ($users->hasPages())
    <div class="mt-6 flex justify-center">

        <nav class="inline-flex items-center bg-blue-50 border border-blue-200 rounded-xl shadow-sm overflow-hidden">

            {{-- Previous --}}
            @if ($users->onFirstPage())
                <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❮</span>
            @else
                <a href="{{ $users->previousPageUrl() }}"
                class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                    ❮
                </a>
            @endif

            {{-- LOOP DARI PAGINATION ARRAY --}}
            @foreach ($users->toArray()['links'] as $link)

                {{-- Skip "Previous" & "Next" default --}}
                @if ($loop->first || $loop->last)
                    @continue
                @endif

                {{-- Active Page --}}
                @if ($link['active'])
                    <span class="px-4 py-2 bg-blue-600 text-white font-semibold">
                        {{ $link['label'] }}
                    </span>
                @else
                    <a href="{{ $link['url'] }}"
                    class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                        {{ $link['label'] }}
                    </a>
                @endif

            @endforeach

            {{-- Next --}}
            @if ($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}"
                class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                    ❯
                </a>
            @else
                <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❯</span>
            @endif

        </nav>
    </div>
    @endif  
</div>

<!-- Modal Konfirmasi Bulk Delete -->
<div id="bulkModal" class="fixed inset-0 bg-black/50 flex items-center justify-center opacity-0 invisible transition-opacity duration-300 z-50">
    <div class="bg-white rounded-lg shadow-lg w-[90%] max-w-sm p-5 text-center">
        <h3 class="text-lg font-bold mb-4 text-gray-800">Konfirmasi Penghapusan</h3>
        <p class="text-gray-600 mb-5">Apakah Anda yakin ingin menghapus <span id="countSelected" class="font-bold"></span> pegawai yang dipilih?</p>

        <div class="flex justify-between gap-3">
            <button id="cancelBulk" class="flex-1 py-2 px-4 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Batal</button>
            <button id="confirmBulk" class="flex-1 py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Ya, Hapus</button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Single Delete -->
<div id="singleDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center opacity-0 invisible transition-opacity duration-300 z-50">
    <div class="bg-white rounded-lg shadow-lg w-[90%] max-w-sm p-5 text-center">
        <h3 class="text-lg font-bold mb-4 text-gray-800">Konfirmasi Penghapusan</h3>
        <p id="singleDeleteMessage" class="text-gray-600 mb-5"></p>

        <div class="flex justify-between gap-3">
            <button id="cancelSingle" class="flex-1 py-2 px-4 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Batal</button>
            <button id="confirmSingle" class="flex-1 py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Hapus</button>
        </div>
    </div>
</div>

<!-- Hidden form for single delete -->
<form id="singleDeleteForm" action="#" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    // --- LOGIKA BARU: TOGGLE DELETE MODE ---
    const toggleBtn = document.getElementById('toggleDeleteModeBtn');
    const cancelBtn = document.getElementById('cancelDeleteModeBtn');
    const deleteBtnText = document.getElementById('deleteBtnText');
    const checkboxes = document.querySelectorAll('.select-checkbox');
    let isDeleteMode = false;

    // Fungsi Toggle Mode
    toggleBtn.addEventListener('click', function() {
        if (!isDeleteMode) {
            // Masuk Mode Hapus
            isDeleteMode = true;
            deleteBtnText.textContent = "Hapus Terpilih"; // Ubah teks tombol
            cancelBtn.classList.remove('hidden'); // Munculkan tombol batal
            
            // Munculkan semua checkbox
            checkboxes.forEach(cb => cb.classList.remove('hidden'));
        } else {
            // Jika sudah mode hapus, tombol ini berfungsi sebagai "Eksekusi Hapus" (Open Modal)
            openBulkModal();
        }
    });

    // Fungsi Batal Mode
    cancelBtn.addEventListener('click', function() {
        isDeleteMode = false;
        deleteBtnText.textContent = "Hapus"; // Kembalikan teks tombol
        cancelBtn.classList.add('hidden'); // Sembunyikan tombol batal
        
        // Sembunyikan & Reset checkbox
        checkboxes.forEach(cb => {
            cb.classList.add('hidden');
            cb.checked = false;
        });
    });

    // --- LOGIKA MODAL BULK DELETE ---
    function openBulkModal() {
        const checked = Array.from(document.querySelectorAll('.select-checkbox:checked')).map(i => i.value);

        if (!checked.length) {
            alert('Pilih minimal satu pegawai untuk dihapus.');
            return;
        }

        const form = document.getElementById('bulkDeleteForm');

        // Reset input hidden lama
        form.querySelectorAll('input[name="nips[]"]').forEach(i => i.remove());

        // Masukkan NIP terpilih ke form hidden
        checked.forEach(nip => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'nips[]';
            input.value = nip;
            form.appendChild(input);
        });

        // Update teks jumlah di modal
        document.getElementById('countSelected').textContent = checked.length;

        // Tampilkan modal
        const modal = document.getElementById('bulkModal');
        modal.classList.remove('opacity-0', 'invisible');
    }

    // Cancel Bulk Modal (Tutup saja, jangan reset mode hapus biar user gak kaget)
    document.getElementById('cancelBulk').addEventListener('click', function () {
        const modal = document.getElementById('bulkModal');
        modal.classList.add('opacity-0');
        setTimeout(() => modal.classList.add('invisible'), 300);
    });

    // Confirm Bulk Delete -> Submit
    document.getElementById('confirmBulk').addEventListener('click', function () {
        document.getElementById('bulkDeleteForm').submit();
    });

    // --- LOGIKA SINGLE DELETE (TETAP SAMA) ---
    const singleButtons = document.querySelectorAll('.openSingleDeleteModal');
    singleButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const nip = this.getAttribute('data-nip');
            const nama = this.getAttribute('data-nama');

            document.getElementById('singleDeleteMessage').innerHTML =
            `Apakah Anda yakin ingin menghapus:<br>
            <span class="text-red-600 font-semibold">${nama}</span><br>
            <span class="text-red-600 font-semibold">NIP: ${nip}</span><br>
            Tindakan ini tidak dapat dibatalkan.`;

            const base = "{{ url('pic/pegawai') }}";
            const action = base + '/' + encodeURIComponent(nip);
            const form = document.getElementById('singleDeleteForm');
            form.action = action;

            const modal = document.getElementById('singleDeleteModal');
            modal.classList.remove('opacity-0', 'invisible');
        });
    });

    document.getElementById('cancelSingle').addEventListener('click', function () {
        const modal = document.getElementById('singleDeleteModal');
        modal.classList.add('opacity-0');
        setTimeout(() => modal.classList.add('invisible'), 300);
    });

    document.getElementById('confirmSingle').addEventListener('click', function () {
        document.getElementById('singleDeleteForm').submit();
    });

    // Close on ESC
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') {
            ['bulkModal', 'singleDeleteModal'].forEach(id => {
                const modal = document.getElementById(id);
                if (!modal.classList.contains('invisible')) {
                    modal.classList.add('opacity-0');
                    setTimeout(() => modal.classList.add('invisible'), 300);
                }
            });
        }
    });
</script>
@endpush
