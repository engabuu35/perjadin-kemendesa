@extends('layouts.appPimpinan')

@section('title', 'Beranda Pimpinan')

@section('content')
<style>
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
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
</style>

<div class="min-h-screen p-6">
    <div class="mx-auto" style="max-width: 1400px;">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Monitoring Pegawai</h1>
            <p class="text-gray-600 mt-2">Dashboard monitoring perjalanan dinas pegawai</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column - Map, Bar Chart, Line Chart -->
            <div class="space-y-6">
                <!-- Map Card with Pegawai Counter -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">                 
                    <div class="p-8 flex items-center justify-center border border-gray-200" style="height: 292px;">
                        <div class="text-center">
                            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            <h2 class="text-2xl font-bold text-gray-400">PETA</h2>
                            <p class="text-gray-500 text-sm mt-2">Lokasi perjalanan dinas akan ditampilkan di sini</p>
                        </div>
                    </div>

                    <div class="bg-orange-500 text-white px-6 py-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold">Pegawai dalam Perjalanan Dinas Saat Ini</p>
                            <span class="bg-white text-orange-500 px-3 py-1 rounded-full text-lg font-bold">{{ $pegawaiOnProgress }}</span>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Bar Chart Card -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Grafik Perjalanan Dinas per Bulan</h3>
                        <div class="h-64">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="bg-green-600 text-white px-6 py-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold">Total Perjalanan Dinas Sebulan Terakhir</p>
                            <span class="bg-white text-green-600 px-3 py-1 rounded-full text-lg font-bold">{{ $totalSebulanTerakhir }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Line Chart Card -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Grafik Anggaran Perjalanan Dinas per Bulan</h3>
                        <div class="h-64">
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="bg-red-500 text-white px-6 py-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold">Total Anggaran Sebulan Terakhir</p>
                            <span class="bg-white text-red-500 px-3 py-1 rounded-full text-sm font-bold">Rp {{ number_format($anggaranSebulanTerakhir, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Employee Cards (Full Height) -->
            <div class="flex flex-col">
                <!-- Employee Cards Header -->
                <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Perjalanan Dinas Aktif</h2>
                    <p class="text-gray-600 text-sm">Daftar perjalanan dinas yang sedang berlangsung</p>
                </div>

                <!-- Employee Cards List with Scrollable Container -->
                <div class="bg-white rounded-lg shadow-md p-4 flex-1 overflow-hidden">
                    <div class="space-y-4 overflow-y-auto h-full custom-scrollbar pr-2">
                        @forelse($perjalanandinas as $pd)
                        <div class="bg-gray-50 rounded-lg hover:shadow-md transition-shadow duration-200 p-5 border-l-4 border-orange-400">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-blue-600 mb-1">{{ $pd->nomor_surat }}</h3>
                                    <p class="text-xs text-gray-500">Tanggal Surat: {{ \Carbon\Carbon::parse($pd->tanggal_surat)->format('d M Y') }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-2 ml-4">
                                    <span class="bg-orange-400 text-white text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1 whitespace-nowrap">
                                        <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                        On Progress
                                    </span>
                                    <a href="{{ route('pimpinan.detail', $pd->id) }}" 
                                       class="text-blue-500 text-xs font-medium hover:underline hover:text-blue-700 transition">
                                        Lihat Detail →
                                    </a>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <p class="text-blue-600 text-sm font-medium">{{ $pd->tujuan }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <p class="text-blue-600 text-sm">
                                            {{ \Carbon\Carbon::parse($pd->tgl_mulai)->format('d F Y') }} - 
                                            {{ \Carbon\Carbon::parse($pd->tgl_selesai)->format('d F Y') }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Durasi: {{ \Carbon\Carbon::parse($pd->tgl_mulai)->diffInDays(\Carbon\Carbon::parse($pd->tgl_selesai)) + 1 }} hari
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="bg-gray-50 rounded-lg p-8 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 font-medium">Tidak ada perjalanan dinas yang sedang berlangsung</p>
                            <p class="text-gray-400 text-sm mt-2">Data akan muncul ketika ada pegawai dalam perjalanan dinas</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Bar Chart - Jumlah Perjalanan Dinas per Bulan
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Oct', 'Nov', 'Des'],
            datasets: [{
                label: 'Jumlah Perjalanan Dinas',
                data: @json($barChartData),
                backgroundColor: '#10b981',
                borderRadius: 6,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Total: ' + context.parsed.y + ' perjalanan dinas';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value;
                        }
                    },
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
    // Line Chart - Total Anggaran per Bulan
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Oct', 'Nov', 'Des'],
            datasets: [{
                label: 'Total Anggaran (Rp)',
                data: @json($lineChartData),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                pointBackgroundColor: '#ef4444',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
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
                    labels: {
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Anggaran: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000) + 'jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000) + 'k';
                            }
                            return 'Rp ' + value;
                        }
                    },
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection