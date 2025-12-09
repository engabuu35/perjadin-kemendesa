@extends('layouts.appPIC')

@section('title', 'Penugasan Perjalanan Dinas')

@section('content')
<main class="transition-all duration-300 ml-0 sm:ml-[60px] min-h-screen">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
            
            {{-- Judul dengan Garis Bawah (Sesuai Request) --}}
             <div class="flex flex-col gap-0.5 mb-1">
            <x-page-title 
            title="Penugasan Perjalanan Dinas"
            subtitle="Kelola perjalanan dinas pegawai: tambah, edit, selesaikan manual, atau batalkan." />
        </div>   

            <a href="{{ route('pic.penugasan.create') }}"
               class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-semibold shadow-md hover:bg-blue-700 hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Penugasan</span>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-gray-800 font-bold text-lg mb-4 flex items-center gap-2 border-b pb-2">
                <i class="fa-solid fa-filter text-blue-600"></i> Filter Data
            </h3>
            
            <form action="{{ route('pic.penugasan') }}" method="GET">
                {{-- Jaga input search jika ada --}}
                @if(request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    
                    {{-- Input Pencarian --}}
                    <div class="md:col-span-5">
                        <label for="q_input" class="block text-sm font-semibold text-gray-600 mb-1">Cari Surat / Tujuan</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input type="text" 
                                   name="q" 
                                   id="q_input"
                                   value="{{ $q ?? '' }}" 
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 transition" 
                                   placeholder="Nomor Surat atau Kota Tujuan...">
                        </div>
                    </div>

                    {{-- Dropdown Status --}}
                    <div class="md:col-span-4">
                        <label for="status" class="block text-sm font-semibold text-gray-600 mb-1">Status Perjadin</label>
                        <select name="status" id="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 transition cursor-pointer">
                            <option value="">-- Semua Status --</option>
                            @foreach($allStatus as $st)
                                <option value="{{ $st->id }}" {{ (isset($statusFilter) && $statusFilter == $st->id) ? 'selected' : '' }}>
                                    {{ $st->nama_status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="md:col-span-3 flex gap-2">
                        {{-- Tombol Terapkan  --}}
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-800 text-white font-semibold rounded-lg transition shadow-sm">
                            Terapkan
                        </button>
                        
                        {{-- Tombol Reset --}}
                        <a href="{{ route('pic.penugasan') }}" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition text-center" title="Reset Filter">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            @forelse ($penugasans as $perjalanan)
                @php
                    // Logika Status & Warna (Bawaan Kode Asli)
                    $statusName = $perjalanan->status->nama_status ?? null;
                    $statusId = intval($perjalanan->id_status ?? 0);

                    if (isset($perjalanan->status_name) && $perjalanan->status_name) {
                        $statusName = $perjalanan->status_name;
                    }
                    if (isset($perjalanan->status_class) && $perjalanan->status_class) {
                        $statusClass = $perjalanan->status_class;
                    } else {
                        $map = [
                            1 => 'bg-red-500',    // Draft / Menunggu
                            2 => 'bg-yellow-500', // Sedang Berlangsung
                            3 => 'bg-blue-500',   
                            4 => 'bg-green-600',  // Selesai
                            7 => 'bg-purple-600', // Manual (Opsional)
                        ];
                        $statusClass = $map[$statusId] ?? 'bg-gray-500';
                    }

                    $statusLabel = $statusName ?? match($statusId) {
                        1 => 'Menunggu',
                        2 => 'Sedang Berlangsung',
                        4 => 'Selesai',
                        default => 'Draft'
                    };
                    $badgeIcon = match (true) {
                        str_contains($statusClass, 'red')    => 'fa-circle-exclamation',
                        str_contains($statusClass, 'yellow') => 'fa-spinner animate-pulse',
                        str_contains($statusClass, 'green')  => 'fa-circle-check',
                        default                              => 'fa-circle',
                    };

                    $lokasi = $perjalanan->tujuan ?? ($perjalanan->lokasi ?? '-');
                @endphp

                {{-- KARTU ASLI (BORDER-T-4) --}}
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
                    <div class="border-t-4 border-blue-600 p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                            <div class="flex-1 space-y-3 min-w-0">
                                <h3 class="text-blue-800 font-bold text-xl tracking-wide border-b-2 border-blue-200 pb-2 truncate w-full">{{ $perjalanan->nomor_surat }}</h3>

                                <p class="flex items-center gap-2 text-gray-700 text-md font-medium">
                                    <i class="fa-solid fa-map-marker-alt w-4 text-center text-gray-400"></i>
                                    <span>{{ $lokasi }}</span>
                                </p>
                                <p class="flex items-center gap-2 text-gray-700 text-sm">
                                    <i class="fa-solid fa-calendar-days w-4 text-center text-gray-400"></i>
                                    <span>{{ \Carbon\Carbon::parse($perjalanan->tgl_mulai)->format('d M Y') }}
                                        - {{ \Carbon\Carbon::parse($perjalanan->tgl_selesai)->format('d M Y') }}</span>
                                </p>
                            </div>

                            <div class="flex flex-col items-center gap-3 sm:min-w-[150px]">
                                <span class="px-4 py-2 text-sm font-bold text-white rounded-full shadow-md {{ $statusClass }} flex items-center gap-2">
                                    <i class="fa-solid {{ $badgeIcon }} text-xs"></i>
                                    {{ $statusLabel }}
                                </span>

                                <a href="{{ route('pic.penugasan.edit', $perjalanan->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline text-sm font-semibold flex items-center gap-2 px-2 py-1 rounded hover:bg-blue-50">
                                    <span>Lihat Detail</span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
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
                            <p class="text-green-700 font-semibold flex items-center gap-2 mb-1 text-sm">
                                <i class="fa-solid fa-circle-exclamation w-3 text-center text-xs"></i>
                                <span>Info</span>
                            </p>
                            <p class="text-green-700 text-xs">Semua data lengkap.</p>
                        </div>
                    @endif
                </div>
            @empty
                <x-empty-state 
                    icon="fa-folder-open"
                    title="Data Tidak Ditemukan"
                    message="Tidak ada data penugasan yang sesuai dengan filter atau pencarian Anda."
                />
            @endforelse
        </div>

        @if ($penugasans->hasPages())
            <div class="mt-8 flex justify-center">
                {{-- Gunakan appends agar filter tidak hilang saat pindah halaman --}}
                <nav class="inline-flex items-center bg-blue-50 border border-blue-200 rounded-xl shadow-sm overflow-hidden">
                    @if ($penugasans->onFirstPage())
                        <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❮</span>
                    @else
                        <a href="{{ $penugasans->appends(request()->query())->previousPageUrl() }}"
                           class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">❮</a>
                    @endif

                    @foreach ($penugasans->toArray()['links'] as $link)
                        @if ($loop->first || $loop->last) @continue @endif
                        @if ($link['active'])
                            <span class="px-4 py-2 bg-blue-600 text-white font-semibold">{{ $link['label'] }}</span>
                        @else
                            <a href="{{ $link['url'] . (strpos($link['url'], '?') ? '&' : '?') . http_build_query(request()->except(['page'])) }}"
                               class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">{{ $link['label'] }}</a>
                        @endif
                    @endforeach

                    @if ($penugasans->hasMorePages())
                        <a href="{{ $penugasans->appends(request()->query())->nextPageUrl() }}"
                           class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">❯</a>
                    @else
                        <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❯</span>
                    @endif
                </nav>
            </div>
        @endif
    </div>
</main>
@endsection