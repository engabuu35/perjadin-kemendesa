@extends('layouts.appPIC')

@section('title','Manajemen Pegawai')

@section('content')
<main class="transition-all duration-300 ml-0 sm:ml-[60px]">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">

    <div class="flex flex-col gap-0.5 mb-1">
        <x-page-title 
        title="Manajemen Pegawai"
        subtitle="Kelola data pegawai: tambah, edit, atau hapus." />
    </div>

       <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-3">
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

<div class="flex flex-col sm:flex-row gap-3 sm:gap-4 w-full sm:w-auto">
    
    <a href="{{ route('pic.pegawai.create') }}"
       class="w-full sm:w-auto text-center
              px-4 py-2
              border-2 border-dashed border-blue-600
              text-blue-700 rounded-2xl
              hover:bg-blue-50 transition">
        + Tambah
    </a>

    <form id="bulkDeleteForm" action="{{ route('pic.pegawai.bulkDelete') }}" method="POST" class="w-full sm:w-auto">
        @csrf
        <input type="hidden" name="nips[]" id="bulkNips">

        <button type="button" id="openBulkModal"
            class="w-full sm:w-auto
                   flex items-center justify-center gap-2
                   px-4 py-2
                   border-2 border-red-600
                   text-white bg-red-600
                   rounded-2xl hover:bg-red-700 transition">
            <i class="fas fa-trash"></i>
            Hapus
        </button>
    </form>

</div>

            
            <button type="button" id="cancelDeleteModeBtn"
                class="hidden px-4 py-2 border-2 border-gray-400 text-gray-600 bg-gray-100 rounded-2xl hover:bg-gray-200 transition">
                Batal
            </button>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-3">
            {{-- Ikon Ceklis --}}
            <i class="fa-solid fa-circle-check text-green-600 text-lg"></i>
        
            {{-- Teks Pesan --}}
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-green-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-3">
            {{-- Ikon Ceklis --}}
            <i class="fa-solid fa-circle-cross text-red-600 text-lg"></i>
        
            {{-- Teks Pesan --}}
            <span class="font-medium">{{ session('error') }}</span>
        </div>
        @endif

    <div id="userListContainer">
        @forelse($users as $user)
<div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm
            flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-3">
            <div class="flex items-center gap-4">
                <input type="checkbox"
                    name="nips[]"
                    value="{{ $user->nip }}"
                    class="select-checkbox opacity-0 transition-all duration-300">

                <div>
                    <div class="text-blue-700 font-semibold text-lg">
                        {{ $user->nama }}
                    </div>
                    <div class="text-gray-600 text-sm">
                        NIP {{ $user->nip }}
                    </div>
                    <div class="text-gray-600 text-sm">
                        {{ $user->email ?? '-' }}
                    </div>
                </div>
            </div>
            <div class="flex gap-4 sm:gap-3
                        w-full sm:w-auto
                        justify-end sm:justify-start">

                <a href="{{ route('pic.pegawai.edit', $user->nip) }}"
                class="w-full sm:w-auto
                        text-center text-blue-700 font-medium">
                    Edit
                </a>

                <button type="button"
                        class="w-full sm:w-auto
                            text-center text-red-600 font-medium
                            openSingleDeleteModal"
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

    @if ($users->hasPages())
    <div class="mt-6 flex justify-center">
        <nav class="inline-flex items-center bg-blue-50 border border-blue-200 rounded-xl shadow-sm overflow-hidden">
            @if ($users->onFirstPage())
                <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❮</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">❮</a>
            @endif

            @foreach ($users->toArray()['links'] as $link)
                @if ($loop->first || $loop->last) @continue @endif
                @if ($link['active'])
                    <span class="px-4 py-2 bg-blue-600 text-white font-semibold">{{ $link['label'] }}</span>
                @else
                    <a href="{{ $link['url'] }}" class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">{{ $link['label'] }}</a>
                @endif
            @endforeach

            @if ($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">❯</a>
            @else
                <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❯</span>
            @endif
        </nav>
    </div>
    @endif  
</div>

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

<form id="singleDeleteForm" action="#" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
// Toggle Mode Hapus (Checkbox & Tombol Batal)
document.getElementById('openBulkModal').addEventListener('click', function () {
    const checkboxes = document.querySelectorAll('.select-checkbox');
    const cancelBtn = document.getElementById('cancelDeleteModeBtn');

    if (checkboxes[0].classList.contains('opacity-0')) {
        checkboxes.forEach(cb => {
            cb.classList.remove('opacity-0');
            cb.classList.add('opacity-100');
        });
        cancelBtn.classList.remove('hidden');
        return; 
    }

    // Jika diklik lagi, proses hapus
    const form = document.getElementById('bulkDeleteForm');
    form.querySelectorAll('input[name="nips[]"]').forEach(i => i.remove());
    const checked = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);

    if (!checked.length) {
        alert('Pilih minimal satu pegawai untuk dihapus.');
        return;
    }

    checked.forEach(nip => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'nips[]';
        input.value = nip;
        form.appendChild(input);
    });

    document.getElementById('countSelected').textContent = checked.length;
    const modal = document.getElementById('bulkModal');
    modal.classList.remove('opacity-0', 'invisible');
});

// Tombol Batal
document.getElementById('cancelDeleteModeBtn').addEventListener('click', function () {
    const checkboxes = document.querySelectorAll('.select-checkbox');
    checkboxes.forEach(cb => {
        cb.classList.add('opacity-0');
        cb.classList.remove('opacity-100');
        cb.checked = false; 
    });
    this.classList.add('hidden');
});

// Modal Logic
document.getElementById('cancelBulk').addEventListener('click', function () {
    const modal = document.getElementById('bulkModal');
    modal.classList.add('opacity-0');
    setTimeout(() => modal.classList.add('invisible'), 300);
});

document.getElementById('confirmBulk').addEventListener('click', function () {
    document.getElementById('bulkDeleteForm').submit();
});

document.querySelectorAll('.openSingleDeleteModal').forEach(btn => {
    btn.addEventListener('click', function () {
        const nip = this.dataset.nip;
        const nama = this.dataset.nama;
        document.getElementById('singleDeleteMessage').innerHTML =
        `Apakah Anda yakin ingin menghapus:<br><span class="text-red-600 font-semibold">${nama}</span><br><span class="text-red-600 font-semibold">NIP: ${nip}</span>`;
        const base = "{{ url('pic/pegawai') }}";
        document.getElementById('singleDeleteForm').action = `${base}/${nip}`;
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
</div>
@endpush