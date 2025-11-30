@extends('layouts.appPIC')

@section('title', 'Daftar Pelaporan Masuk')

@section('content')
<main class="ml-0 sm:ml-[80px] max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <x-page-title 
        title="Verifikasi Pelaporan"
        subtitle="Daftar laporan perjalanan dinas yang menunggu verifikasi atau revisi." />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($laporanList as $item)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-md transition overflow-hidden flex flex-col">
            <div class="p-6 flex-1">
                <div class="flex justify-between items-start mb-4">
                    <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-0.5 rounded border border-gray-200">
                        {{ $item->nomor_surat }}
                    </span>
                    
                    {{-- MENGGUNAKAN LOGIKA CUSTOM DARI CONTROLLER --}}
                    {{-- Warna disesuaikan dengan Tailwind classes --}}
                    @php
                        $colorClass = 'text-gray-600'; // Default
                        if($item->status_color == 'red') $colorClass = 'text-red-600 bg-red-50 px-2 py-1 rounded border border-red-100';
                        if($item->status_color == 'blue') $colorClass = 'text-blue-600 bg-blue-50 px-2 py-1 rounded border border-blue-100';
                        if($item->status_color == 'yellow') $colorClass = 'text-yellow-600 bg-yellow-50 px-2 py-1 rounded border border-yellow-100';
                        if($item->status_color == 'indigo') $colorClass = 'text-indigo-600 bg-indigo-50 px-2 py-1 rounded border border-indigo-100';
                    @endphp

                    <span class="{{ $colorClass }} text-xs font-bold flex items-center gap-1">
                        {!!$item->status_icon!!} {{$item->custom_status }}
                    </span>
                </div>
                
                <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2">{{ $item->tujuan }}</h3>
                
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="fa-slab-press fa-regular fa-calendar"></i>
                        {{ \Carbon\Carbon::parse($item->tgl_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($item->tgl_selesai)->format('d M Y') }}
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                @if($item->custom_status == 'Menunggu PPK')
                    <button disabled class="block w-full text-center bg-gray-100 text-gray-400 font-semibold py-2 rounded-lg cursor-not-allowed border border-gray-200">
                        Sedang Diverifikasi PPK
                    </button>
                @else
                    <a href="{{ route('pic.pelaporan.detail', $item->id) }}" class="block w-full text-center bg-white border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white font-semibold py-2 rounded-lg transition">
                        {{ $item->custom_status == 'Perlu Revisi' ? 'Perbaiki Laporan' : 'Proses Laporan' }}
                    </a>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
            <p class="text-gray-400 text-lg">Tidak ada laporan yang perlu diproses saat ini.</p>
        </div>
        @endforelse
    </div>
    
    <div class="mt-6">
        {{ $laporanList->links() }}
    </div>

</main>
@endsection