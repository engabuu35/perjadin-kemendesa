@extends('layouts.app')

@section('content')
{{-- 
  Konten <main>
  class="ml-[80px]" memberi ruang untuk sidebar.
--}}
<main class="item-center max-w-5xl min-h-screen mx-auto px-5 py-8 ml-[80px]">
    
    <!-- Judul -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-gray-700 text-2xl font-bold pb-3 relative">
                Perjalanan Dinas
                <span class="absolute bottom-0 left-0 w-48 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
            </h2>
            <p class="text-gray-500 text-sm">Daftar penugasan perjalanan dinas Anda.</p>
        </div>
    </div>

    <!-- Daftar Kartu Perjalanan -->
    <div class="space-y-6">

        @forelse ($perjalanan_list as $perjalanan)
            
            {{-- Logika Warna Badge & Border --}}
            @php
                $badge_class = 'bg-gray-500'; 
                $border_class = 'border-gray-200';
                $bg_catatan = 'bg-gray-50';
                $text_catatan = 'text-gray-700';

                if ($perjalanan->status_color == 'red') {
                    $badge_class = 'bg-red-500';
                    $border_class = 'border-red-500'; // Border kiri merah
                    $bg_catatan = 'bg-red-50';
                    $text_catatan = 'text-red-700';
                } elseif ($perjalanan->status_color == 'yellow') {
                    $badge_class = 'bg-yellow-500';
                    $border_class = 'border-yellow-500'; // Border kiri kuning
                    $bg_catatan = 'bg-yellow-50';
                    $text_catatan = 'text-yellow-700';
                } elseif ($perjalanan->status_color == 'green') {
                    $badge_class = 'bg-green-600';
                    $border_class = 'border-green-600'; // Border kiri hijau
                    $bg_catatan = 'bg-green-50';
                    $text_catatan = 'text-green-700';
                } elseif ($perjalanan->status_color == 'blue') {
                    $badge_class = 'bg-blue-500';
                    $border_class = 'border-blue-500';
                    $bg_catatan = 'bg-blue-50';
                    $text_catatan = 'text-blue-700';
                }
            @endphp

            <!-- Kartu Perjalanan -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg border border-gray-100">
                
                <!-- Bagian Atas Kartu (Info Utama) -->
                {{-- Border Left tebal sesuai status --}}
                <div class="border-l-[6px] {{ $border_class }} p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        
                        <!-- Info Kiri -->
                        <div class="space-y-3">
                            <h3 class="text-blue-800 font-bold text-xl tracking-wide">{{ $perjalanan->nomor_surat }}</h3>
                            
                            <p class="flex items-center gap-3 text-gray-700 text-base">
                                <i class="fa-solid fa-location-dot w-5 text-center text-gray-400"></i>
                                <span class="font-medium">{{ $perjalanan->lokasi }}</span>
                            </p>
                            <p class="flex items-center gap-3 text-gray-600 text-sm">
                                <i class="fa-regular fa-calendar w-5 text-center text-gray-400"></i>
                                <span>{{ $perjalanan->tanggal }}</span>
                            </p>
                        </div>

                        <!-- Info Kanan (Status & Link) -->
                        <div class="flex flex-col items-start sm:items-end gap-3 w-full sm:w-auto mt-2 sm:mt-0">
                            
                            <!-- Badge Status -->
                            <span class="px-4 py-1.5 text-sm font-bold text-white rounded-full shadow-sm {{ $badge_class }}">
                                {{ $perjalanan->status }}
                            </span>
                            
                            <!-- Link Detail -->
                            <a href="{{ route('perjalanan.detail', $perjalanan->id) }}" class="group flex items-center text-blue-600 hover:text-blue-800 text-sm font-semibold transition-colors mt-1">
                                Lihat Detail
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
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