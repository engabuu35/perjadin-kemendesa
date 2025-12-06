@extends('layouts.appPimpinan')

@section('title', 'Detail Perjalanan Dinas')

@section('content')
<div class="ml-[80px] p-6 mt-[10px] max-w-6xl mx-auto">
    {{-- BACK LINK --}}
    <div class="mb-6">
        <a href="{{ route('pimpinan.monitoring') }}"
           class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Kembali ke Dashboard
        </a>
    </div>

    @php
        use Carbon\Carbon;

        $manualFinish  = $progress['manual_finish'] ?? false;
        $manualReason  = $progress['manual_finish_reason'] ?? null;
        $dalamRangka   = $progress['dalam_rangka'] ?? ($dalamRangka ?? null);

        $tglMulai   = $progress['mulai'] ? $progress['mulai']->translatedFormat('d M Y') : '-';
        $tglSelesai = $progress['selesai'] ? $progress['selesai']->translatedFormat('d M Y') : '-';

        // Badge status utama perjadin
        $statusLabel = $progress['status_perjadin'] ?? $progress['fase'] ?? '-';
        $statusClass = 'bg-gray-100 text-gray-700 border-gray-200';

        $lowerStatus = strtolower($statusLabel);

        if (strtolower($statusLabel) === 'diselesaikan manual' || str_contains(strtolower($statusLabel), 'diselesaikan manual')) {
            $statusClass = 'bg-amber-100 text-amber-800 border-amber-200';
        } elseif (str_contains(strtolower($statusLabel), 'berlangsung')) {
            $statusClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
        } elseif (str_contains(strtolower($statusLabel), 'selesai')) {
            $statusClass = 'bg-green-100 text-green-700 border-green-200';
        } elseif (str_contains(strtolower($statusLabel), 'pembuatan')) {
            $statusClass = 'bg-blue-100 text-blue-700 border-blue-200';
        } elseif (str_contains(strtolower($statusLabel), 'tindakan')) {
            $statusClass = 'bg-red-100 text-red-700 border-red-200';
        }

        // Badge status laporan keuangan (tetap)
        $statusLap = $keuangan['status_laporan'] ?? 'Belum Dibuat';
        $statusLapClass = 'bg-gray-100 text-gray-700 border-gray-200';

        if (str_contains(strtolower($statusLap), 'disetujui')
            || str_contains(strtolower($statusLap), 'valid')) {
            $statusLapClass = 'bg-green-100 text-green-700 border-green-200';
        } elseif (str_contains(strtolower($statusLap), 'revisi')
            || str_contains(strtolower($statusLap), 'perlu')) {
            $statusLapClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
        } elseif (str_contains(strtolower($statusLap), 'tolak')) {
            $statusLapClass = 'bg-red-100 text-red-700 border-red-200';
        }
    @endphp


    {{-- ================= HEADER PERJADIN ================= --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">
                    Surat Tugas
                </p>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-wide mb-2">
                    {{ $perjadin->nomor_surat ?? '-' }}
                </h1>
                <p class="text-gray-600 text-sm md:text-base">
                    <i class="fa-solid fa-location-dot mr-2 text-gray-400"></i>
                    {{ $perjadin->tujuan ?? '-' }}
                </p>
                <p class="text-gray-500 text-sm mt-1">
                    <i class="fa-regular fa-calendar mr-2 text-gray-400"></i>
                    {{ $tglMulai }} &mdash; {{ $tglSelesai }}
                </p>
            </div>

            <div class="flex flex-col items-start md:items-end gap-2">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full border text-xs font-semibold {{ $statusClass }}">
                    <span class="w-2 h-2 rounded-full bg-current mr-2 opacity-70"></span>
                    {{ $statusLabel }}
                </span>

                <span class="inline-flex items-center px-3 py-1 rounded-full border text-[11px] font-semibold {{ $statusLapClass }}">
                    <i class="fa-solid fa-file-invoice-dollar mr-2 text-xs"></i>
                    Laporan Keuangan: {{ $statusLap }}
                </span>

                <div class="text-right text-xs text-gray-500 mt-1">
                    <p>Dibuat oleh:
                        <span class="font-semibold text-gray-700">
                            {{ $pembuat->nama ?? '-' }}
                        </span>
                    </p>
                    @if($approver)
                        <p>Disetujui oleh:
                            <span class="font-semibold text-gray-700">
                                {{ $approver->nama }}
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- INFO: Diselesaikan Manual --}}
    @php
        $isManual = isset($perjadin->nama_status) && strtolower($perjadin->nama_status) === 'diselesaikan manual';
        $pegawaiBelum = $progress['pegawai_belum'] ?? max($progress['total_pegawai'] - $progress['pegawai_selesai'], 0);
    @endphp

    @if($isManual)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-amber-500 text-lg mt-0.5"></i>
                <div>
                    <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide">
                        Perjalanan Dinas Diselesaikan Manual oleh PIC
                    </p>

                    @php
                        $alasanManual = trim($perjadin->alasan_selesai_manual ?? '');
                    @endphp

                    @if($alasanManual !== '')
                        <p class="text-sm text-amber-800 mt-1">
                            Alasan PIC: {{ $alasanManual }}
                        </p>
                    @else
                        <p class="text-sm text-amber-800 mt-1">
                            Perjalanan dinas ini ditandai selesai secara manual oleh PIC sebelum sistem
                            mencatat seluruh tahapan selesai secara otomatis.
                        </p>
                    @endif

                    <p class="text-xs text-amber-700 mt-2">
                        Terdapat
                        <strong>{{ $pegawaiBelum }}</strong>
                        pegawai yang tercatat
                        <strong>belum menandai selesai / belum mengisi uraian</strong>.  
                        Detail status masing-masing pegawai dapat dilihat pada bagian
                        <strong>"Tim Perjalanan Dinas"</strong> di bawah.
                    </p>
                </div>
            </div>
        </div>
    @endif


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- ================= TIM & KONTAK ================= --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-users text-blue-500"></i>
                    Tim Perjalanan Dinas
                </h2>
                <span class="text-xs text-gray-500">
                    {{ $progress['pegawai_selesai'] }}/{{ $progress['total_pegawai'] }} pegawai menandai selesai
                </span>
            </div>

            @if($pegawai->isEmpty())
                <p class="text-sm text-gray-500 italic">
                    Belum ada pegawai yang terdaftar pada perjalanan dinas ini.
                </p>
            @else
                <div class="space-y-3">
                    @foreach($pegawai as $pg)
                        @php
                            $finished = (int) $pg->is_finished === 1;
                        @endphp
                        <div class="flex items-start justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                            <div class="space-y-0.5">
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $pg->nama }}
                                    <span class="text-xs font-normal text-gray-500">
                                        &mdash; {{ $pg->role_perjadin ?? 'Anggota' }}
                                    </span>
                                </p>
                                <p class="text-xs text-gray-500">
                                    NIP: {{ $pg->nip }}
                                </p>
                                @if(!empty($pg->no_telp))
                                    <p class="text-xs text-gray-500">
                                        <i class="fa-solid fa-phone mr-1 text-gray-400"></i>
                                        {{ $pg->no_telp }}
                                    </p>
                                @endif
                                @if(!empty($pg->email))
                                    <p class="text-xs text-gray-500">
                                        <i class="fa-solid fa-envelope mr-1 text-gray-400"></i>
                                        {{ $pg->email }}
                                    </p>
                                @endif
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold
                                {{ $finished ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-yellow-100 text-yellow-700 border border-yellow-200' }}">
                                <i class="fa-solid {{ $finished ? 'fa-check-circle' : 'fa-clock' }} mr-1.5"></i>
                                {{ $finished ? 'Selesai' : 'Belum Selesai' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ================= RINGKASAN PROGRESS ================= --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-indigo-500"></i>
                Ringkasan Progres
            </h2>

            <div class="space-y-3 text-sm">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-flag text-blue-500 mt-0.5"></i>
                    <div>
                        <p class="font-semibold text-gray-800">Fase Perjalanan</p>
                        <p class="text-gray-600 text-xs">{{ $progress['fase'] }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-calendar-days text-emerald-500 mt-0.5"></i>
                    <div>
                        <p class="font-semibold text-gray-800">Periode</p>
                        <p class="text-gray-600 text-xs">{{ $tglMulai }} &mdash; {{ $tglSelesai }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-user-check text-sky-500 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">Penyelesaian Tugas Pegawai</p>
                        <p class="text-gray-600 text-xs mb-1">
                            {{ $progress['pegawai_selesai'] }} dari {{ $progress['total_pegawai'] }} pegawai menandai selesai.
                        </p>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full bg-emerald-500"
                                 style="width: {{ $progress['persen_pegawai_selesai'] }}%;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-200 my-2"></div>

                <ul class="space-y-1 text-xs text-gray-700">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid {{ $progress['ada_uraian_perjadin'] ? 'fa-check-circle text-green-500' : 'fa-circle-xmark text-gray-400' }}"></i>
                        Uraian pelaksanaan PIC
                        <span class="ml-auto font-semibold">
                            {{ $progress['ada_uraian_perjadin'] ? 'Sudah diisi' : 'Belum ada' }}
                        </span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid {{ $progress['jumlah_uraian_individu'] > 0 ? 'fa-check-circle text-green-500' : 'fa-circle-xmark text-gray-400' }}"></i>
                        Uraian individu pegawai
                        <span class="ml-auto font-semibold">
                            {{ $progress['jumlah_uraian_individu'] }} laporan
                        </span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid {{ $keuangan['ada_laporan'] ? 'fa-check-circle text-green-500' : 'fa-circle-xmark text-gray-400' }}"></i>
                        Laporan keuangan
                        <span class="ml-auto font-semibold">
                            {{ $keuangan['status_laporan'] }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ================= URAIAN PELAKSANAAN ================= --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-file-lines text-amber-500"></i>
            Uraian Pelaksanaan
        </h2>

            {{-- Uraian PIC / perjadin --}}
            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">
                    Uraian Utama (PIC / Surat Tugas)
                </p>

                @php
                    $hasDalamRangka     = isset($dalamRangka) && trim($dalamRangka) !== '';
                    $hasUraianPerjadin  = isset($uraianPerjadin) && trim($uraianPerjadin) !== '';
                @endphp

                @if($hasDalamRangka || $hasUraianPerjadin)
                    <div class="text-sm text-gray-700 leading-relaxed bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 space-y-3">
                        @if($hasDalamRangka)
                            <div>
                                <p class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">
                                    Dalam Rangka
                                </p>
                                <p>{!! nl2br(e($dalamRangka)) !!}</p>
                            </div>
                        @endif

                        @if($hasUraianPerjadin)
                            <div class="{{ $hasDalamRangka ? 'border-t border-dashed border-gray-200 pt-3 mt-1' : '' }}">
                                <p class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">
                                    Uraian Pelaksanaan PIC
                                </p>
                                <p>{!! nl2br(e($uraianPerjadin)) !!}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">
                        Belum ada uraian pelaksanaan yang diisi oleh PIC.
                    </p>
                @endif

                @if($manualFinish)
                    <div class="mt-3 border-l-4 border-amber-500 bg-amber-50 px-4 py-2 rounded-md">
                        <p class="text-xs font-semibold text-amber-800 uppercase tracking-wide mb-1">
                            Diselesaikan Manual oleh PIC
                        </p>
                        @if($manualReason)
                            <p class="text-xs text-amber-900 leading-relaxed">
                                Alasan: {!! nl2br(e($manualReason)) !!}
                            </p>
                        @else
                            <p class="text-xs text-amber-900 leading-relaxed">
                                Perjalanan dinas ini ditandai selesai secara manual oleh PIC.
                            </p>
                        @endif
                    </div>
                @endif
            </div>


        {{-- ================= REKAP KEUANGAN & GEOTAG ================= --}}

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
            {{-- REKAP KEUANGAN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-coins text-green-500"></i>
                    Rekapitulasi Keuangan
                </h2>

                @if(!$keuangan['ada_laporan'])
                    <p class="text-sm text-gray-400 italic">
                        Belum ada laporan keuangan untuk perjalanan dinas ini.
                    </p>
                @else
                    <div class="mb-4">
                        <p class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">
                            Total Biaya Rampung
                        </p>
                        <p class="text-2xl font-bold text-gray-800">
                            Rp {{ number_format($keuangan['total_biaya_rampung'], 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Status: {{ $keuangan['status_laporan'] }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- RINGKASAN GEOTAGGING --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-rose-500"></i>
                    Ringkasan Geotagging
                </h2>

                @if($geotagSummary['total_hari'] === 0)
                    <p class="text-sm text-gray-400 italic">
                        Periode perjalanan belum lengkap, ringkasan geotagging tidak tersedia.
                    </p>
                @else
                    <div class="grid grid-cols-3 gap-3 mb-2 text-center">
                        <div class="bg-gray-50 rounded-xl px-3 py-2">
                            <p class="text-xs text-gray-500">Total Hari</p>
                            <p class="text-lg font-bold text-gray-800">
                                {{ $geotagSummary['total_hari'] }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-xl px-3 py-2">
                            <p class="text-xs text-gray-500">Hari Geotag Lengkap</p>
                            <p class="text-lg font-bold text-emerald-600">
                                {{ $geotagSummary['hari_terisi'] }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-xl px-3 py-2">
                            <p class="text-xs text-gray-500">Total Titik</p>
                            <p class="text-lg font-bold text-blue-600">
                                {{ $geotagSummary['total_record'] }}
                            </p>
                        </div>
                    </div>

                    <p class="text-[11px] text-gray-400">
                        Ringkasan di atas dihitung dari seluruh data geotagging untuk perjalanan dinas ini.
                    </p>
                @endif
            </div>
        </div>

        {{-- ============================ PETA GEOTAGGING (CARD TERPISAH) =============================== --}}
        @if($geotagSummary['total_record'] > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-10">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-blue-500"></i>
                        Peta Geotagging Harian
                    </h2>

                    <div class="flex items-center gap-2">
                        <label class="text-xs text-gray-500">Pilih Tanggal:</label>
                        <select id="geotagFilterDate"
                                class="border border-gray-300 rounded-lg text-xs px-2 py-1">
                        </select>
                    </div>
                </div>

                <div id="mapPimpinan"
                    class="rounded-xl border border-gray-200 w-full h-80">
                </div>

                <p id="mapInfo" class="mt-2 text-xs text-gray-500"></p>
            </div>
        @endif
    </div>

    {{-- ================================== SCRIPT PETA ================================== --}}
    @if($geotagSummary['total_record'] > 0)
        <link rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <style>
            /* Fix z-index untuk semua elemen leaflet agar tidak menimpa navbar */
            #mapPimpinan .leaflet-pane,
            #mapPimpinan .leaflet-tile-pane,
            #mapPimpinan .leaflet-overlay-pane,
            #mapPimpinan .leaflet-shadow-pane,
            #mapPimpinan .leaflet-marker-pane,
            #mapPimpinan .leaflet-tooltip-pane,
            #mapPimpinan .leaflet-popup-pane {
                z-index: 1 !important;
            }
            
            /* Control zoom dan attribution juga harus di bawah navbar */
            #mapPimpinan .leaflet-control-container,
            #mapPimpinan .leaflet-top,
            #mapPimpinan .leaflet-bottom,
            #mapPimpinan .leaflet-control-zoom,
            #mapPimpinan .leaflet-control-attribution {
                z-index: 50 !important;
            }
        </style>

        <script>
        document.addEventListener("DOMContentLoaded", function () {
            const geoData = @json($geotagMapData ?? []);
            if (!geoData.length) return;

            const mapEl    = document.getElementById('mapPimpinan');
            const filterEl = document.getElementById('geotagFilterDate');
            const infoEl   = document.getElementById('mapInfo');

            // 1. Ambil daftar tanggal unik dari data
            const dates = [...new Set(geoData.map(g => g.tanggal))].sort();
            filterEl.innerHTML = '';
            dates.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d;
                opt.textContent = d;
                filterEl.appendChild(opt);
            });

            // 2. Inisialisasi peta
            const map = L.map('mapPimpinan').setView([-2.548926, 118.0148634], 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            const markersLayer = L.layerGroup().addTo(map);

            // 3. Render marker untuk tanggal tertentu
            function renderMap(date) {
                markersLayer.clearLayers();
                const subset = geoData.filter(g => g.tanggal === date);

                if (!subset.length) {
                    infoEl.textContent = 'Belum ada geotagging pada tanggal ' + date + '.';
                    return;
                }

                const bounds = [];
                subset.forEach(p => {
                    const m = L.marker([p.lat, p.lng]).addTo(markersLayer);
                    m.bindPopup(`
                        <div class="text-xs">
                            <strong>${p.nama}</strong><br>
                            NIP: ${p.nip}<br>
                            Waktu: ${p.waktu}<br>
                            Tipe: ${p.tipe ?? '-'}
                        </div>
                    `);
                    bounds.push([p.lat, p.lng]);
                });

                if (bounds.length) {
                    map.fitBounds(bounds, { padding: [24, 24] });
                }

                infoEl.textContent = subset.length + ' titik geotagging pada tanggal ' + date + '.';
            }

            // 4. Render awal & event perubahan filter
            if (dates.length) {
                filterEl.value = dates[0];      // atau dates[dates.length-1] kalau mau hari terakhir
                renderMap(dates[0]);
            }

            filterEl.addEventListener('change', function () {
                renderMap(this.value);
            });
        });
        </script>
@endif
@endsection