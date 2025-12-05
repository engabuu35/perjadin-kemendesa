@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

@php
    // Read-only kalau:
    // 1) tugas pegawai sudah ditandai selesai, atau
    // 2) perjadin ini sudah berstatus "Selesai" (riwayat)
    $isRiwayatPerjadin = $isRiwayatPerjadin ?? false;
    $isReadOnly = $isMyTaskFinished || $isRiwayatPerjadin;
    $remainingCooldown = $remainingCooldown ?? 0;
@endphp

<main class="item-center max-w-6xl min-h-screen mx-auto px-5 py-8">
    
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <x-page-title
                title="Pencatatan Perjalanan Dinas" 
                subtitle="Halaman untuk mencatat perjalanan dinas Anda" />
                <div class=" -mb-2">
                <!-- Surat Tugas -->
                <p class="bg-white font-semibold rounded-2xl px-3 py-1 text-sm text-gray-500 inline-block">
                    Surat Tugas: 
                    <span class="text-blue-600">{{ $perjalanan->nomor_surat }}</span>
                </p>

                <!-- Badge Status -->
                @if($isMyTaskFinished)
                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold border border-green-200">
                        Tugas Anda Sudah Selesai
                    </span>
                @elseif($isRiwayatPerjadin)
                    <span class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-bold border border-gray-200">
                        Perjalanan Dinas Telah Selesai (Riwayat)
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold border {{ $statusBadgeClass ?? '' }}">
                        {{ $statusPegawai ?? '—' }}
                    </span>
                @endif
            </div>
        </div>
        
        <!-- DIUBAH SUPAYA BALIK KE BERANDA SELALU -->
        <!-- <x-back-button /> -->
        <div class="flex justify-end mb-8">
            <a href="{{ url('/beranda') }}" 
            class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col gap-8">
        
        <!-- 1. GEOTAGGING -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 {{ $isReadOnly ? 'opacity-75 pointer-events-none grayscale-[20%]' : '' }}">
            <h2 class="text-xl font-bold text-gray-800 mb-4"> Geotagging Harian</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- PETA -->
                <div class="lg:col-span-2">
                    <div id="map" class="w-full h-64 bg-gray-100 rounded-xl mb-4 z-0 border border-gray-200 shadow-inner"></div>
                    
                    @if(!$isReadOnly)
                        <button 
                            type="button" 
                            id="geotag-btn" 
                            data-url="{{ route('perjalanan.hadir', $perjalanan->id) }}" 
                            {{ (!$isTodayInPeriod || !$bolehGeotagSekarang) ? 'disabled' : '' }} 
                            class="w-full {{ (!$isTodayInPeriod || !$bolehGeotagSekarang) ? 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg' }} font-bold py-3 px-6 rounded-xl transition flex justify-center items-center gap-2">
                            
                            <i class="fa-solid fa-location-dot"></i>
                            @if($sudahAbsenHariIni)  
                                Lokasi Hari Ini Tercatat 
                            @else  
                                Tandai Lokasi Saya 
                            @endif
                        </button>

                        <div class="mt-2 flex items-center justify-center">
                            <span id="geotag-countdown" class="hidden text-xs font-medium px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 border border-yellow-100"></span>
                        </div>

                        @if(!$isTodayInPeriod)
                            <p class="text-center text-xs text-red-500 mt-2 font-bold">{{ $statusMessage }}</p>
                        @endif

                        <p class="text-left text-[11px] text-gray-500 mt-1">
                            Catatan: <br>Tombol geotag akan aktif kembali setelah Anda mengambil foto untuk geotag sebelumnya dan minimal 5 menit setelah geotag terakhir.
                        </p>

                        
                    @else
                        <p class="text-center text-xs text-gray-400 mt-2 italic">
                            Geotagging tidak dapat dilakukan karena perjalanan dinas ini telah selesai / tugas Anda sudah selesai.
                        </p>
                    @endif
                </div>

                <!-- LIST KOORDINAT -->
                <div class="lg:col-span-1 bg-gray-50 rounded-xl p-4 max-h-96 overflow-y-auto border border-gray-200">
                    <h3 class="text-xs font-bold text-gray-500 mb-3 uppercase tracking-wider">Titik Koordinat</h3>
                    <div class="space-y-3">
                        @foreach($geotagHistory as $h)
                        <div 
                            onclick="focusLocation({{ $h['lat_raw'] ?? 'null' }}, {{ $h['long_raw'] ?? 'null' }}, '{{ $h['tanggal'] }}')"
                            class="flex items-start gap-3 p-3 rounded-lg border transition cursor-pointer hover:shadow-md
                            {{ $h['status'] == 'Sudah' ? 'bg-white border-gray-200 hover:border-blue-300' : 'bg-gray-100 border-transparent opacity-60 cursor-not-allowed' }}">
                            
                            <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold 
                                {{ $h['status'] == 'Sudah' ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-500' }}">
                                H{{ $h['hari_ke'] }}
                            </div>
                            
                            <div class="flex-1">
                                <p class="text-xs font-bold text-gray-700">{{ $h['tanggal'] }}</p>
                                <p class="text-[11px] text-gray-500">
                                    {{ ($h['waktu'] ?? '-') !== '-' ? 'Pukul ' . $h['waktu'] : '—' }}
                                </p>
                                @if($h['status'] == 'Sudah')
                                    <p class="text-[10px] text-gray-500 mt-0.5 flex items-center gap-1">
                                        <i class="fa-solid fa-map-pin text-red-500"></i> Lihat di Peta
                                    </p>
                                @else
                                    <p class="text-[10px] text-gray-400 italic">Belum ada data</p>
                                @endif
                            </div>

                            @if(($h['foto_count'] ?? 0) > 0)
                                <div class="flex flex-col items-center gap-1 ml-2">
                                    @foreach($h['photo_urls'] as $idx => $url)
                                        <a href="{{ $url }}" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-100" title="Lihat Foto Geotagging {{ $idx + 1 }}">
                                            <i class="fa-solid fa-camera text-[11px]"></i>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mt-5 bg-gray-50 border border-dashed border-gray-300 rounded-xl p-4 flex flex-col items-center">
                <h3 class="text-lg font-bold text-gray-700 mb-4 text-center">Foto Kehadiran</h3>

                <div class="flex flex-wrap justify-center items-center gap-2 mb-3">
                    <button type="button" id="open-camera-btn" data-upload-url="{{ route('perjalanan.fotoGeotag', $perjalanan->id) }}" 
                        {{ !$bolehFotoSekarang ? 'disabled' : '' }}
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-medium {{ !$bolehFotoSekarang ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-gray-800 text-white hover:bg-black' }}">
                        <i class="fa-solid fa-camera"></i>
                        <span>Aktifkan Kamera</span>
                    </button>

                    <button type="button" id="switch-camera-btn" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium border border-gray-300 text-gray-700 hover:bg-gray-100">
                        <i class="fa-solid fa-repeat"></i>
                        <span>Ganti Kamera Depan/Belakang</span>
                    </button>

                    <button type="button" id="capture-photo-btn" class="hidden inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-medium bg-blue-600 text-white hover:bg-blue-700">
                        <i class="fa-solid fa-circle-dot"></i>
                        <span>Ambil Foto</span>
                    </button>

                    <button type="button" id="download-photo-btn" class="hidden inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-green-600 text-white hover:bg-green-700">
                        <i class="fa-solid fa-download"></i>
                        <span>Download Foto</span>
                    </button>
                </div>

                <div class="space-y-2">
                    <video id="camera-stream" class="hidden w-[85%] max-w-[85%] rounded-lg bg-black mx-auto" autoplay playsinline></video>
                    <img id="photo-preview" class="hidden w-full max-h-64 rounded-lg border border-gray-300 object-contain" alt="Foto hasil geotagging">
                        <!-- Modal Preview Foto -->
                        <div id="photoModal" class="fixed inset-0 hidden items-center justify-center z-50">
                        <!-- overlay -->
                        <div id="modalOverlay" class="absolute inset-0 bg-black/60"></div>

                        <!-- dialog -->
                        <div class="relative bg-white rounded-xl shadow-xl w-[90%] max-w-lg mx-auto z-10 p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Preview Foto Kehadiran</h3>
                            <img id="modal-photo" src="" alt="Preview Foto" class="w-full max-h-[60vh] object-contain rounded-md border border-gray-200 mb-3" />
                            <p id="modal-meta" class="text-sm text-gray-600 mb-4"></p>

                            <div class="flex gap-2 justify-between">
                            <div class="flex gap-2">
                                <button id="modal-download-btn" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-medium bg-green-600 text-white hover:bg-green-700">
                                <i class="fa-solid fa-download"></i> Download
                                </button>

                                <button id="modal-save-btn" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan
                                </button>
                            </div>

                            <div>
                                <button id="modal-retry-btn" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-medium bg-gray-200 text-gray-800 hover:bg-gray-300">
                                <i class="fa-solid fa-rotate-left"></i> Ulangi
                                </button>
                            </div>
                            </div>
                        </div>
                        </div>
                    <p id="photo-meta" class="text-[11px] text-gray-500"></p>
                    @if(!$bolehFotoSekarang)
                        <p class="text-[11px] text-red-500 font-medium">Silakan lakukan geotagging terlebih dahulu untuk mengaktifkan kamera.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. URAIAN -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 {{ $isReadOnly ? 'opacity-75 pointer-events-none' : '' }}">
            <h2 class="text-xl font-bold text-gray-800 mb-4"> Uraian Kegiatan</h2>
            <form action="{{ route('perjalanan.storeUraian', $perjalanan->id) }}" method="POST">
                @csrf
                <textarea 
                    name="uraian" 
                    rows="5" 
                    class="w-full border-gray-300 rounded-xl shadow-sm text-sm p-4 focus:ring-blue-500 focus:border-blue-500 @if($isReadOnly) bg-gray-100 text-gray-500 cursor-not-allowed @endif" 
                    placeholder="Ceritakan aktivitas secara detail (min. 100 kata)..." 
                    {{ $isReadOnly ? 'disabled readonly' : '' }}
                >{{ old('uraian', $laporanSaya->uraian ?? '') }}</textarea>

                @if(!$isReadOnly)
                    <div class="flex justify-end mt-3">
                        <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 font-medium text-sm">
                            Simpan Uraian
                        </button>
                    </div>
                @else
                    <p class="mt-2 text-xs text-gray-500 italic">
                        Uraian tidak dapat diubah karena 
                        @if($isRiwayatPerjadin)
                            perjalanan dinas ini telah selesai.
                        @elseif($isMyTaskFinished)
                            Anda sudah menandai tugas ini sebagai selesai.
                        @endif
                    </p>
                @endif
            </form>
        </div>

        <!-- 3. SELESAI -->
        @if(!$isMyTaskFinished)
        @php
            // Tombol final hanya benar-benar bisa diklik kalau:
            // - aturan bisnis canFinish terpenuhi, dan
            // - perjadin belum berstatus "Selesai"
            $finalCanFinish = $canFinish && !$isRiwayatPerjadin;
        @endphp

        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-8 rounded-2xl shadow-lg text-center relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-2xl font-bold mb-2">Sudah Selesai Bertugas?</h3>
                <p class="text-blue-100 mb-6 text-sm max-w-md mx-auto">
                    @if(!$finalCanFinish)
                        <span class="bg-white/20 px-3 py-1 rounded text-yellow-300 font-bold border border-white/30 block mb-2">
                             Tombol Belum Aktif
                        </span>

                        @if($isRiwayatPerjadin)
                            Perjalanan dinas ini telah dinyatakan selesai, sehingga Anda tidak dapat lagi mengubah status tugas.
                        @else
                            {{ $finishMessage ?? '' }}
                        @endif
                    @else
                        Jika Anda sudah kembali dan menyelesaikan semua kegiatan, silakan klik tombol di bawah. <br>
                        <span class="text-yellow-300 font-semibold">Data tidak bisa diubah setelah ini.</span>
                    @endif
                </p>
                
                <form action="{{ route('perjalanan.selesaikan', $perjalanan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan tugas ini? Data uraian dan lokasi tidak dapat diubah lagi.')">
                    @csrf
                    <button 
                        type="submit" 
                        {{ !$finalCanFinish ? 'disabled' : '' }} 
                        class="{{ !$finalCanFinish ? 'bg-gray-400 cursor-not-allowed opacity-70' : 'bg-white text-blue-700 hover:bg-blue-50 hover:scale-105 shadow-xl' }} px-8 py-3 rounded-xl font-bold transition transform">
                        
                        @if(!$finalCanFinish) 
                            Belum Bisa Selesai 
                        @else  
                            ✅ Saya Sudah Selesai 
                        @endif
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>

<!-- Script JS Peta -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Variabel Global untuk Map
    let map;
    let markers = [];

    // Fungsi untuk zoom ke lokasi saat list diklik
    function focusLocation(lat, long, title) {
        if (lat && long) {
            map.flyTo([lat, long], 15, {
                animate: true,
                duration: 1.5
            });
            // Cari marker yang sesuai dan buka popupnya
            markers.forEach(m => {
                if (m.getLatLng().lat == lat && m.getLatLng().lng == long) {
                    m.openPopup();
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Init Map (Default View Indonesia Tengah jika kosong)
        map = L.map('map').setView([-2.548926, 118.0148634], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

        // Render Marker Riwayat
        const historyData = @json($geotagHistory);
        let bounds = [];

        historyData.forEach(p => {
            if(p.lat_raw && p.long_raw) {
                const marker = L.marker([p.lat_raw, p.long_raw])
                                .addTo(map)
                                .bindPopup(`<b>${p.tanggal}</b><br>Lokasi Tercatat`);
                markers.push(marker);
                bounds.push([p.lat_raw, p.long_raw]);
            }
        });

        // Fit Bounds agar semua marker terlihat (jika ada)
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }

        // Logic Tombol Geotagging
        const btn = document.getElementById('geotag-btn');
        const token = document.querySelector('meta[name="csrf-token"]').content;

        if(btn && !btn.disabled) {
            btn.addEventListener('click', () => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sedang mendeteksi...';
                btn.disabled = true;

                if (!navigator.geolocation) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Didukung',
                        text: 'Browser Anda tidak mendukung Geotagging.',
                        confirmButtonText: 'Tutup'
                    }).then(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;

                        // Tampilkan marker sementara (Visual Feedback)
                        L.marker([lat, lng]).addTo(map)
                            .bindPopup("<b>Lokasi Anda Saat Ini</b>").openPopup();
                        map.flyTo([lat, lng], 16);

                        // Kirim ke Server
                        fetch(btn.dataset.url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                latitude: lat, 
                                longitude: lng, 
                                id_tipe: 1
                            })
                        })
                        .then(r => r.json())
                        .then(d => {
                            if(d.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: d.message,
                                    confirmButtonText: 'OK'
                                }).then(() => location.reload());
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: d.message || 'Terjadi kesalahan.',
                                    confirmButtonText: 'Tutup'
                                }).then(() => {
                                    btn.innerHTML = originalText;
                                    btn.disabled = false;
                                });
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Terjadi kesalahan koneksi.',
                                confirmButtonText: 'Tutup'
                            }).then(() => {
                                btn.innerHTML = originalText;
                                btn.disabled = false;
                            });
                        });
                    }, 
                    (err) => {
                        console.error(err);
                        let msg = "Gagal mendapatkan lokasi.";
                        if (err.code == 1) msg = "Izin lokasi ditolak. Mohon aktifkan GPS.";
                        else if (err.code == 2) msg = "Lokasi tidak tersedia (sinyal lemah).";
                        else if (err.code == 3) msg = "Waktu permintaan habis.";

                        Swal.fire({
                            icon: 'warning',
                            title: 'Kesalahan Geotangging',
                            text: msg,
                            confirmButtonText: 'Tutup'
                        }).then(() => {
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        });
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            });
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const openCameraBtn = document.getElementById('open-camera-btn');
    const switchCameraBtn = document.getElementById('switch-camera-btn');
    const capturePhotoBtn = document.getElementById('capture-photo-btn');
    const downloadPhotoBtn = document.getElementById('download-photo-btn');
    const video = document.getElementById('camera-stream');
    const photoPreview = document.getElementById('photo-preview');
    const photoMeta = document.getElementById('photo-meta');

    // Modal elements
    const photoModal = document.getElementById('photoModal');
    const modalPhoto = document.getElementById('modal-photo');
    const modalMeta = document.getElementById('modal-meta');
    const modalDownloadBtn = document.getElementById('modal-download-btn');
    const modalSaveBtn = document.getElementById('modal-save-btn');
    const modalRetryBtn = document.getElementById('modal-retry-btn');
    const modalOverlay = document.getElementById('modalOverlay');

    if (!openCameraBtn || !switchCameraBtn || !capturePhotoBtn || !downloadPhotoBtn || !video || !photoPreview || !photoMeta || !photoModal) {
        return;
    }

    const token = document.querySelector('meta[name="csrf-token"]').content;
    const uploadUrl = openCameraBtn.dataset.uploadUrl || null;

    let currentStream = null;
    let useFrontCamera = false;
    let lastPhotoDataUrl = null;
    let lastPhotoSize = 0;
    let lastLat = null;
    let lastLng = null;
    let lastAddress = '';

    // UI helpers
    function setOpenBtnToActiveState() {
        openCameraBtn.innerHTML = '<i class="fa-solid fa-camera"></i> <span>Matikan Kamera</span>';
        openCameraBtn.classList.remove('bg-gray-800', 'text-white');
        openCameraBtn.classList.add('bg-red-600', 'hover:bg-red-700', 'text-white');
    }
    function setOpenBtnToInactiveState() {
        openCameraBtn.innerHTML = '<i class="fa-solid fa-camera"></i> <span>Aktifkan Kamera</span>';
        openCameraBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
        openCameraBtn.classList.add('bg-gray-800', 'text-white', 'hover:bg-black');
    }
    function showCaptureControls() { capturePhotoBtn.classList.remove('hidden'); }
    function hideCaptureControls() { capturePhotoBtn.classList.add('hidden'); downloadPhotoBtn.classList.add('hidden'); }
    function resetPreview() { lastPhotoDataUrl = null; lastPhotoSize = 0; photoPreview.src = ''; photoPreview.classList.add('hidden'); photoMeta.textContent = ''; }

    // Camera start / stop
    async function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            Swal.fire({ icon: 'error', title: 'Tidak Didukung', text: 'Browser tidak mendukung kamera.', confirmButtonText: 'Tutup' });
            return false;
        }
        try {
            const constraints = { video: { facingMode: useFrontCamera ? 'user' : 'environment' } };
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            if (currentStream) stopStream();
            currentStream = stream;
            video.srcObject = stream;
            video.classList.remove('hidden');

            setOpenBtnToActiveState();
            showCaptureControls();
            resetPreview();
            return true;
        } catch (e) {
            console.error(e);
            Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Gagal mengakses kamera. Pastikan izin kamera sudah diberikan.', confirmButtonText: 'Tutup' });
            return false;
        }
    }
    
    function stopStream() {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
        if (video) { video.srcObject = null; video.classList.add('hidden'); }
        setOpenBtnToInactiveState();
        hideCaptureControls();
        resetPreview();
    }

    // Geolocation helpers
    function getCurrentPositionSafely() {
        if (!navigator.geolocation) return Promise.resolve(null);
        return new Promise((resolve) => {
            navigator.geolocation.getCurrentPosition(resolve, () => resolve(null), { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
        });
    }
    async function reverseGeocode(lat, lng) {
        try {
            const url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng);
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return '';
            const data = await res.json();
            return data && data.display_name ? data.display_name : '';
        } catch (e) { console.error(e); return ''; }
    }

    // Upload to server (called when user confirms save)
    async function uploadPhotoToServer_confirmed(dataUrl, lat, lng) {
        if (!uploadUrl) return { status: 'error', message: 'Upload URL tidak tersedia.' };
        try {
            const res = await fetch(uploadUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ image: dataUrl, latitude: lat, longitude: lng })
            });
            return await res.json();
        } catch (e) {
            console.error(e);
            return { status: 'error', message: 'Terjadi kesalahan saat mengirim foto ke server.' };
        }
    }

    // Show / hide modal
    function openModalWithPhoto(dataUrl, metaText) {
        modalPhoto.src = dataUrl;
        modalMeta.textContent = metaText;
        photoModal.classList.remove('hidden');
        // center flex
        photoModal.classList.add('flex');
    }
    function closeModal() {
        photoModal.classList.add('hidden');
        photoModal.classList.remove('flex');
        modalPhoto.src = '';
        modalMeta.textContent = '';
    }

    // Toggle camera button
    openCameraBtn.addEventListener('click', async function () {
        if (currentStream) {
            stopStream();
            return;
        }
        await startCamera();
    });

    // Switch camera
    switchCameraBtn.addEventListener('click', async function () {
        useFrontCamera = !useFrontCamera;
        if (currentStream) {
            stopStream();
            setTimeout(() => startCamera(), 150);
        }
    });

    // Capture photo -> show modal (do NOT upload immediately) — modal only, no inline preview
    capturePhotoBtn.addEventListener('click', async function () {
        if (!video || !video.srcObject) return;

        const canvas = document.createElement('canvas');
        let width = video.videoWidth || 1280;
        let height = video.videoHeight || 720;
        const maxWidth = 1280, maxHeight = 1280;
        const ratio = Math.min(maxWidth / width, maxHeight / height, 1);
        width = Math.round(width * ratio);
        height = Math.round(height * ratio);

        canvas.width = width; canvas.height = height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, width, height);

        // geolocation
        const pos = await getCurrentPositionSafely();
        lastLat = null; lastLng = null; lastAddress = '';
        if (pos && pos.coords) {
            lastLat = pos.coords.latitude;
            lastLng = pos.coords.longitude;
            lastAddress = await reverseGeocode(lastLat, lastLng);
        }

        // timestamp + coords text
        const now = new Date();
        const timeStr = now.toLocaleString('id-ID', { hour12: false });
        const textLines = ['Waktu: ' + timeStr];
        if (lastLat !== null && lastLng !== null) textLines.push('Lat: ' + lastLat.toFixed(6) + ', Lng: ' + lastLng.toFixed(6));
        if (lastAddress) textLines.push(lastAddress);

        // watermark
        const padding = 8, lineHeight = 16, fontSize = 12, x = 10;
        ctx.font = fontSize + 'px sans-serif';
        let textWidth = 0;
        textLines.forEach(line => { const w = ctx.measureText(line).width; if (w > textWidth) textWidth = w; });
        const boxWidth = textWidth + padding * 2;
        const boxHeight = lineHeight * textLines.length + padding * 2;
        const y = height - boxHeight - 10;
        ctx.fillStyle = 'rgba(0,0,0,0.6)'; ctx.fillRect(x, y, boxWidth, boxHeight);
        ctx.fillStyle = '#ffffff';
        textLines.forEach((line, idx) => ctx.fillText(line, x + padding, y + padding + (idx + 1) * lineHeight - 4));

        // compress
        let quality = 0.9;
        let dataUrl = canvas.toDataURL('image/jpeg', quality);
        let fileSize = Math.round(dataUrl.length * 3 / 4);
        const targetSize = 200 * 1024;
        while (fileSize > targetSize && quality > 0.2) {
            quality -= 0.1;
            dataUrl = canvas.toDataURL('image/jpeg', quality);
            fileSize = Math.round(dataUrl.length * 3 / 4);
        }

        lastPhotoDataUrl = dataUrl;
        lastPhotoSize = fileSize;

        // build meta text for modal
        const sizeKb = Math.round(fileSize / 1024);
        let metaText = 'Perkiraan ukuran: ' + sizeKb + ' KB';
        if (lastLat !== null && lastLng !== null) metaText += ' | Lat: ' + lastLat.toFixed(6) + ', Lng: ' + lastLng.toFixed(6);
        if (lastAddress) metaText += ' | Lokasi: ' + lastAddress;

        // open modal with preview & meta (no inline preview)
        openModalWithPhoto(dataUrl, metaText);
    });


    // Modal: download
    modalDownloadBtn.addEventListener('click', function () {
        if (!lastPhotoDataUrl) return;
        const a = document.createElement('a');
        a.href = lastPhotoDataUrl;
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hour = String(now.getHours()).padStart(2, '0');
        const minute = String(now.getMinutes()).padStart(2, '0');
        const second = String(now.getSeconds()).padStart(2, '0');
        a.download = 'geotag_' + year + month + day + '_' + hour + minute + second + '.jpg';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    // Modal: retry (ambil ulang) -> close modal, keep camera on for retake
    modalRetryBtn.addEventListener('click', function () {
        closeModal();
        // reset small preview but keep camera running
        photoPreview.classList.add('hidden');
        downloadPhotoBtn.classList.add('hidden');
        photoMeta.textContent = '';
        lastPhotoDataUrl = null;
    });

    // Modal: save -> upload then close modal (disable tombol selama proses)
    modalSaveBtn.addEventListener('click', async function () {
        if (!lastPhotoDataUrl) {
            return Swal.fire({ icon: 'info', title: 'Info', text: 'Tidak ada foto untuk disimpan.', confirmButtonText: 'Tutup' });
        }

        modalSaveBtn.disabled = true;
        modalSaveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        modalDownloadBtn.disabled = true;
        modalRetryBtn.disabled = true;

        const res = await uploadPhotoToServer_confirmed(lastPhotoDataUrl, lastLat, lastLng);

        modalSaveBtn.disabled = false;
        modalSaveBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan';
        modalDownloadBtn.disabled = false;
        modalRetryBtn.disabled = false;

        if (res && res.status === 'success') {
            closeModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: res.message || 'Foto geotagging tersimpan.',
                confirmButtonText: 'OK'
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: (res && res.message) ? res.message : 'Gagal menyimpan foto.',
                confirmButtonText: 'Tutup'
            });
        }
    });

    // overlay click closes modal (optional) — but keep preview intact
    modalOverlay.addEventListener('click', closeModal);

    // Cleanup when unload
    window.addEventListener('beforeunload', function () {
        stopStream();
    });

    // init state
    setOpenBtnToInactiveState();
    hideCaptureControls();
    resetPreview();

    // lastGeotagAt dikirim dari server (ISO atau null). Pastikan nama variabel sama di compact()
    const lastGeotagAt = @json(optional($lastGeotagHariIni)->created_at ?? null);

    // cari tombol geotag
    const geotagBtn = document.getElementById('geotag-btn');
    if (!geotagBtn) return;

    // simpan HTML asli supaya bisa dikembalikan nanti
    if (!geotagBtn.dataset.origHtml) geotagBtn.dataset.origHtml = geotagBtn.innerHTML;

    // helper format mm:ss
    function formatMMSS(totalSeconds) {
        const mm = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
        const ss = String(Math.floor(totalSeconds % 60)).padStart(2, '0');
        return mm + ':' + ss;
    }

    // enable tombol (restore)
    function enableGeotagBtn() {
        geotagBtn.disabled = false;
        // restore classes (ensure visible style)
        geotagBtn.classList.remove('bg-gray-100','text-gray-400','cursor-not-allowed','border','border-gray-200');
        geotagBtn.classList.add('bg-blue-600','hover:bg-blue-700','text-white');
        // restore original innerHTML if stored
        if (geotagBtn.dataset.origHtml) geotagBtn.innerHTML = geotagBtn.dataset.origHtml;
    }

    // disable tombol and show custom text
    function disableGeotagBtnWithText(text) {
        geotagBtn.disabled = true;
        geotagBtn.classList.remove('bg-blue-600','hover:bg-blue-700','text-white');
        geotagBtn.classList.add('bg-gray-100','text-gray-400','cursor-not-allowed','border','border-gray-200');
        geotagBtn.innerHTML = '<i class="fa-solid fa-clock"></i> ' + text;
    }

    // jika tidak ada lastGeotag -> pastikan enable
    if (!lastGeotagAt) {
        enableGeotagBtn();
        return;
    }

    // parse timestamp
    const lastTs = Date.parse(lastGeotagAt);
    if (isNaN(lastTs)) { enableGeotagBtn(); return; }

    const minMs = 5 * 60 * 1000; // 5 menit
    function tickCountdown() {
        const now = Date.now();
        const elapsed = now - lastTs;
        if (elapsed >= minMs) {
            // selesai: enable tombol & clear interval
            enableGeotagBtn();
            clearInterval(countIv);
            return;
        }
        const remainingSec = Math.ceil((minMs - elapsed) / 1000);
        // tampilkan format MM:SS — contoh "Tunggu 04:12"
        const mmss = formatMMSS(remainingSec);
        disableGeotagBtnWithText('Tunggu ' + mmss);
    }

    // run immediately and tiap 1 detik
    tickCountdown();
    const countIv = setInterval(tickCountdown, 1000);
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
