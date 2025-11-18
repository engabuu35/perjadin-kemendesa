@extends('layouts.app')

@section('content')

{{-- 
  Data dummy untuk daftar riwayat.
  Nantinya, ini akan Anda kirim dari Controller.
--}}
@php
    $riwayat_list = [
        (object)[
            'id' => 3,
            'nomor_surat' => '489/PRC.03.01/2024',
            'lokasi' => 'Ogan Komering Ilir',
            'tanggal' => '30 Desember 2024 - 1 Januari 2025',
            'status' => 'Selesai',
        ],
        (object)[
            'id' => 4,
            'nomor_surat' => '489/PRC.03.01/2024',
            'lokasi' => 'Ogan Komering Ilir',
            'tanggal' => '30 Desember 2024 - 1 Januari 2025',
            'status' => 'Selesai',
        ],
        (object)[
            'id' => 5,
            'nomor_surat' => '489/PRC.03.01/2024',
            'lokasi' => 'Ogan Komering Ilir',
            'tanggal' => '30 Desember 2024 - 1 Januari 2025',
            'status' => 'Selesai',
        ]
    ];
@endphp

{{-- Konten main dengan margin kiri untuk sidebar --}}
<main class="ml-0 sm:ml-[80px] max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
    
    <!-- Header dengan Info dan Tabs -->
    <div class="mb-4 sm:mb-6">
        <h2 class="text-gray-800 text-2xl sm:text-3xl font-bold mb-3 sm:mb-4">
            Riwayat Perjalanan Dinas
        </h2>
        
        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6 flex items-start gap-2 sm:gap-3 transition-colors hover:bg-blue-100">
            <i class="fa-solid fa-circle-info text-blue-600 text-base sm:text-lg mt-0.5 flex-shrink-0"></i>
            <p class="text-blue-800 text-xs sm:text-sm">
                Riwayat perjalanan dinas yang ditampilkan hanya untuk 1 bulan terakhir.
            </p>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 sm:gap-3 mb-4 sm:mb-6">
            <button class="px-4 sm:px-6 py-2 sm:py-2.5 bg-blue-600 text-white text-sm sm:text-base font-semibold rounded-lg shadow-sm hover:bg-blue-700 hover:shadow-md transition-all duration-200 active:scale-95">
                Pribadi
            </button>
            <button class="px-4 sm:px-6 py-2 sm:py-2.5 bg-gray-200 text-gray-600 text-sm sm:text-base font-semibold rounded-lg hover:bg-gray-300 hover:shadow-sm transition-all duration-200 active:scale-95">
                Pegawai
            </button>
        </div>

        <!-- Search Bar -->
        <div class="relative mb-4 sm:mb-6 group">
            <i class="fa-solid fa-search absolute left-3 sm:left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm sm:text-base group-focus-within:text-blue-500 transition-colors duration-200"></i>
            <input 
                type="text" 
                placeholder="Cari nomor surat atau lokasi..." 
                class="w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400 transition-all duration-200"
            />
        </div>
    </div>

    <!-- Daftar Kartu Riwayat -->
    <div class="space-y-4">

        @forelse ($riwayat_list as $riwayat)
            
            <!-- Kartu Riwayat -->
            <div class="rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 group" style="background-color: #BCBCBF;">
                
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        
                        <!-- Info Kiri -->
                        <div class="flex-1 space-y-2.5">
                            <h3 class="text-white font-bold text-xl group-hover:translate-x-1 transition-transform duration-300">
                                {{ $riwayat->nomor_surat }}
                            </h3>
                            
                            <div class="space-y-1.5">
                                <p class="flex items-center gap-3 text-white text-base group-hover:translate-x-1 transition-transform duration-300 delay-75">
                                    <i class="fa-solid fa-map-marker-alt w-5"></i>
                                    <span>{{ $riwayat->lokasi }}</span>
                                </p>
                                <p class="flex items-center gap-3 text-white text-base group-hover:translate-x-1 transition-transform duration-300 delay-100">
                                    <i class="fa-solid fa-calendar-days w-5"></i>
                                    <span>{{ $riwayat->tanggal }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Info Kanan (Status & Link) -->
                        <div class="flex flex-col items-start sm:items-end gap-3 sm:min-w-[140px]">
                            
                            <span class="px-5 py-2 text-sm font-semibold text-white rounded-full bg-green-500 shadow-sm flex items-center gap-2 hover:bg-green-600 transition-colors duration-200">
                                <i class="fa-solid fa-circle text-xs animate-pulse"></i>
                                {{ $riwayat->status }}
                            </span>
                            
                            <a href="{{ route('perjalanan.detail', $riwayat->id) }}" 
                               class="text-gray-200 hover:text-white hover:underline text-sm font-medium transition-all duration-200 flex items-center gap-1.5 group/link">
                                <span>Lihat Detail</span>
                                <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform duration-200"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Akhir Kartu Riwayat -->
        
        @empty
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-500 hover:shadow-md transition-shadow duration-300">
                <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-3"></i>
                <p class="text-lg">Belum ada riwayat perjalanan dinas yang selesai.</p>
            </div>
        @endforelse

    </div>

</main>

@endsection