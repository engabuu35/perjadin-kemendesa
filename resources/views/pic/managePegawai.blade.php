@extends('layouts.appPIC')
@section('title','Manajemen Pegawai')

@section('content')
<div class="item-center max-w-5xl min-h-screen mx-auto px-5 py-8 ">
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
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-gray-700 text-2xl font-bold pb-3 relative">
                Manajemen Pegawai
                <span class="absolute bottom-0 left-0 w-60 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
            </h2>
            <p class="text-gray-500 text-sm">Kelola data pegawai: tambah, lihat detail, atau hapus.</p>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <!-- search -->
            <form action="{{ route('pic.pegawai.index') }}" method="GET" class="mr-3 w-full sm:w-auto">
                <div class="flex items-center gap-2">
                    <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari NIP atau Nama" class="px-3 py-2 border rounded-lg text-sm w-full sm:w-64">
                    <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm">Cari</button>
                </div>
            </form>

            <!-- Tambah -->
            <a href="{{ route('pic.pegawai.create') }}"
               class="px-4 py-2 border-2 border-dashed border-blue-600 text-blue-700 rounded-2xl hover:bg-blue-50">
                + Tambah
            </a>

            <!-- Bulk delete button (open modal) -->
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
    </div>

    <!-- List of users -->
    <form id="listForm" action="#" method="POST">
        @csrf
        @forelse($users as $user)
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm flex justify-between items-center mb-3">
                <div class="flex items-start gap-4">
                    <input type="checkbox" name="nips[]" value="{{ $user->nip }}" class="select-checkbox">
                    <div>
                        <div class="text-blue-700 font-semibold text-lg">{{ $user->nama }}</div>
                        <div class="text-gray-600 text-sm">NIP {{ $user->nip }}</div>
                        <div class="text-gray-600 text-sm">{{ $user->email ?? '-' }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('pic.pegawai.edit', $user->nip) }}" class="text-blue-700">Edit</a>

                    <!-- single delete: open modal (no immediate submit) -->
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
    </form>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

<!-- Modal Konfirmasi Bulk Delete (sama gaya dengan modal logout) -->
<div id="bulkModal" class="fixed inset-0 bg-black/50 flex items-center justify-center opacity-0 invisible transition-opacity duration-300 z-50">
    <div class="bg-white rounded-lg shadow-lg w-[90%] max-w-sm p-5 text-center">
        <h3 class="text-lg font-bold mb-4 text-gray-800">Konfirmasi Penghapusan</h3>
        <p class="text-gray-600 mb-5">Apakah Anda yakin ingin menghapus pegawai-pegawai yang dipilih? Tindakan ini tidak dapat dibatalkan.</p>

        <div class="flex justify-between gap-3">
            <button id="cancelBulk" class="flex-1 py-2 px-4 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Batal</button>
            <button id="confirmBulk" class="flex-1 py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Hapus</button>
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

<!-- Hidden form for single delete (will be submitted by JS) -->
<form id="singleDeleteForm" action="#" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
// BULK DELETE modal logic
document.getElementById('openBulkModal').addEventListener('click', function () {
    const checked = Array.from(document.querySelectorAll('.select-checkbox:checked')).map(i => i.value);

    if (!checked.length) {
        alert('Pilih minimal satu pegawai.');
        return;
    }

    const form = document.getElementById('bulkDeleteForm');

    // hapus input hidden lama
    form.querySelectorAll('input[name="nips[]"]').forEach(i => i.remove());

    // buat input hidden baru per pegawai
    checked.forEach(nip => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'nips[]';
        input.value = nip;
        form.appendChild(input);
    });

    // tampilkan modal
    const modal = document.getElementById('bulkModal');
    modal.classList.remove('opacity-0', 'invisible');
});

// Cancel bulk modal
document.getElementById('cancelBulk').addEventListener('click', function () {
    const modal = document.getElementById('bulkModal');
    modal.classList.add('opacity-0');
    setTimeout(() => modal.classList.add('invisible'), 300);
});

// Confirm bulk delete -> submit the bulkDeleteForm
document.getElementById('confirmBulk').addEventListener('click', function () {
    document.getElementById('bulkDeleteForm').submit();
});

// SINGLE DELETE modal logic
const singleButtons = document.querySelectorAll('.openSingleDeleteModal');
singleButtons.forEach(btn => {
    btn.addEventListener('click', function () {
        const nip = this.getAttribute('data-nip');
        const nama = this.getAttribute('data-nama');

        // set modal message
        document.getElementById('singleDeleteMessage').innerHTML =
        `Apakah Anda yakin ingin menghapus:<br>
        <span class="text-red-600 font-semibold">${nama}</span><br>
        <span class="text-red-600 font-semibold">NIP: ${nip}</span><br>
        Tindakan ini tidak dapat dibatalkan.`;

        // set form action to the destroy route: /pic/pegawai/{nip}
        const base = "{{ url('pic/pegawai') }}"; // resolves to /pic/pegawai
        const action = base + '/' + encodeURIComponent(nip);
        const form = document.getElementById('singleDeleteForm');
        form.action = action;

        // show modal
        const modal = document.getElementById('singleDeleteModal');
        modal.classList.remove('opacity-0', 'invisible');
    });
});

// Cancel single modal
document.getElementById('cancelSingle').addEventListener('click', function () {
    const modal = document.getElementById('singleDeleteModal');
    modal.classList.add('opacity-0');
    setTimeout(() => modal.classList.add('invisible'), 300);
});

// Confirm single delete -> submit the hidden form
document.getElementById('confirmSingle').addEventListener('click', function () {
    document.getElementById('singleDeleteForm').submit();
});

// Optional: close modals on ESC
document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') {
        const bulk = document.getElementById('bulkModal');
        const single = document.getElementById('singleDeleteModal');
        [bulk, single].forEach(modal => {
            if (!modal.classList.contains('invisible')) {
                modal.classList.add('opacity-0');
                setTimeout(() => modal.classList.add('invisible'), 300);
            }
        });
    }
});
</script>
@endpush
