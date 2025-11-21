@extends('layouts.app')

{{-- 
  Data dummy untuk daftar perjalanan.
  Nantinya, ini akan Anda kirim dari Controller.
--}}
@section('content')
@php
    $perjalanan_list = [
        (object)[
            'id' => 2,
            'nomor_surat' => '489/PRC.03.01/2024',
            'lokasi' => 'Ogan Komering Ilir',
            'tanggal' => '30 Desember 2024 - 1 Januari 2025',
            'status' => 'On Progress',
            'status_color' => 'yellow',
            'catatan' => 'Surat Tugas Belum Lengkap'
        ]
    ];
@endphp

{{-- 
  Konten <main>
  class="ml-[80px]" PENTING untuk memberi ruang bagi sidebar.
--}}
<main class="item-center max-w-6xl min-h-screen mx-auto px-5 py-8">
    
    <!-- Judul -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-gray-700 text-4xl font-bold pb-3 relative">
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
                $status_bg_color = 'bg-gray-500';
                if ($perjalanan->status_color == 'red') {
                    $status_bg_color = 'bg-red-500';
                } elseif ($perjalanan->status_color == 'yellow') {
                    $status_bg_color = 'bg-yellow-500';
                }
            @endphp

            <!-- Kartu Perjalanan -->
            <div class="rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 group bg-white">
                
                <div class="flex">
                    <!-- Garis Tebal di Samping Kiri -->
                    <div class="w-1.5 bg-blue-600"></div>
                    
                    <!-- Konten Kartu -->
                    <div class="flex-1">
                        <!-- Bagian Info Utama -->
                        <div class="p-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                
                                <!-- Info Kiri -->
                                <div class="flex-1 space-y-2.5">
                                    <h3 class="text-blue-700 font-bold text-xl group-hover:translate-x-1 transition-transform duration-300 border-b-2 border-blue-600 pb-2">
                                        {{ $perjalanan->nomor_surat }}
                                    </h3>
                                    
                                    <div class="space-y-1.5">
                                        <p class="flex items-center gap-3 text-gray-700 text-base group-hover:translate-x-1 transition-transform duration-300 delay-75">
                                            <i class="fa-solid fa-map-marker-alt w-5 text-gray-400"></i>
                                            <span>{{ $perjalanan->lokasi }}</span>
                                        </p>
                                        <p class="flex items-center gap-3 text-gray-700 text-base group-hover:translate-x-1 transition-transform duration-300 delay-100">
                                            <i class="fa-solid fa-calendar-days w-5 text-gray-400"></i>
                                            <span>{{ $perjalanan->tanggal }}</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Info Kanan (Status & Link) -->
                                <div class="flex flex-col items-center gap-3 sm:min-w-[140px]">
                                    
                                    <span class="px-5 py-2 text-sm font-semibold text-white rounded-full {{ $status_bg_color }} shadow-sm flex items-center gap-2 hover:brightness-110 transition-all duration-200">
                                        <i class="fa-solid fa-circle text-xs animate-pulse"></i>
                                        {{ $perjalanan->status }}
                                    </span>
                                    
                                    <a href="{{ route('perjalanan.detail', $perjalanan->id) }}" 
                                       class="text-blue-600 hover:text-blue-700 hover:underline text-sm font-medium transition-all duration-200 flex items-center gap-1.5 group/link">
                                        <span>Lihat Detail</span>
                                        <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform duration-200"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Banner Catatan (Bagian Bawah) -->
                        <div class="bg-red-50 px-6 py-4 border-t border-red-200">
                            <p class="text-red-700 text-base font-medium flex items-center gap-2">
                                <i class="fa-solid fa-circle-exclamation w-4 text-center"></i>
                                <span>{{ $perjalanan->catatan }}</span>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Akhir Kartu Perjalanan -->
        @endforeach

    </div>

</main>
@endsection