@php
    $badge_class = $bg_catatan = $text_catatan = '';

    // Warna komponen
    $badge_class = match($perjalanan->status_color) {
        'red' => 'bg-red-500',
        'yellow' => 'bg-yellow-500',
        'green' => 'bg-green-600',
        'blue' => 'bg-blue-500',
        default => 'bg-gray-500'
    };

    $bg_catatan = match($perjalanan->status_color) {
        'red' => 'bg-red-50',
        'yellow' => 'bg-yellow-50',
        'green' => 'bg-green-50',
        'blue' => 'bg-blue-50',
        default => 'bg-gray-50'
    };

    $text_catatan = match($perjalanan->status_color) {
        'red' => 'text-red-700',
        'yellow' => 'text-yellow-700',
        'green' => 'text-green-700',
        'blue' => 'text-blue-700',
        default => 'text-gray-700'
    };
@endphp

<div class="bg-white rounded-3xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl group border border-gray-100">

    <!-- Atas -->
    <div class="border-l-[6px] border-blue-500 p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4">

            <!-- Kiri -->
            <div class="flex-1 space-y-3 min-w-0">
                <h3 class="text-blue-800 font-bold text-xl tracking-wide group-hover:translate-x-1 transition-transform duration-300 border-b-2 border-blue-200 pb-2 truncate">
                    {{ $perjalanan->nomor_surat }}
                </h3>

                <div class="space-y-2">
                    <p class="flex items-start gap-3 text-gray-700 text-base group-hover:translate-x-1 transition-transform duration-300 delay-75">
                        <i class="fa-solid fa-location-dot w-5 mt-0.5 flex-shrink-0 text-gray-400"></i>
                        <span class="font-medium break-words">{{ $perjalanan->lokasi }}</span>
                    </p>
                    <p class="flex items-start gap-3 text-gray-600 text-sm group-hover:translate-x-1 transition-transform duration-300 delay-100">
                        <i class="fa-regular fa-calendar w-5 mt-0.5 flex-shrink-0 text-gray-400"></i>
                        <span class="break-words">{{ $perjalanan->tanggal }}</span>
                    </p>
                </div>
            </div>

            <!-- Kanan -->
            <div class="flex flex-col items-center sm:items-center gap-3 sm:min-w-[150px]">

                <!-- Badge (gunakan komponen) -->
                <x-status-badge 
                    :statusColor="$perjalanan->status_color"
                    :status="$perjalanan->status" 
                />

                <!-- Link -->
                <a href="{{ route('perjalanan.detail', $perjalanan->id) }}" 
                    class="text-blue-600 hover:text-blue-800 hover:underline text-sm font-semibold transition-all duration-200 flex items-center gap-2 group/link px-2 py-1 rounded hover:bg-blue-50">
                    <span>Lihat Detail</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform duration-200"></i>
                </a>
            </div>

        </div>
    </div>

    <!-- Banner bawah -->
    <x-catatan-banner 
        :color="$perjalanan->status_color"
        :text="$perjalanan->catatan"
    />

</div>
