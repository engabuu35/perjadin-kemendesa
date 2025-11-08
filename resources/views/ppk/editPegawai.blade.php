@extends('layouts.app')

@section('title', 'Detail Pegawai')

@section('content')
 <!-- Main Content -->
    <div class="max-w-lg mx-auto px-5 py-8">
        <h2 class="text-gray-700 text-2xl font-bold mb-5 pb-4 relative">
            Detail Pegawai
            <span class="absolute bottom-0 left-0 w-48 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
        </h2>
        
        <div class="bg-white rounded-xl p-8 shadow">
            <form>
                <div class="mb-5">
                    <label for="nama" class="block text-gray-700 text-sm font-medium mb-2">Nama Pegawai</label>
                    <input type="text" id="nama" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                </div>

                <div class="mb-5">
                    <label for="nip" class="block text-gray-700 text-sm font-medium mb-2">NIP</label>
                    <input type="text" id="nip" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                </div>

                <div class="mb-5">
                    <label for="jabatan" class="block text-gray-700 text-sm font-medium mb-2">Jabatan</label>
                    <input type="text" id="jabatan" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                </div>

                <div class="mb-5">
                    <label for="uke1" class="block text-gray-700 text-sm font-medium mb-2">Nama UKE-1</label>
                    <input type="text" id="uke1" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                </div>

                <div class="mb-5">
                    <label for="uke2" class="block text-gray-700 text-sm font-medium mb-2">Nama UKE-2</label>
                    <input type="text" id="uke2" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                </div>

                <div class="mb-5">
                    <label for="pangkat" class="block text-gray-700 text-sm font-medium mb-2">Pangkat Golongan</label>
                    <input type="text" id="pangkat" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition">
                </div>

                <div class="flex flex-col gap-3 mt-8">
                    <button type="button" class="w-full py-3.5 bg-gray-300 text-gray-600 rounded-lg font-semibold hover:bg-gray-400 transition">
                        Batal
                    </button>
                    <button type="submit" class="w-full py-3.5 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection