@extends('layouts.app')

@section('content')

{{-- 
  Data dummy untuk daftar riwayat.
  Nantinya, ini akan Anda kirim dari Controller.
--}}
@php
    $riwayat_list = [
        (object)[
            'id' => 3, // Ganti dengan ID dari database
            'nomor_surat' => '489/PRC.03.01/2024',
            'lokasi' => 'Ogan Komering Ilir',
            'tanggal' => '30 Desember 2024 - 1 Januari 2025',
            'status' => 'Selesai',
        ],
        (object)[
            'id' => 4, // Ganti dengan ID dari database
            'nomor_surat' => '489/PRC.03.01/2024',
            'lokasi' => 'Ogan Komering Ilir',
            'tanggal' => '30 Desember 2024 - 1 Januari 2025',
            'status' => 'Selesai',
        ],
        (object)[
            'id' => 5, // Ganti dengan ID dari database
            'nomor_surat' => '489/PRC.03.01/2024',
            'lokasi' => 'Ogan Komering Ilir',
            'tanggal' => '30 Desember 2024 - 1 Januari 2025',
            'status' => 'Selesai',
        ]
    ];
@endphp

{{-- 
  Konten <main>
  class="ml-[80px]" PENTING untuk memberi ruang bagi sidebar.
--}}
<main class="ml-[80px] max-w-5xl mx-auto px-5 py-8">
    
    <!-- Judul -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-gray-700 text-2xl font-bold pb-3 relative">
                Riwayat Perjalanan Dinas
                <span class="absolute bottom-0 left-0 w-48 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
            </h2>
            <p class="text-gray-500 text-sm">Daftar perjalanan dinas yang telah selesai.</p>
        </div>
    </div>

    <!-- Daftar Kartu Riwayat -->
    <div class="space-y-4">

        @forelse ($riwayat_list as $riwayat)
            
            <!-- Kartu Riwayat -->
            <div class="bg-gray-200 rounded-2xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg">
                
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        
                        <!-- Info Kiri -->
                        <div class="space-y-3">
                            <h3 class="text-gray-800 font-bold text-lg tracking-wide">{{ $riwayat->nomor_surat }}</h3>
                            
                            <p class="flex items-center gap-2 text-gray-700 text-base">
                                <i class="fa-solid fa-map-marker-alt w-4 text-center text-gray-500"></i>
                                <span>{{ $riwayat->lokasi }}</span>
                            </p>
                            <p class="flex items-center gap-2 text-gray-700 text-base">
                                <i class="fa-solid fa-calendar-days w-4 text-center text-gray-500"></i>
                                <span>{{ $riwayat->tanggal }}</span>
                            </p>
                        </div>

                        <!-- Info Kanan (Status & Link) -->
                        <div class="flex flex-col items-start sm:items-end gap-2 w-full sm:w-auto">
                            
                            <span class="px-4 py-1.5 text-sm font-semibold text-white rounded-full bg-green-500">
                                {{ $riwayat->status }}
                            </span>
                            
                            {{-- Link 'Lihat Detail' di-abu-abu-kan seperti di gambar --}}
                            <a href="{{ route('perjalanan.detail', $riwayat->id) }}" class="text-gray-400 hover:text-gray-600 hover:underline text-base font-medium">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Akhir Kartu Riwayat -->
        
        @empty
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center text-gray-500">
                Belum ada riwayat perjalanan dinas yang selesai.
            </div>
        @endforelse

    </div>

</main>

@endsection

{{-- Tidak ada <script> khusus untuk halaman ini --}}