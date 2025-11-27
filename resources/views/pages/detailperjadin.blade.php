@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<div class="ml-[80px] p-6 mt-[90px] max-w-4xl mx-auto">
    
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Pelaksanaan Tugas</h1>
            <p class="text-gray-500 mt-1">Surat Tugas: <span class="font-semibold text-blue-600">{{ $perjalanan->nomor_surat }}</span></p>
            
            @if($isMyTaskFinished)
                <span class="inline-block mt-2 bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold border border-green-200">
                    ✅ Tugas Anda Sudah Selesai
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusBadgeClass ?? '' }}">
                {{ $statusPegawai ?? '—' }}
            </span>
            @endif
        </div>
        
        <div class="flex gap-2">
            <a href="{{ url()->previous() }}" class="bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-xl hover:bg-gray-50 transition shadow-sm font-medium">← Kembali</a>
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
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 {{ $isMyTaskFinished ? 'opacity-75 pointer-events-none grayscale-[20%]' : '' }}">
            <h2 class="text-xl font-bold text-gray-800 mb-4">📍 Geotagging Harian</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div id="map" class="w-full h-64 bg-gray-100 rounded-xl mb-4 z-0 border border-gray-200"></div>
                    @if(!$isMyTaskFinished)
                        <button type="button" id="geotag-btn" data-url="{{ route('perjalanan.hadir', $perjalanan->id) }}" {{ (!$isTodayInPeriod || $sudahAbsenHariIni) ? 'disabled' : '' }} class="w-full {{ (!$isTodayInPeriod || $sudahAbsenHariIni) ? 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg' }} font-bold py-3 px-6 rounded-xl transition">
                            @if($sudahAbsenHariIni) ✓ Lokasi Hari Ini Tercatat @else 🎯 Tandai Lokasi Saya @endif
                        </button>
                        @if(!$isTodayInPeriod)<p class="text-center text-xs text-red-500 mt-2 font-bold">{{ $statusMessage }}</p>@endif
                    @endif
                </div>
                <div class="lg:col-span-1 bg-gray-50 rounded-xl p-4 h-64 overflow-y-auto border border-gray-200">
                    <h3 class="text-xs font-bold text-gray-500 mb-3 uppercase">Riwayat</h3>
                    <div class="space-y-3">
                        @foreach($geotagHistory as $h)
                        <div class="flex items-start gap-3 p-2 rounded-lg {{ $h['status'] == 'Sudah' ? 'bg-white border border-gray-200' : 'opacity-50' }}">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $h['status'] == 'Sudah' ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-500' }}">H{{ $h['hari_ke'] }}</div>
                            <div><p class="text-xs font-bold">{{ $h['tanggal'] }}</p><p class="text-[10px]">{{ $h['lokasi'] }}</p></div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. URAIAN -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 {{ $isMyTaskFinished ? 'opacity-75 pointer-events-none' : '' }}">
            <h2 class="text-xl font-bold text-gray-800 mb-4">📝 Uraian Kegiatan</h2>
            <form action="{{ route('perjalanan.storeUraian', $perjalanan->id) }}" method="POST">
                @csrf
                <textarea name="uraian" rows="5" class="w-full border-gray-300 rounded-xl shadow-sm text-sm p-4" placeholder="Ceritakan aktivitas..." {{ $isMyTaskFinished ? 'disabled' : '' }}>{{ old('uraian', $laporanSaya->uraian ?? '') }}</textarea>
                @if(!$isMyTaskFinished)<div class="flex justify-end mt-3"><button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 font-medium text-sm">Simpan Uraian</button></div>@endif
            </form>
        </div>

        <!-- 3. SELESAI (LOGIKA BARU) -->
        @if(!$isMyTaskFinished)
        <div class="bg-blue-600 text-white p-8 rounded-2xl shadow-lg text-center relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-2xl font-bold mb-2">Sudah Selesai Bertugas?</h3>
                <p class="text-blue-100 mb-6 text-sm max-w-md mx-auto">
                    @if(!$canFinish)
                        <span class="bg-white/20 px-3 py-1 rounded text-yellow-300 font-bold border border-white/30 block mb-2">
                            ⚠️ Tombol Belum Aktif
                        </span>
                        {{ $finishMessage }}
                    @else
                        Jika Anda sudah kembali dan menyelesaikan semua kegiatan, silakan klik tombol di bawah. <br>
                        <span class="text-yellow-300 font-semibold">Data tidak bisa diubah setelah ini.</span>
                    @endif
                </p>
                
                <form action="{{ route('perjalanan.selesaikan', $perjalanan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan tugas ini?')">
                    @csrf
                    <button type="submit" {{ !$canFinish ? 'disabled' : '' }} 
                        class="{{ !$canFinish ? 'bg-gray-400 cursor-not-allowed opacity-70' : 'bg-white text-blue-700 hover:bg-blue-50 hover:scale-105' }} px-8 py-3 rounded-xl font-bold transition transform shadow-md">
                        @if(!$canFinish) ⏳ Belum Bisa Selesai @else ✅ Saya Sudah Selesai @endif
                    </button>
                </form>
            </div>
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-blue-500 to-blue-700 opacity-50"></div>
        </div>
        @endif

    </div>
</div>

<!-- Script JS Peta -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('map').setView([-2.548926, 118.0148634], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
    @json($geotagHistory).forEach(p => {
        if(p.lat_raw) L.marker([p.lat_raw, p.long_raw]).addTo(map).bindPopup(p.tanggal);
    });
    const btn = document.getElementById('geotag-btn');
    const token = document.querySelector('meta[name="csrf-token"]').content;
    if(btn && !btn.disabled) {
        btn.addEventListener('click', () => {
            btn.innerHTML = 'Sedang mendeteksi...';
            navigator.geolocation.getCurrentPosition(pos => {
                fetch(btn.dataset.url, {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':token},
                    body: JSON.stringify({latitude:pos.coords.latitude, longitude:pos.coords.longitude, id_tipe:1})
                }).then(r=>r.json()).then(d=>{alert(d.message);location.reload()});
            }, ()=> alert('Gagal lokasi.'));
        });
    }
});
</script>
@endsection