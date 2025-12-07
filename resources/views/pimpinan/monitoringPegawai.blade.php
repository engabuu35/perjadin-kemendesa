@extends('layouts.appPimpinan')

@section('title', 'Monitoring Perjalanan Dinas')

@section('content')
<style>
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #fb923c;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #f97316;
    }
    
    /* Leaflet Map Z-Index Fix */
    .leaflet-pane,
    .leaflet-tile,
    .leaflet-layer,
    .leaflet-control,
    .leaflet-top,
    .leaflet-bottom {
        z-index: 1 !important;
    }
    
    .leaflet-popup {
        z-index: 10 !important;
    }
    
    /* Modal Fullscreen Map */
    .map-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        backdrop-filter: blur(5px);
        animation: fadeIn 0.3s ease;
    }
    
    .map-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .map-modal-content {
        width: 95%;
        height: 90%;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        position: relative;
        animation: slideUp 0.3s ease;
    }
    
    .map-close-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 10000;
        background: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .map-close-btn:hover {
        background: #ef4444;
        color: white;
        transform: rotate(90deg);
    }
    

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes slideUp {
        from {
            transform: translateY(50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>
<main class="transition-all duration-300 ml-0 sm:ml-[60px]">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
   
        <div class="mx-auto max-w-[1400px]">

            {{-- Header --}}
            <div class="flex flex-col gap-0.5 mb-1 mb-7">
                <x-page-title 
                title="Monitoring Pegawai"
                subtitle="Monitoring pegawai yang sedang melakukan perjalanan dinas." />
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-[0.9fr_1.1fr] gap-4 items-start">

                {{-- LEFT COLUMN: Map + Bar + Line --}}
                <div class="space-y-4" id="leftColumn">
                    {{-- Map --}}
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-4">
                            <h3 class="text-base font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-map-location-dot text-blue-500"></i>
                                Peta Perjalanan Dinas Aktif
                            </h3>

                            @if($geotagMapData->isEmpty())
                                <div class="flex items-center justify-center h-[250px] bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 013.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                                            </path>
                                        </svg>
                                        <p class="text-sm text-gray-400">
                                            Belum ada titik geotagging dari perjalanan dinas yang sedang berlangsung.
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div id="mapMonitoring" class="w-full h-[250px] rounded-lg border border-gray-200 overflow-hidden cursor-pointer transition-all hover:shadow-lg hover:border-blue-400" onclick="openMapModal()" title="Klik untuk memperbesar peta"></div>
                            @endif
                        </div>

                        <div class="bg-orange-500 text-white px-4 py-2">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold">
                                    Pegawai dalam Perjalanan Dinas Saat Ini :
                                    <span class="bg-white text-orange-500 px-2 py-0.5 rounded-full text-sm font-bold ml-1.5">
                                        {{ $pegawaiOnProgress }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Bar chart --}}
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-4">
                            <h3 class="text-base font-semibold text-gray-700 mb-2">
                                Grafik Perjalanan Dinas per Bulan
                            </h3>
                            <div class="h-44">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>

                        <div class="bg-green-600 text-white px-4 py-2">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold">
                                    Total Perjalanan Dinas Sebulan Terakhir :
                                    <span class="bg-white text-green-600 px-2 py-0.5 rounded-full text-sm font-bold ml-1.5">
                                        {{ $totalSebulanTerakhir }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Line chart --}}
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-4">
                            <h3 class="text-base font-semibold text-gray-700 mb-2">
                                Grafik Anggaran Perjalanan Dinas per Bulan
                            </h3>
                            <div class="h-44">
                                <canvas id="lineChart"></canvas>
                            </div>
                        </div>

                        <div class="bg-red-500 text-white px-4 py-2">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold flex items-center flex-wrap">
                                    <span>Total Anggaran Sebulan Terakhir :</span>
                                    <span class="bg-white text-red-500 px-2.5 py-0.5 rounded-full text-sm font-bold ml-1.5 whitespace-nowrap">
                                        Rp {{ number_format($anggaranSebulanTerakhir, 0, ',', '.') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: daftar perjalanan aktif --}}
                <div class="flex flex-col h-full" id="rightColumnWrapper">
                    {{-- header box --}}
                    <div class="bg-white rounded-xl shadow-md p-2.5 mb-2.5 flex-shrink-0">
                        <h2 class="text-base font-bold text-gray-800">Perjalanan Dinas Aktif</h2>
                        <p class="text-gray-600 text-[10px] mb-2">
                            Daftar perjalanan dinas yang sedang berlangsung
                        </p>
                        
                        {{-- Search & Filter --}}
                        <div class="flex gap-2 mt-2">
                            {{-- Search Input --}}
                            <div class="flex-1 relative">
                                <input 
                                    type="text" 
                                    id="searchInput"
                                    placeholder="Cari nomor surat atau tujuan..."
                                    class="w-full px-3 py-1.5 pl-8 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                <i class="fa-solid fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                            </div>
                            
                            {{-- Filter Bulan --}}
                            <div class="relative">
                                <select 
                                    id="filterBulan"
                                    class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent appearance-none pr-8 bg-white cursor-pointer">
                                    <option value="all">Semua Bulan</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[8px] pointer-events-none"></i>
                            </div>
                        </div>
                        
                        {{-- Result Count --}}
                        <div class="mt-2 text-[10px] text-gray-500">
                            Menampilkan <span id="resultCount" class="font-semibold text-blue-600">{{ count($perjalanandinas) }}</span> perjalanan dinas
                        </div>
                    </div>

                    {{-- scrollable cards container --}}
                    <div
                        id="rightColumn"
                        class="space-y-2.5 custom-scrollbar overflow-y-auto bg-gray-50 rounded-lg p-2.5 flex-1">

                        @forelse($perjalanandinas as $perjadin)
                            @php
                                $tglMulai   = \Carbon\Carbon::parse($perjadin->tgl_mulai)->format('d M Y');
                                $tglSelesai = \Carbon\Carbon::parse($perjadin->tgl_selesai)->format('d M Y');
                                $tanggal    = $tglMulai . ' - ' . $tglSelesai;
                                $status     = 'Sedang Berlangsung';
                                $badge_class = 'bg-yellow-500';
                                $status_value = 'onprogress';
                            @endphp

                            <div class="perjadin-card bg-white rounded-2xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl group border border-gray-100"
                                data-nomor="{{ strtolower($perjadin->nomor_surat ?? 'Nomor Surat Tidak Tersedia') }}"
                                data-tujuan="{{ strtolower($perjadin->tujuan ?? 'Tidak ada tujuan') }}"
                                data-bulan="{{ \Carbon\Carbon::parse($perjadin->tgl_mulai)->format('n') }}"
                                data-status="{{ $status_value }}">
                                <div class="border-l-[5px] border-blue-500 p-3">
                                    <div class="flex flex-col sm:flex-row justify-between items-start gap-2.5">
                                        <div class="flex-1 space-y-1.5 min-w-0">
                                            <h3 class="text-blue-800 font-bold text-sm tracking-wide group-hover:translate-x-1 transition-transform duration-300 border-b border-blue-200 pb-1 truncate">
                                                {{ $perjadin->nomor_surat ?? 'Nomor Surat Tidak Tersedia' }}
                                            </h3>

                                            <div class="space-y-1">
                                                <p class="flex items-start gap-1.5 text-gray-700 text-xs group-hover:translate-x-1 transition-transform duration-300 delay-75">
                                                    <i class="fa-solid fa-location-dot w-3.5 mt-0.5 flex-shrink-0 text-gray-400 text-[10px]"></i>
                                                    <span class="font-medium break-words">
                                                        {{ $perjadin->tujuan ?? 'Tidak ada tujuan' }}
                                                    </span>
                                                </p>
                                                <p class="flex items-start gap-1.5 text-gray-600 text-[10px] group-hover:translate-x-1 transition-transform duration-300 delay-100">
                                                    <i class="fa-regular fa-calendar w-3.5 mt-0.5 flex-shrink-0 text-gray-400 text-[10px]"></i>
                                                    <span class="break-words">{{ $tanggal }}</span>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex flex-col items-center gap-1.5 sm:min-w-[100px]">
                                            <span class="px-2.5 py-1 text-[10px] font-bold text-white rounded-full shadow-md {{ $badge_class }} flex items-center gap-1 hover:brightness-110 hover:scale-105 transition-all duration-200 whitespace-nowrap">
                                                <span class="w-1 h-1 bg-white rounded-full"></span>
                                                {{ $status }}
                                            </span>

                                            <a href="{{ route('pimpinan.detail', $perjadin->id) }}"
                                            class="text-blue-600 hover:text-blue-800 hover:underline text-[10px] font-semibold transition-all duration-200 flex items-center gap-1 group/link px-1 py-0.5 rounded hover:bg-blue-50">
                                                <span>Lihat Detail</span>
                                                <i class="fa-solid fa-arrow-right text-[8px] group-hover/link:translate-x-1 transition-transform duration-200"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div id="emptyState" class="bg-white rounded-lg shadow-md p-6 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                <h3 class="text-base font-semibold text-gray-600 mb-1.5">
                                    Tidak Ada Perjalanan Dinas Aktif
                                </h3>
                                <p class="text-gray-500 text-xs">
                                    Saat ini tidak ada pegawai yang sedang melakukan perjalanan dinas
                                </p>
                            </div>
                        @endforelse
                        
                        {{-- No Results Message --}}
                        <div id="noResults" class="hidden bg-white rounded-lg shadow-md p-6 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                                </path>
                            </svg>
                            <h3 class="text-base font-semibold text-gray-600 mb-1.5">
                                Tidak Ada Hasil
                            </h3>
                            <p class="text-gray-500 text-xs">
                                Tidak ditemukan perjalanan dinas yang sesuai dengan pencarian atau filter Anda
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Fullscreen Map --}}
    <div id="mapModal" class="map-modal">
        <div class="map-modal-content">
            <button class="map-close-btn" onclick="closeMapModal()">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
            <div id="mapMonitoringFull" class="w-full h-full"></div>
        </div>
    </div>
</div>
{{-- Leaflet CSS & JS (hanya dimuat jika ada data) --}}
@if(!$geotagMapData->isEmpty())
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endif

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==================== SEARCH & FILTER FUNCTIONALITY ====================
    const searchInput = document.getElementById('searchInput');
    const filterBulan = document.getElementById('filterBulan');
    const resultCount = document.getElementById('resultCount');
    const perjadinCards = document.querySelectorAll('.perjadin-card');
    const noResults = document.getElementById('noResults');
    
    function filterCards() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const bulanFilter = filterBulan.value;
        let visibleCount = 0;
        
        perjadinCards.forEach(card => {
            const nomor = card.getAttribute('data-nomor').toLowerCase();
            const tujuan = card.getAttribute('data-tujuan').toLowerCase();
            const bulan = card.getAttribute('data-bulan');
            
            // Check search match
            const matchSearch = searchTerm === '' || 
                                nomor.includes(searchTerm) || 
                                tujuan.includes(searchTerm);
            
            // Check bulan filter
            const matchBulan = bulanFilter === 'all' || bulan === bulanFilter;
            
            // Show/hide card
            if (matchSearch && matchBulan) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update result count
        resultCount.textContent = visibleCount;
        
        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }
    
    // Event listeners
    searchInput.addEventListener('input', filterCards);
    filterBulan.addEventListener('change', filterCards);
    
    // ==================== SYNC COLUMN HEIGHTS ====================
    function syncColumnHeights() {
        const leftColumn = document.getElementById('leftColumn');
        const rightWrapper = document.getElementById('rightColumnWrapper');
        const rightColumn = document.getElementById('rightColumn');
        
        if (leftColumn && rightColumn && rightWrapper) {
            // Reset inline styles
            rightWrapper.style.height = '';
            rightColumn.style.maxHeight = '';
            
            // Force layout calculation
            void leftColumn.offsetHeight;
            
            // Get actual rendered height of left column
            const leftHeight = leftColumn.getBoundingClientRect().height;
            
            // Set right wrapper to exact same height
            rightWrapper.style.height = leftHeight + 'px';
            
            // Calculate scrollable area height
            const headerBox = rightWrapper.querySelector('.bg-white.rounded-xl');
            const headerHeight = headerBox ? headerBox.getBoundingClientRect().height : 0;
            const gap = 10; // mb-2.5 = 10px
            
            // Set max-height for scrollable area
            const scrollableHeight = leftHeight - headerHeight - gap;
            rightColumn.style.maxHeight = scrollableHeight + 'px';
        }
    }
    
    // Run on page load
    syncColumnHeights();
    
    // Run on window resize with debounce
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(syncColumnHeights, 100);
    });
    
    // Run after charts are fully loaded
    setTimeout(syncColumnHeights, 200);

        // ==================== CHART DATA ====================
        const barPerjadinTotal  = @json($barPerjadinTotal);
        const barPegawaiSelesai = @json($barPegawaiSelesai);
        const barPegawaiBelum   = @json($barPegawaiBelum);
        const lineData          = @json($lineChartData);

        console.log('Bar Perjadin:', barPerjadinTotal);
        console.log('Bar Pegawai Selesai:', barPegawaiSelesai);
        console.log('Bar Pegawai Belum:', barPegawaiBelum);
        console.log('Line Chart Data:', lineData);

        // ==================== BAR CHART (3 BAR PER BULAN) ====================
        const barCanvas = document.getElementById('barChart');
        if (barCanvas) {
            const barCtx = barCanvas.getContext('2d');
            const barChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                    datasets: [
                        {
                            // Perjalanan dinas yang selesai bulan itu
                            label: 'Perjalanan Dinas Selesai',
                            data: barPerjadinTotal,
                            backgroundColor: '#22c55e', // hijau
                            borderRadius: 6,
                            borderWidth: 0
                        },
                        {
                            // Tugas pegawai yang sudah menyelesaikan laporan (is_finished = 1)
                            label: 'Pegawai Selesai Laporan',
                            data: barPegawaiSelesai,
                            backgroundColor: '#3b82f6', // biru
                            borderRadius: 6,
                            borderWidth: 0
                        },
                        {
                            // Tugas pegawai yang belum menyelesaikan laporan
                            label: 'Pegawai Belum Selesai',
                            data: barPegawaiBelum,
                            backgroundColor: '#ef4444', // merah
                            borderRadius: 6,
                            borderWidth: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { font: { size: 10 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    const label = ctx.dataset.label || '';
                                    return label + ': ' + ctx.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 10 },
                                callback: value => Number.isInteger(value) ? value : null
                            },
                            grid: { color: '#f3f4f6' }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    },
                    // Biar bar sedikit "ngumpul" dan kelihatan overlap/rapat
                    datasets: {
                        bar: {
                            categoryPercentage: 0.7,
                            barPercentage: 0.8
                        }
                    },
                    animation: {
                        onComplete: syncColumnHeights
                    }
                }
            });
        }


    // ==================== LINE CHART ====================
    const lineCanvas = document.getElementById('lineChart');
    if (lineCanvas) {
        const lineCtx = lineCanvas.getContext('2d');
        const lineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                datasets: [{
                    label: 'Total Anggaran (Rp)',
                    data: lineData,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,0.1)',
                    tension: 0.4,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { font: { size: 10 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => 'Anggaran: Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 10 },
                            callback: value => {
                                if (value >= 1_000_000) return 'Rp ' + (value / 1_000_000).toFixed(1) + 'jt';
                                if (value >= 1_000)    return 'Rp ' + (value / 1_000).toFixed(0) + 'k';
                                return 'Rp ' + value;
                            }
                        },
                        grid: { color: '#f3f4f6' }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                },
                animation: {
                    onComplete: syncColumnHeights
                }
            }
        });
    }

    // ==================== LEAFLET MAP INITIALIZATION ====================
    @if(!$geotagMapData->isEmpty())
    let mapInstance = null;
    let mapFullInstance = null;
    const geoData = @json($geotagMapData);
    
    // Fungsi untuk membuat map
    function createMap(containerId, isFullscreen = false) {
        const mapEl = document.getElementById(containerId);
        if (!mapEl || typeof L === 'undefined') return null;

        // Inisialisasi peta
        const map = L.map(containerId).setView([-2.548926, 118.0148634], 5);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Paksa map untuk resize
        setTimeout(function() {
            map.invalidateSize();
        }, 100);

        const layer  = L.layerGroup().addTo(map);
        const bounds = [];

        // Palet warna untuk perjadin
        const palette = [
            '#2563eb', '#16a34a', '#f97316', '#e11d48', 
            '#a855f7', '#0ea5e9', '#facc15'
        ];

        // Map: id_perjadin -> warna
        const colorByPerjadin = {};
        let colorIndex = 0;

        geoData.forEach(point => {
            if (!colorByPerjadin[point.id_perjadin]) {
                colorByPerjadin[point.id_perjadin] = palette[colorIndex % palette.length];
                colorIndex++;
            }
        });

        // Tambah marker (tiap titik mewakili beberapa pegawai jika perlu)
        geoData.forEach(point => {
            const color = colorByPerjadin[point.id_perjadin];

            const marker = L.circleMarker([point.lat, point.lng], {
                radius: isFullscreen ? 10 : 7,
                weight: 2,
                color: color,
                fillColor: color,
                fillOpacity: 0.85
            }).addTo(layer);

            // Susun daftar pegawai di titik ini
            const pegawaiList = (point.pegawai || [])
                .map(p => `• ${p.nama} (${p.nip})`)
                .join('<br>');

            marker.bindPopup(`
                <div class="text-xs">
                    <strong>${point.nomor}</strong><br>
                    Tujuan: ${point.tujuan}<br>
                    <hr class="my-1">
                    <strong>Pegawai di titik ini (${point.jumlah}):</strong><br>
                    ${pegawaiList}<br>
                    <hr class="my-1">
                    Waktu terakhir: ${point.waktu}<br>
                    Tipe: ${point.tipe ?? '-'}
                </div>
            `);

            bounds.push([point.lat, point.lng]);
        });


        // Auto zoom ke semua titik
        if (bounds.length === 1) {
            map.setView(bounds[0], 13);
        } else if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }

        return map;
    }
    
    // Tunggu sampai container siap
    setTimeout(function() {
        mapInstance = createMap('mapMonitoring', false);
        if (!mapInstance) return;

        // Sync heights setelah map dimuat
        setTimeout(syncColumnHeights, 300);
    }, 250);
    
    // Fungsi untuk membuka modal fullscreen
    window.openMapModal = function() {
        const modal = document.getElementById('mapModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Buat map fullscreen jika belum ada
        setTimeout(function() {
            if (!mapFullInstance) {
                mapFullInstance = createMap('mapMonitoringFull', true);
            } else {
                mapFullInstance.invalidateSize();
            }
        }, 100);
    };
    
    // Fungsi untuk menutup modal
    window.closeMapModal = function() {
        const modal = document.getElementById('mapModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
    };
    
    // Close modal dengan ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMapModal();
        }
    });
    
    // Close modal ketika klik di luar content
    document.getElementById('mapModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeMapModal();
        }
    });
    @endif
});
</script>
</main>
@endsection