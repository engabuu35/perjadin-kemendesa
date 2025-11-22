@extends('layouts.appPIC')

@section('title', 'Manajemen Perjalanan Dinas')

@section('content')
<main class="item-center max-w-5xl min-h-screen mx-auto px-5 py-8 ">

    <!-- Header + Search + Button Tambah -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-gray-700 text-2xl font-bold pb-3 relative">
                Manajemen Perjalanan Dinas
                <span class="absolute bottom-0 left-0 w-60 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
            </h2>
            <p class="text-gray-500 text-sm">Kelola penugasan dan perjalanan dinas pegawai.</p>
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
                        2 => 'bg-yellow-500', // contoh: On Progress
                        3 => 'bg-blue-500',
                        4 => 'bg-green-600',  // Selesai
                    ];
                    $statusClass = $map[$statusId] ?? 'bg-gray-500';
                }

                // default label jika kosong
                $statusLabel = $statusName ?? match($statusId) {
                    1 => 'Menunggu',
                    2 => 'On Progress',
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

                <div class="bg-red-50 px-6 py-4 border-t border-red-200">
                    <p class="text-red-700 text-base font-medium flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation w-4 text-center"></i>
                        <span>{{ $catatan }}</span>
                    </p>
                </div>
            </div>
        @empty
            <div class="bg-white p-6 rounded-lg text-center text-gray-500">
                Belum ada penugasan perjalanan dinas.
            </div>
        @endforelse
    </div>

    <!-- pagination -->
    <div class="mt-6">
        {{ $penugasans->links() }}
    </div>
</main>
@endsection
