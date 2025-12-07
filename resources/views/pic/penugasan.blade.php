@extends('layouts.appPIC')

@section('title', 'Penugasan Perjalanan Dinas')

@section('content')
<main class="transition-all duration-300 ml-0 sm:ml-[60px] min-h-screen">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">

        <!-- Header + Search + Button Tambah -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex flex-col gap-0.5 mb-1">
            <x-page-title 
            title="Penugasan Perjalanan Dinas"
            subtitle="Kelola data pegawai: tambah, lihat detail, atau hapus." />
        </div>   
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <form action="{{ route('pic.penugasan') }}" method="GET" class="mr-3 w-full sm:w-auto">
                    <div class="flex items-center gap-2">
                        <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari nomor surat atau tujuan..." class="px-3 py-2 border rounded-lg text-sm w-full sm:w-64">
                        <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm">Cari</button>
                    </div>
                </form>

                <a href="{{ route('pic.penugasan.create') }}"
                class="px-5 py-2 border-2 border-dashed border-blue-600 text-blue-700 rounded-2xl hover:bg-blue-50">
                    + Tambah
                </a>
            </div>
        </div>

        <!-- Daftar Kartu Perjalanan -->
        <div class="space-y-6">
            @forelse ($penugasans as $perjalanan)
                @php
                    // preferensi: gunakan relasi status jika tersedia
                    $statusName = $perjalanan->status->nama_status ?? null;
                    $statusId = intval($perjalanan->id_status ?? 0);

                    // jika PerjalananDinas model menyediakan accessor status_class/status_name gunakan itu
                    if (isset($perjalanan->status_name) && $perjalanan->status_name) {
                        $statusName = $perjalanan->status_name;
                    }
                    if (isset($perjalanan->status_class) && $perjalanan->status_class) {
                        $statusClass = $perjalanan->status_class;
                    } else {
                        // fallback mapping berdasarkan id_status
                        $map = [
                            1 => 'bg-red-500',    // contoh: Menunggu / Draft
                            2 => 'bg-yellow-500', // contoh: Sedang Berlangsung
                            3 => 'bg-blue-500',
                            4 => 'bg-green-600',  // Selesai
                        ];
                        $statusClass = $map[$statusId] ?? 'bg-gray-500';
                    }

                    // default label jika kosong
                    $statusLabel = $statusName ?? match($statusId) {
                        1 => 'Menunggu',
                        2 => 'Sedang Berlangsung',
                        4 => 'Selesai',
                        default => 'Draft'
                    };

                    // gunakan tujuan sebagai lokasi jika kolom lokasi tidak ada
                    $lokasi = $perjalanan->tujuan ?? ($perjalanan->lokasi ?? '-');
                    // catatan: cek beberapa kemungkinan field
                    $catatan = $perjalanan->uraian ?? $perjalanan->hasil_perjadin ?? $perjalanan->catatan ?? '-';
                @endphp

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
                    <div class="border-t-4 border-blue-600 p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                            <div class="space-y-3">
                                <h3 class="text-blue-700 font-bold text-lg tracking-wide">{{ $perjalanan->nomor_surat }}</h3>

                                <p class="flex items-center gap-2 text-gray-700 text-base">
                                    <i class="fa-solid fa-map-marker-alt w-4 text-center text-gray-400"></i>
                                    <span>{{ $lokasi }}</span>
                                </p>
                                <p class="flex items-center gap-2 text-gray-700 text-base">
                                    <i class="fa-solid fa-calendar-days w-4 text-center text-gray-400"></i>
                                    <span>{{ \Carbon\Carbon::parse($perjalanan->tgl_mulai)->format('d M Y') }}
                                        - {{ \Carbon\Carbon::parse($perjalanan->tgl_selesai)->format('d M Y') }}</span>
                                </p>
                            </div>

                            <div class="flex flex-col items-start sm:items-end gap-2 w-full sm:w-auto">
                                <span class="item-center px-4 py-1.5 text-sm font-semibold text-white rounded-full {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>

                                <a href="{{ route('pic.penugasan.edit', $perjalanan->id) }}" class="text-blue-600 hover:underline text-base font-medium mr-[30px]">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>

                    @php
                        $miss = $missingUraian[$perjalanan->id] ?? [];
                        $isSuratMissing = $suratMissing[$perjalanan->id] ?? false;
                        $conf = $conflicts[$perjalanan->id] ?? [];
                    @endphp

                    @if(count($miss) || $isSuratMissing || count($conf))
                        <div class="bg-red-50 px-5 py-3 border-t border-red-200">
                            <p class="text-red-700 font-semibold flex items-center gap-2 mb-1 text-sm">
                                <i class="fa-solid fa-circle-exclamation w-3 text-center text-xs"></i>
                                <span>Peringatan</span>
                            </p>

                            <ul class="text-red-700 text-xs list-disc ml-4 space-y-1 leading-relaxed">
                                @if(count($miss))
                                    <li>
                                        Uraian pegawai belum lengkap = NIP
                                        <strong>{{ implode(', ', $miss) }}</strong>.
                                    </li>
                                @endif

                                @if($isSuratMissing)
                                    <li>Surat tugas belum di-upload.</li>
                                @endif

                                @if(count($conf))
                                    <li>
                                        Pegawai dengan NIP <strong>{{ implode(', ', $conf) }}</strong>
                                        memiliki perjalanan dinas pada tanggal yang sama / bersinggungan.
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @else
                        <div class="bg-green-50 px-5 py-3 border-t border-green-200">
                            <p class="text-green-700 text-xs">Semua data lengkap.</p>
                        </div>
                    @endif


                </div>
            @empty
                <x-empty-state 
                    icon="fa-folder-open"
                    title="Belum ada Penugasan Perjalanan Dinas"
                    message="Saat ini tidak ada penugasan perjalanan dinas aktif."
                />
            @endforelse
        </div>

        <!-- pagination -->
        @if ($penugasans->hasPages())
            <div class="mt-6 flex justify-center">

                <nav class="inline-flex items-center bg-blue-50 border border-blue-200 rounded-xl shadow-sm overflow-hidden">

                    {{-- Previous --}}
                    @if ($penugasans->onFirstPage())
                        <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❮</span>
                    @else
                        <a href="{{ $penugasans->previousPageUrl() }}"
                        class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                            ❮
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($penugasans->toArray()['links'] as $link)

                        {{-- Skip "Previous" & "Next" from Laravel --}}
                        @if ($loop->first || $loop->last)
                            @continue
                        @endif

                        {{-- Active --}}
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
                    @if ($penugasans->hasMorePages())
                        <a href="{{ $penugasans->nextPageUrl() }}"
                        class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                            ❯
                        </a>
                    @else
                        <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❯</span>
                    @endif

                </nav>

            </div>
        @endif
    </div>
</main>
@endsection
