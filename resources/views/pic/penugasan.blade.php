@extends('layouts.app')

@section('title', 'Penugasan Perjalanan Dinas')

@section('content')
<!-- Main Content -->
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
    
    <div class="bg-white rounded-xl p-8" style="box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.2);">
            @csrf
            
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

            <div class="mb-5">
                <label for="tanggal" class="block text-gray-700 text-sm font-medium mb-2">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" 
                       value="{{ old('tanggal') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition"
                       required>
                @error('tanggal')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

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

            <!-- Daftar Pegawai -->
            <div class="mt-8">
                <h3 class="text-gray-700 text-xl font-semibold mb-5">Daftar Pegawai</h3>
                
                <div id="pegawaiList">
                    <div class="pegawai-card bg-blue-50 border border-blue-200 rounded-xl p-6 mb-5 relative">
                        <button type="button" onclick="hapusPegawai(this)" 
                                class="absolute top-4 right-4 px-4 py-2 bg-red-500 text-white text-sm rounded-lg font-semibold hover:bg-red-600 transition">
                            Hapus
                        </button>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">NIP</label>
                            <input type="text" name="pegawai[0][nip]" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap</label>
                            <input type="text" name="pegawai[0][nama]" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Nomor Telepon</label>
                            <input type="text" name="pegawai[0][telepon]" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                        </div>
                        
                        <div class="mb-0">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                            <input type="email" name="pegawai[0][email]" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                        </div>
                    </div>
                </div>

                <button type="button" onclick="tambahPegawai()" 
                        class="w-full py-3.5 border-2 border-dashed border-blue-700 text-blue-700 rounded-lg font-bold hover:bg-blue-50 transition text-base">
                    + Tambah Pegawai
                </button>
            </div>

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
    let pegawaiCount = 1;

    function tambahPegawai() {
        const list = document.getElementById('pegawaiList');
        const div = document.createElement('div');
        div.className = 'pegawai-card bg-blue-50 border border-blue-200 rounded-xl p-6 mb-5 relative';
        div.innerHTML = `
            <button type="button" onclick="hapusPegawai(this)" 
                    class="absolute top-4 right-4 px-4 py-2 bg-red-500 text-white text-sm rounded-lg font-semibold hover:bg-red-600 transition">
                Hapus
            </button>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">NIP</label>
                <input type="text" name="pegawai[${pegawaiCount}][nip]" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap</label>
                <input type="text" name="pegawai[${pegawaiCount}][nama]" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Nomor Telepon</label>
                <input type="text" name="pegawai[${pegawaiCount}][telepon]" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
            </div>
            
            <div class="mb-0">
                <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                <input type="email" name="pegawai[${pegawaiCount}][email]" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
            </div>
        `;
        list.appendChild(div);
        pegawaiCount++;
    }

    function hapusPegawai(btn) {
        btn.closest('.pegawai-card').remove();
    }
</script>
@endpush
@endsection