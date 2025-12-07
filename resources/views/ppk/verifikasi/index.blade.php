@extends('layouts.appPPK') 
{{-- Kita pakai layout PIC saja karena strukturnya mirip (Sidebar PPK) --}}

@section('title', 'Verifikasi Keuangan')

@section('content')
<main class="transition-all duration-300 ml-0 sm:ml-[60px]">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-2">
            <div class="mb-1 sm:mb-2">
                <x-page-title title="Verifikasi Keuangan"
                subtitle="Validasi laporan keuangan dan input nomor SP2D." />
            </div>
        </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse($listVerifikasi as $item)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-md transition overflow-hidden flex flex-col">

            <div class="p-6 flex-1">
                <div class="flex justify-between items-start mb-4">
                    <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-0.5 rounded border border-gray-200">
                        {{ $item->nomor_surat }}
                    </span>

                    @php
                        $colorClass = 'text-gray-600'; // Default

                        if($item->nama_status == 'Selesai') {
                            $colorClass = 'text-green-600 bg-green-50 px-2 py-1 rounded border border-green-100';
                            $label = '✓ Terverifikasi';
                        } else {
                            $colorClass = 'text-blue-600 bg-blue-50 px-2 py-1 rounded border border-blue-100';
                            $label = '<i class="fa-solid fa-clock"></i> Butuh Validasi';
                        }
                    @endphp

                    <span class="{!! $colorClass !!} text-xs font-bold flex items-center gap-1">
                        {!! $label !!}
                    </span>
                </div>

                <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2">
                    {{ $item->tujuan }}
                </h3>

                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-calendar"></i>
                        {{ \Carbon\Carbon::parse($item->tgl_mulai)->format('d M') }} -
                        {{ \Carbon\Carbon::parse($item->tgl_selesai)->format('d M Y') }}
                    </div>
                </div>
            </div>

            {{-- FOOTER--}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                @if($item->nama_status == 'Selesai')
                    {{-- JIKA SELESAI: Tombol "Lihat Data" (Aktif, mengarah ke detail) --}}
                    <a href="{{ route('ppk.verifikasi.detail', $item->id) }}"
                       class="block w-full text-center bg-white border border-green-600 text-green-600 hover:bg-green-50 font-semibold py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-eye mr-1"></i> Lihat Data
                    </a>
                @else
                    {{-- JIKA BELUM: Tombol "Verifikasi & Bayar" --}}
                    <a href="{{ route('ppk.verifikasi.detail', $item->id) }}"
                        class="block w-full text-center bg-yellow-600 text-white hover:bg-yellow-700 font-semibold py-2 rounded-lg transition shadow-sm">
                        Verifikasi & Bayar
                    </a>
                @endif
            </div>
        </div>
        @empty
            <div class="col-span-full text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                <p class="text-gray-400 text-lg">Tidak ada laporan yang perlu diverifikasi saat ini.</p>
            </div>
        @endforelse
    </div>

        
    @if ($listVerifikasi instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-6 flex justify-center">

        <nav class="inline-flex items-center bg-blue-50 border border-blue-200 rounded-xl shadow-sm overflow-hidden">

            {{-- Previous --}}
            @if ($listVerifikasi->onFirstPage())
                <span class="px-4 py-2 text-blue-300 cursor-not-allowed"></span>
            @else
                <a href="{{ $listVerifikasi->previousPageUrl() }}"
                class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                            
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($listVerifikasi->toArray()['links'] as $link)
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
            @if ($listVerifikasi->hasMorePages())
                <a href="{{ $listVerifikasi->nextPageUrl() }}"
                class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                >
                </a>
            @else
                <span class="px-4 py-2 text-blue-300 cursor-not-allowed"></span>
            @endif

        </nav>

    </div>
    @endif
    </div>
</main>
@endsection