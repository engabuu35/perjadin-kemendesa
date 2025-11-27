@props(['riwayat'])

<div class="rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 group" style="background-color: #BCBCBF;">
    
    <div class="p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
            
            <!-- Info Kiri -->
            <div class="flex-1 space-y-2.5">
                <h3 class="text-white font-bold text-xl group-hover:translate-x-1 transition-transform duration-300 border-b-2 border-white pb-2">
                    {{ $riwayat->nomor_surat }}
                </h3>
                
                <div class="space-y-1.5">
                    <p class="flex items-center gap-3 text-white text-base group-hover:translate-x-1 transition-transform duration-300 delay-75">
                        <i class="fa-solid fa-map-marker-alt w-5"></i>
                        <span class="font-medium break-words">{{ $riwayat->tujuan }}</span>
                    </p>
                    
                    <p class="flex items-center gap-3 text-white text-base group-hover:translate-x-1 transition-transform duration-300 delay-100">
                        <i class="fa-solid fa-calendar-days w-5"></i>
                        <span>
                            {{ \Carbon\Carbon::parse($riwayat->tgl_mulai)->format('d M Y') }} 
                            - 
                            {{ \Carbon\Carbon::parse($riwayat->tgl_selesai)->format('d M Y') }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Info Kanan -->
            <div class="flex flex-col items-start sm:items-end gap-3 sm:min-w-[140px]">
                
                @php
                    $statusClass = 'bg-gray-500';
                    $statusText = 'Menunggu';
                    
                    if($riwayat->id_status == 4) {
                        $statusClass = 'bg-green-500';
                        $statusText = 'Selesai';
                    }
                @endphp
                                
                <span class="px-5 py-2 text-sm font-semibold text-white rounded-full {{ $statusClass }} shadow-sm flex items-center gap-2 hover:brightness-110 transition-all duration-200">
                    <i class="fa-solid fa-circle text-xs animate-pulse"></i>
                    {{ $statusText }}
                </span>
                                
                <a href="{{ route('perjalanan.detail', $riwayat->id) }}" 
                   class="text-gray-200 hover:text-white hover:underline text-sm font-medium transition-all duration-200 flex items-center gap-1.5 group/link">
                    <span>Lihat Detail</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform duration-200"></i>
                </a>
            </div>

        </div>
    </div>
</div>
