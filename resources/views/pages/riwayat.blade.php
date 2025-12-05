@extends('layouts.app')

@section('content')
@php
    use Carbon\Carbon;
    $tab  = $tab  ?? 'pribadi';
    $role = $role ?? '';
@endphp

<main class="pl-0 sm:pl-[80px] max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
    
    <!-- Header -->
    <div class="mb-3 sm:mb-4">
        <x-page-title
               title="Riwayat Perjalanan Dinas" />
        
        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4 mt-4 sm:mt-6 mb-2 flex items-start gap-2 sm:gap-3 transition-colors hover:bg-blue-100">
            <i class="fa-solid fa-circle-info text-blue-600 text-base sm:text-lg mt-0.5 flex-shrink-0"></i>
            <p class="text-blue-800 text-xs sm:text-sm">
                Riwayat perjalanan dinas yang ditampilkan maksimal untuk 1 tahun terakhir.
            </p>
        </div>

        <!-- Tabs -->
        @if($role === 'PIMPINAN')
            <div class="flex gap-2 sm:gap-3 mb-4 sm:mb-6">
                <a href="{{ route('riwayat', ['tab' => 'pribadi']) }}"
                class="px-4 sm:px-6 py-2 sm:py-2.5 rounded-lg text-sm sm:text-base font-semibold
                {{ $tab === 'pribadi' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-200 text-gray-600 hover:bg-gray-300' }}">
                    Pribadi
                </a>

                <a href="{{ route('riwayat', ['tab' => 'pegawai']) }}"
                class="px-4 sm:px-6 py-2 sm:py-2.5 rounded-lg text-sm sm:text-base font-semibold
                {{ $tab === 'pegawai' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-200 text-gray-600 hover:bg-gray-300' }}">
                    Pegawai
                </a>
            </div>
        @endif


        <!-- Search Form -->
        <form method="GET" action="{{ route('riwayat') }}" class="relative mb-1 sm:mb-1 group">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <i class="fa-solid fa-search absolute left-3 sm:left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm sm:text-base group-focus-within:text-blue-500 transition-colors duration-200"></i>
            <input 
                type="text" 
                name="search"
                value="{{ $search ?? '' }}"
                placeholder="Cari nomor surat atau lokasi..." 
                class="w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400 transition-all duration-200"
            />
        </form>
    </div>

    <!-- Daftar Riwayat -->
    <div class="space-y-4">

        @forelse ($riwayat_list as $riwayat)
            @php
                $lokasi  = $riwayat->tujuan ?? '-';
                $tglMulai   = $riwayat->tgl_mulai ? Carbon::parse($riwayat->tgl_mulai)->translatedFormat('d M Y') : '-';
                $tglSelesai = $riwayat->tgl_selesai ? Carbon::parse($riwayat->tgl_selesai)->translatedFormat('d M Y') : '-';
                $tanggal    = $tglMulai . ' - ' . $tglSelesai;

                // Route detail:
                // - Jika PIMPINAN & tab pegawai → ke halaman detail pimpinan
                // - Selain itu → ke halaman detail perjalanan (pegawai)
                $detailRoute = ($role === 'PIMPINAN' && $tab === 'pegawai')
                    ? route('pimpinan.detail', $riwayat->id)
                    : route('perjalanan.detail', $riwayat->id);
            @endphp
            
            <div class="rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 group" style="background-color: #BCBCBF;">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-1.5">
                        
                        <!-- Info Kiri -->
                        <div class="flex-1 space-y-2.5">
                            <h3 class="text-white font-bold text-xl group-hover:translate-x-1 transition-transform duration-300">
                                {{ $riwayat->nomor_surat ?? 'Nomor surat tidak tersedia' }}
                            </h3>
                            
                            <div class="space-y-1.5">
                                <p class="flex items-center gap-3 text-white text-base group-hover:translate-x-1 transition-transform duration-300 delay-75">
                                    <i class="fa-solid fa-map-marker-alt w-5"></i>
                                    <span>{{ $lokasi }}</span>
                                </p>
                                <p class="flex items-center gap-3 text-white text-base group-hover:translate-x-1 transition-transform duration-300 delay-100">
                                    <i class="fa-solid fa-calendar-days w-5"></i>
                                    <span>{{ $tanggal }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Info Kanan -->
                        <div class="flex flex-col items-start sm:items-end gap-3 sm:min-w-[140px]">
                            <span class="px-5 py-2 text-sm font-semibold text-white rounded-full bg-green-500 shadow-sm flex items-center gap-2 hover:bg-green-600 transition-colors duration-200">
                                <i class="fa-solid fa-circle text-xs animate-pulse"></i>
                                {{ $riwayat->status ?? 'Selesai' }}
                            </span>
                            
                            <a href="{{ $detailRoute }}" 
                               class="text-gray-200 hover:text-white hover:underline text-sm font-medium transition-all duration-200 flex items-center gap-1.5 group/link">
                                <span>Lihat Detail</span>
                                <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform duration-200"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        @empty
           <x-empty-state 
                icon="fa-suitcase-rolling"
                title="Belum ada Riwayat Perjalanan Dinas"
                message="Belum ada riwayat perjalanan dinas yang selesai."
            />
        @endforelse

    </div>

    @if ($riwayat_list instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-6 flex justify-center">

        <nav class="inline-flex items-center bg-blue-50 border border-blue-200 rounded-xl shadow-sm overflow-hidden">

            {{-- Previous --}}
            @if ($riwayat_list->onFirstPage())
                <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❮</span>
            @else
                <a href="{{ $riwayat_list->previousPageUrl() }}"
                class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                ❮
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($riwayat_list->toArray()['links'] as $link)
                @if ($loop->first || $loop->last) @continue @endif

                @if ($link['active'])
                    <span class="px-4 py-2 bg-blue-600 text-white font-semibold">
                        {{ $link['label'] }}
                    </span>
                @else
                    <a href="{{ $link['url'] }}"
                    class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                    {{ $link['label'] }}
                    </a>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($riwayat_list->hasMorePages())
                <a href="{{ $riwayat_list->nextPageUrl() }}"
                class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                ❯
                </a>
            @else
                <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❯</span>
            @endif

        </nav>

    </div>
    @endif


</main>
@endsection
