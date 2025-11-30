@extends('layouts.appPIC')

@section('title', 'Tambah Pegawai')

@section('content')
<!-- Main Content -->
<div class="max-w-4xl mx-auto px-5 py-8">
    <div class="flex justify-between items-center mb-6">
        <x-page-title title="Tambah Pegawai" class="!mb-0" />
        <x-back-button />
    </div>

    <div class="bg-white rounded-xl p-8" style="box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.2);">
        <form action="{{ route('pic.pegawai.store') }}" method="POST">
            @csrf

            {{-- Nama --}}
            <div class="mb-5">
                <label for="nama" class="block text-gray-700 text-sm font-medium mb-2">Nama Pegawai</label>
                <input type="text" id="nama" name="nama"
                    value="{{ old('nama') }}"
                    class="w-full px-4 py-3 border @error('nama') border-red-500 @else border-gray-300 @endif rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition" 
                    placeholder="Masukkan Nama Lengkap Tanpa Gelar" required>
                @error('nama')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- NIP --}}
            <div class="mb-5">
                <label for="nip" class="block text-gray-700 text-sm font-medium mb-2">NIP</label>
                <input type="text" id="nip" name="nip"
                    value="{{ old('nip') }}"
                    class="w-full px-4 py-3 border @error('nip') border-red-500 @else border-gray-300 @endif rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                    placeholder="Masukkan NIP" required>
                @error('nip')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-5">
                <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                <input type="email" id="email" name="email"
                    value="{{ old('email', '') }}"
                    class="w-full px-4 py-3 border @error('email') border-red-500 @else border-gray-300 @endif rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                    placeholder="contoh: nama@gmail.com" required>
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- No. Telepon --}}
            <div class="mb-5">
                <label for="no_telp" class="block text-gray-700 text-sm font-medium mb-2">No. Telepon</label>
                <input type="tel" id="no_telp" name="no_telp"
                    value="{{ old('no_telp', '') }}"
                    class="w-full px-4 py-3 border @error('no_telp') border-red-500 @else border-gray-300 @endif rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                    placeholder="0812xxxxxxx"
                    pattern="[0-9+\-()\s]+">
                @error('no_telp')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Role (single-select dropdown) --}}
            <div class="mb-5">
                <label for="role" class="block text-gray-700 text-sm font-medium mb-2">Role</label>

                <select id="role" name="role"
                    class="w-full px-4 py-3 border @error('role') border-red-500 @else border-gray-300 @endif rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                    required>
                    <option value="">- Pilih Role -</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}"
                            {{ (string) old('role', $defaultRole->id ?? '') === (string) $r->id ? 'selected' : '' }}>
                            {{ $r->nama }}
                        </option>
                    @endforeach
                </select>

                <p class="text-xs text-gray-500 mt-2">
                    Pilih satu role untuk pegawai ini (Pegawai, PIC, PPK, atau Pimpinan).
                </p>

                @error('role')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama UKE-1 --}}
            <div class="mb-5">
                <label for="uke1" class="block text-gray-700 text-sm font-medium mb-2">Nama UKE-1</label>

                <select id="uke1" name="uke1"
                    class="w-full px-4 py-3 border @error('uke1') border-red-500 @else border-gray-300 @endif rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                    required>
                    <option value="">- Pilih UKE-1 -</option>
                    @foreach($uke1s as $u1)
                        <option value="{{ $u1->id }}" {{ (string) old('uke1', '') === (string) $u1->id ? 'selected' : '' }}>
                            {{ $u1->nama_uke }}
                        </option>
                    @endforeach
                </select>

                @error('uke1')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama UKE-2 --}}
            <div class="mb-5">
                <label for="uke2" class="block text-gray-700 text-sm font-medium mb-2">Nama UKE-2</label>

                <select id="uke2" name="uke2"
                    class="w-full px-4 py-3 border @error('uke2') border-red-500 @else border-gray-300 @endif rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                    <option value="">- Pilih UKE-2 -</option>
                    {{-- akan dipopulate oleh JS berdasarkan unitKerjaByParent --}}
                </select>

                @error('uke2')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Pangkat/Golongan --}}
            <div class="mb-5">
                <label for="pangkat" class="block text-gray-700 text-sm font-medium mb-2" >Pangkat / Golongan</label>

                <select id="pangkat" name="pangkat" class="w-full px-4 py-3 border rounded-lg text-sm focus:border-blue-600" required>
                    <option value="">- Pilih Pangkat -</option>
                    @foreach($pangkats as $pg)
                        <option value="{{ $pg->id }}" {{ (old('pangkat') == $pg->id) ? 'selected' : '' }}>
                            {{ $pg->nama_pangkat }}
                        </option>
                    @endforeach
                </select>

                @error('pangkat')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex flex-col gap-3 mt-8">
                <a href="{{ route('pic.pegawai.index') }}" class="w-full text-center py-3.5 bg-gray-300 text-gray-600 rounded-lg font-semibold hover:bg-gray-400 transition">
                    Batal
                </a>

                <button type="submit" class="w-full py-3.5 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                    Tambah
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Styles & Script untuk logika checkbox dan UKE dynamic --}}
<style>
    #roleError { margin-top: .25rem; font-size: .75rem; color: #dc2626; }
</style>

{{-- Styles & Script untuk UKE dynamic --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const unitKerjaByParent = {!! $unitKerjaByParent->toJson() !!};
    const uke1El = document.getElementById('uke1');
    const uke2El = document.getElementById('uke2');

    const clearUke2 = () => uke2El.innerHTML = '<option value="">-- Pilih UKE-2 --</option>';

    const populateUke2 = (parentId, selectedId = null) => {
        clearUke2();
        if (!parentId) return;
        const list = unitKerjaByParent[String(parentId)] || [];
        list.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.nama_uke;
            if (selectedId && String(selectedId) === String(item.id)) opt.selected = true;
            uke2El.appendChild(opt);
        });
    };

    uke1El.addEventListener('change', function() {
        populateUke2(this.value);
    });

    // Populate awal jika validasi gagal
    const initialUke1 = {!! json_encode(old('uke1', null)) !!};
    const initialUke2 = {!! json_encode(old('uke2', null)) !!};
    if (initialUke1) {
        uke1El.value = initialUke1;
        populateUke2(initialUke1, initialUke2);
    }
});
</script>

@endsection
