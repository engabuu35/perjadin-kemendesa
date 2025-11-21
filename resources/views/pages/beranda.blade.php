@extends('layouts.app')

@section('content')
{{-- 
  Konten <main>
  class="ml-[80px]" memberi ruang untuk sidebar.
--}}
<main class="item-center max-w-6xl min-h-screen mx-auto px-5 py-8">
    
    <!-- Judul -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-gray-700 text-4xl font-bold pb-3 relative">
                Perjalanan Dinas
                <span class="absolute bottom-0 left-0 w-48 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
            </h2>
            <p class="text-gray-700 text-xl mt-4">Daftar penugasan perjalanan dinas Anda.</p>
        </div>
    </div>

    <!-- Daftar Kartu Perjalanan -->
    <div class="space-y-6">

        @forelse ($perjalanan_list as $perjalanan)
            
            {{-- Logika Warna Badge --}}
            @php
                $badge_class = 'bg-gray-500'; 
                $bg_catatan = 'bg-gray-50';
                $text_catatan = 'text-gray-700';

                if ($perjalanan->status_color == 'red') {
                    $badge_class = 'bg-red-500';
                    $bg_catatan = 'bg-red-50';
                    $text_catatan = 'text-red-700';
                } elseif ($perjalanan->status_color == 'yellow') {
                    $badge_class = 'bg-yellow-500';
                    $bg_catatan = 'bg-yellow-50';
                    $text_catatan = 'text-yellow-700';
                } elseif ($perjalanan->status_color == 'green') {
                    $badge_class = 'bg-green-600';
                    $bg_catatan = 'bg-green-50';
                    $text_catatan = 'text-green-700';
                } elseif ($perjalanan->status_color == 'blue') {
                    $badge_class = 'bg-blue-500';
                    $bg_catatan = 'bg-blue-50';
                    $text_catatan = 'text-blue-700';
                }
            @endphp

            <!-- Kartu Perjalanan -->
            <div class="bg-white rounded-3xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl group border border-gray-100">
                
                <!-- Bagian Atas Kartu (Info Utama) -->
                <div class="border-l-[6px] border-blue-500 p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        
                        <!-- Info Kiri -->
                        <div class="flex-1 space-y-3 min-w-0">
                            <h3 class="text-blue-800 font-bold text-xl tracking-wide group-hover:translate-x-1 transition-transform duration-300 border-b-2 border-blue-200 pb-2 truncate">
                                {{ $perjalanan->nomor_surat }}
                            </h3>
                            
                            <div class="space-y-2">
                                <p class="flex items-start gap-3 text-gray-700 text-base group-hover:translate-x-1 transition-transform duration-300 delay-75">
                                    <i class="fa-solid fa-location-dot w-5 mt-0.5 flex-shrink-0 text-gray-400"></i>
                                    <span class="font-medium break-words">{{ $perjalanan->lokasi }}</span>
                                </p>
                                <p class="flex items-start gap-3 text-gray-600 text-sm group-hover:translate-x-1 transition-transform duration-300 delay-100">
                                    <i class="fa-regular fa-calendar w-5 mt-0.5 flex-shrink-0 text-gray-400"></i>
                                    <span class="break-words">{{ $perjalanan->tanggal }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Info Kanan (Status & Link) -->
                        <div class="flex flex-col items-center sm:items-center gap-3 sm:min-w-[150px]">
                            
                            <!-- Badge Status -->
                            <span class="px-4 py-2 text-sm font-bold text-white rounded-full shadow-md {{ $badge_class }} flex items-center gap-2 hover:brightness-110 hover:scale-105 transition-all duration-200 whitespace-nowrap">
                                @if($perjalanan->status_color == 'red')
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                @elseif($perjalanan->status_color == 'yellow')
                                    <i class="fa-solid fa-spinner text-xs animate-pulse"></i>
                                @elseif($perjalanan->status_color == 'green')
                                    <i class="fa-solid fa-circle-check text-xs"></i>
                                @else
                                    <i class="fa-solid fa-circle text-xs animate-pulse"></i>
                                @endif
                                {{ $perjalanan->status }}
                            </span>
                            
                            <!-- Link Detail -->
                            <a href="{{ route('perjalanan.detail', $perjalanan->id) }}" 
                               class="text-blue-600 hover:text-blue-800 hover:underline text-sm font-semibold transition-all duration-200 flex items-center gap-2 group/link px-2 py-1 rounded hover:bg-blue-50">
                                <span>Lihat Detail</span>
                                <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform duration-200"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Banner Catatan (Bagian Bawah) -->
                <div class="{{ $bg_catatan }} px-6 py-3 border-t border-gray-100">
                    <p class="{{ $text_catatan }} text-sm font-medium flex items-center gap-2">
                        @if($perjalanan->status_color == 'red')
                            <i class="fa-solid fa-circle-exclamation text-lg"></i>
                        @elseif($perjalanan->status_color == 'yellow')
                             <i class="fa-solid fa-spinner fa-spin text-lg"></i>
                        @elseif($perjalanan->status_color == 'green')
                            <i class="fa-solid fa-circle-check text-lg"></i>
                        @else
                            <i class="fa-solid fa-info-circle text-lg"></i>
                        @endif
                        <span>{{ $perjalanan->catatan }}</span>
                    </p>
                </div>

            </div>
            <!-- Akhir Kartu Perjalanan -->

        @empty
            <!-- Tampilan Jika Tidak Ada Data -->
            <div class="bg-white rounded-2xl shadow-sm p-10 text-center border border-gray-100">
                <div class="mb-4">
                    <i class="fa-solid fa-suitcase-rolling text-6xl text-gray-200"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-600">Belum ada Perjalanan Dinas</h3>
                <p class="text-gray-500 mt-2">Saat ini Anda tidak memiliki jadwal perjalanan dinas aktif.</p>
            </div>
        @endforelse

    </div>

</main>
@endsection