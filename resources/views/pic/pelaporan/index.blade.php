@extends('layouts.appPIC')

@section('title', 'Daftar Pelaporan Masuk')

@section('content')
<main class="item-center max-w-6xl min-h-screen mx-auto px-5 py-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-gray-700 text-3xl font-bold pb-2 relative">
                Verifikasi Pelaporan
                <span class="absolute bottom-0 left-0 w-32 h-1 bg-blue-500 rounded"></span>
            </h2>
            <p class="text-gray-500 mt-2">Daftar laporan perjalanan dinas yang menunggu verifikasi atau selesai.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($laporanList as $item)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-md transition overflow-hidden flex flex-col">
            <div class="p-6 flex-1">
                <div class="flex justify-between items-start mb-4">
                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded border border-blue-200">
                        {{ $item->nomor_surat }}
                    </span>
                    @if($item->nama_status == 'Selesai')
                        <span class="text-green-600 text-xs font-bold flex items-center gap-1">
                            ✓ Selesai
                        </span>
                    @else
                        <span class="text-orange-600 text-xs font-bold flex items-center gap-1">
                            ⏳ Menunggu Verifikasi
                        </span>
                    @endif
                </div>
                
                <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2">{{ $item->tujuan }}</h3>
                
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <span class="w-4 text-center">📅</span>
                        {{ \Carbon\Carbon::parse($item->tgl_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($item->tgl_selesai)->format('d M Y') }}
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                <a href="{{ route('pic.pelaporan.detail', $item->id) }}" class="block w-full text-center bg-white border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white font-semibold py-2 rounded-lg transition">
                    Lihat Rincian Keuangan
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <p class="text-gray-400 text-lg">Belum ada laporan yang masuk.</p>
        </div>
        @endforelse
    </div>
    
    <div class="mt-6">
        {{ $laporanList->links() }}
    </div>
</main>
@endsection