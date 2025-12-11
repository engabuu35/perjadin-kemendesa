@props(['perjalanan'])

@php
    $statusClass = $perjalanan->status_class;
    $statusText  = $perjalanan->status;

    $badgeIcon = match (true) {
        str_contains($statusClass, 'red')    => 'fa-circle-exclamation',
        str_contains($statusClass, 'yellow') => 'fa-spinner animate-pulse',
        str_contains($statusClass, 'green')  => 'fa-circle-check',
        default                              => 'fa-circle',
    };

    // flag dari controller
    $uraianMissing = (bool) ($perjalanan->uraian_missing ?? false);
    $geotagMissing = (bool) ($perjalanan->geotag_missing_today ?? false);

    // pesan jika semua sudah lengkap
    $positiveText = "Terima kasih atas kerja kerasnya, Anda telah melengkapi data perjalanan dinas ini.";

    $detailRoute = route('perjalanan.detail', $perjalanan->id);
@endphp

<div class="bg-white rounded-3xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl group border border-gray-100 relative">

    <!-- Garis status kiri -->
    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $statusClass }}"></div>

    <!-- Konten utama -->
    <div class="p-6 pl-8">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4">

            <!-- Kiri -->
            <div class="flex-1 space-y-3 min-w-0">
                <h3 class="text-blue-800 font-bold text-xl tracking-wide border-b-2 border-blue-200 pb-2 truncate">
                    {{ $perjalanan->nomor_surat }}
                </h3>

                <div class="space-y-2">
                    <p class="flex items-start gap-3 text-gray-700 text-base">
                        <i class="fa-solid fa-location-dot w-5 mt-0.5 text-gray-400"></i>
                        <span class="font-medium break-words">{{ $perjalanan->lokasi }}</span>
                    </p>
                    <p class="flex items-start gap-3 text-gray-600 text-sm">
                        <i class="fa-regular fa-calendar w-5 mt-0.5 text-gray-400"></i>
                        <span>{{ $perjalanan->tanggal }}</span>
                    </p>
                </div>
            </div>

            <!-- Kanan -->
            <div class="flex flex-col items-center gap-3 sm:min-w-[150px]">

                <span class="px-4 py-2 text-sm font-bold text-white rounded-full shadow-md {{ $statusClass }} flex items-center gap-2">
                    <i class="fa-solid {{ $badgeIcon }} text-xs"></i>
                    <span>{{ $statusText }}</span>
                </span>

                <a href="{{ $detailRoute }}" class="text-blue-600 hover:text-blue-800 hover:underline text-sm font-semibold flex items-center gap-2 px-2 py-1 rounded hover:bg-blue-50">
                    <span>Lihat Detail</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

        </div>
    </div>

    <!-- Bagian peringatan/info di bawah card -->
    @if($uraianMissing || $geotagMissing)
        <div class="bg-red-50 px-5 py-3 border-t border-red-200">
            <p class="text-red-700 font-semibold flex items-center gap-2 mb-1 text-sm">
                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                <span>Peringatan</span>
            </p>

            <ul class="text-red-700 text-xs list-disc ml-4 space-y-1">
                @if($uraianMissing)
                    <li>Harap isi uraian hasil perjalanan dinas Anda. Anda tidak bisa mengakhiri perjalanan dinas sebelum mengisi uraian.</li>
                @endif

                @if($geotagMissing)
                    <li>Geotagging hari ini belum dilakukan. Harap segera lakukan geotagging.</li>
                @endif
            </ul>
        </div>
    @else
        <div class="bg-green-50 px-5 py-3 border-t border-green-200">
            <p class="text-green-700 font-semibold flex items-center gap-2 mb-2 text-sm">
                <i class="fa-solid fa-circle-check text-xs"></i>
                <span>Info</span>
            </p>

            <ul class="text-green-700 text-xs list-disc ml-4 space-y-1">
                <li>{{ $positiveText }}</li>
            </ul>
        </div>
    @endif

</div>
