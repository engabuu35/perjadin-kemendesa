@extends('layouts.app')

@section('title', 'Detail Pegawai')

@section('content')
<!-- Main Content -->
<div class="max-w-4xl mx-auto px-5 py-8">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-5">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-5 pb-4">
        <h2 class="text-gray-700 text-2xl font-bold relative">
            Detail Pegawai
            <span class="absolute bottom-0 left-0 w-48 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
        </h2>
        <button type="button" onclick="toggleEdit()" class="px-6 py-2 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
            Edit
        </button>
    </div>
    
    <div class="bg-white rounded-xl p-8" style="box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.2);">
            @csrf
            @method('PUT')
            
            <div class="mb-5">
                <label for="namaPegawai" class="block text-gray-700 text-sm font-medium mb-2">Nama Pegawai</label>
                <input type="text" id="namaPegawai" name="namaPegawai" 
                       value="{{ old('namaPegawai', $pegawai->namaPegawai ?? 'Amanda Atika Putri') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                       disabled>
            </div>

            <div class="mb-5">
                <label for="nip" class="block text-gray-700 text-sm font-medium mb-2">NIP</label>
                <input type="text" id="nip" name="nip" 
                       value="{{ old('nip', $pegawai->nip ?? '0000000000000') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                       disabled>
            </div>

            <div class="mb-5">
                <label for="jabatan" class="block text-gray-700 text-sm font-medium mb-2">Jabatan</label>
                <input type="text" id="jabatan" name="jabatan" 
                       value="{{ old('jabatan', $pegawai->jabatan ?? 'Staff PPK') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                       disabled>
            </div>

            <div class="mb-5">
                <label for="namaUke1" class="block text-gray-700 text-sm font-medium mb-2">Nama UKE-1</label>
                <input type="text" id="namaUke1" name="namaUke1" 
                       value="{{ old('namaUke1', $pegawai->namaUke1 ?? 'Inspektorat Jendral') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                       disabled>
            </div>

            <div class="mb-5">
                <label for="namaUke2" class="block text-gray-700 text-sm font-medium mb-2">Nama UKE-2</label>
                <input type="text" id="namaUke2" name="namaUke2" 
                       value="{{ old('namaUke2', $pegawai->namaUke2 ?? 'Sekretariat Inspektorat Jendral') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                       disabled>
            </div>

            <div class="mb-5">
                <label for="pangkatGolongan" class="block text-gray-700 text-sm font-medium mb-2">Pangkat Golongan</label>
                <input type="text" id="pangkatGolongan" name="pangkatGolongan" 
                       value="{{ old('pangkatGolongan', $pegawai->pangkatGolongan ?? 'III') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                       disabled>
            </div>

            <div class="flex flex-col gap-3 mt-8" id="formActions">
                <button type="button" onclick="cancelEdit()" 
                        class="w-full py-3.5 bg-gray-300 text-gray-600 rounded-lg font-semibold hover:bg-gray-400 transition" 
                        disabled>
                    Batal
                </button>
                <button type="submit" 
                        class="w-full py-3.5 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 transition" 
                        disabled>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleEdit() {
        const inputs = document.querySelectorAll('#pegawaiForm input');
        const buttons = document.querySelectorAll('#formActions button');
        
        inputs.forEach(input => {
            input.disabled = !input.disabled;
        });
        
        buttons.forEach(button => {
            button.disabled = !button.disabled;
        });
    }

    function cancelEdit() {
        location.reload();
    }
</script>
@endpush
@endsection