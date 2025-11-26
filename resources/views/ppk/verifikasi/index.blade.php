@extends('layouts.appPIC') 
{{-- Kita pakai layout PIC saja karena strukturnya mirip (Sidebar PPK) --}}

@section('title', 'Verifikasi PPK')

@section('content')
<main class="item-center max-w-6xl min-h-screen mx-auto px-5 py-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-gray-700 text-3xl font-bold pb-2 relative">
                Verifikasi Keuangan (PPK)
                <span class="absolute bottom-0 left-0 w-32 h-1 bg-yellow-500 rounded"></span>
            </h2>
            <p class="text-gray-500 mt-2">Validasi laporan keuangan dan input nomor SP2D.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($listVerifikasi as $item)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-md transition overflow-hidden flex flex-col">
            <div class="p-6 flex-1">
                <div class="flex justify-between items-start mb-4">
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-0.5 rounded border border-yellow-200">
                        {{ $item->nomor_surat }}
                    </span>
                    @if($item->nama_status == 'Selesai')
                        <span class="text-green-600 text-xs font-bold flex items-center gap-1">
                            ✓ Terbayar
                        </span>
                    @else
                        <span class="text-blue-600 text-xs font-bold flex items-center gap-1">
                            ⏳ Butuh Validasi
                        </span>
                    @endif
                </div>
                
                <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2">{{ $item->tujuan }}</h3>
                
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <span>📅</span>
                        {{ \Carbon\Carbon::parse($item->tgl_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($item->tgl_selesai)->format('d M Y') }}
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                <a href="{{ route('ppk.verifikasi.detail', $item->id) }}" class="block w-full text-center bg-yellow-600 text-white hover:bg-yellow-700 font-semibold py-2 rounded-lg transition shadow-sm">
                    {{ $item->nama_status == 'Selesai' ? 'Lihat Data' : 'Verifikasi & Bayar' }}
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
            <p class="text-gray-400 text-lg">Tidak ada laporan yang perlu diverifikasi saat ini.</p>
        </div>
        @endforelse
    </div>
    
    <div class="mt-6">
        {{ $listVerifikasi->links() }}
    </div>
</main>
@endsection