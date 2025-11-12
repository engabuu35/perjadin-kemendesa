@extends('layouts.appPegawai')

{{-- 
  Data dummy untuk daftar perjalanan.
  Nantinya, ini akan Anda kirim dari Controller.
--}}
@section('content')
@php
    $perjalanan_list = [
        
        (object)[
            'id' => 2, // Ganti dengan ID dari database
            'nomor_surat' => '489/PRC.03.01/2024',
            'lokasi' => 'Ogan Komering Ilir',
            'tanggal' => '30 Desember 2024 - 1 Januari 2025',
            'status' => 'On Progress',
            'status_color' => 'yellow', // 'red' atau 'yellow'
            'catatan' => 'Surat Tugas Belum Lengkap'
        ]
    ];
@endphp

{{-- 
  Konten <main>
  class="ml-[80px]" PENTING untuk memberi ruang bagi sidebar.
  Ini juga mengikuti pola 'detailperjadin.blade.php'
--}}
<main class="item-center max-w-5xl min-h-screen mx-auto px-5 py-8 ">
    
    <!-- Judul -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-gray-700 text-2xl font-bold pb-3 relative">
                Perjalanan Dinas
                <span class="absolute bottom-0 left-0 w-48 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
            </h2>
            <p class="text-gray-500 text-sm">Daftar perjalanan dinas Anda yang sedang berjalan.</p>
        </div>
    </div>

    <!-- Daftar Kartu Perjalanan -->
    <div class="space-y-6">

        @foreach ($perjalanan_list as $perjalanan)
            
            {{-- Menentukan warna badge status --}}
            @php
                $status_bg_color = 'bg-gray-500'; // Default
                if ($perjalanan->status_color == 'red') {
                    $status_bg_color = 'bg-red-500';
                } elseif ($perjalanan->status_color == 'yellow') {
                    $status_bg_color = 'bg-yellow-500';
                }
            @endphp

            <!-- Kartu Perjalanan -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
                
                <!-- Bagian Atas Kartu (Info Utama) -->
                <div class="border-t-4 border-blue-600 p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        
                        <!-- Info Kiri -->
                        <div class="space-y-3">
                            <h3 class="text-blue-700 font-bold text-lg tracking-wide">{{ $perjalanan->nomor_surat }}</h3>
                            
                            {{-- PERBAIKAN: Font dibesarkan dari text-sm ke text-base --}}
                            <p class="flex items-center gap-2 text-gray-700 text-base">
                                <i class="fa-solid fa-map-marker-alt w-4 text-center text-gray-400"></i>
                                <span>{{ $perjalanan->lokasi }}</span>
                            </p>
                            <p class="flex items-center gap-2 text-gray-700 text-base">
                                <i class="fa-solid fa-calendar-days w-4 text-center text-gray-400"></i>
                                <span>{{ $perjalanan->tanggal }}</span>
                            </p>
                        </div>

                        <!-- Info Kanan (Status & Link) -->
                        {{-- PERBAIKAN: gap-2 untuk merapatkan link & badge --}}
                        <div class="flex flex-col items-start sm:items-end gap-2 w-full sm:w-auto">
                            
                            {{-- PERBAIKAN: Font dibesarkan (text-sm), padding ditambah (px-4 py-1.5) --}}
                            <span class="item-center px-4 py-1.5 text-sm font-semibold text-white rounded-full {{ $status_bg_color }}">
                                {{ $perjalanan->status }}
                            </span>
                            
                            {{-- PERBAIKAN: Font dibesarkan (text-base) agar seimbang --}}
                            <a href="{{ route('perjalanan.detail', $perjalanan->id) }}" class="text-blue-600 hover:underline text-base font-medium mr-[30px]">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Banner Catatan (Bagian Bawah) -->
                <div class="bg-red-50 px-6 py-4 border-t border-red-200">
                    {{-- PERBAIKAN: Font dibesarkan (text-base) --}}
                    <p class="text-red-700 text-base font-medium flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation w-4 text-center"></i>
                        <span>{{ $perjalanan->catatan }}</span>
                    </p>
                </div>

            </div>
            <!-- Akhir Kartu Perjalanan -->
        @endforeach

    </div>

</main>
@endsection