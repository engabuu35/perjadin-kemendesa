@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<div class="ml-[80px] p-6 mt-[90px]">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Perjalanan Dinas</h1>
            <p class="text-gray-500 text-sm">Pantau lokasi harian dan laporkan kegiatan Anda.</p>
        </div>
        <a href="{{ url()->previous() }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Kembali</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- KOLOM KIRI -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Peta & Geotagging -->
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Geotagging Harian</h2>
                <div id="map" class="w-full h-64 bg-gray-100 rounded-lg mb-4 z-0"></div>
                
                <div class="flex flex-col items-center">
                    <p id="geotag-status" class="text-sm text-gray-600 mb-3 font-medium">
                        {{-- LOGIKA PESAN STATUS --}}
                        @if(!$isTodayInPeriod)
                            <span class="text-red-500">⛔ {{ $statusMessage }} (Tombol Nonaktif)</span>
                        @elseif($sudahAbsenHariIni)
                            <span class="text-green-600">✅ Anda sudah menandai lokasi untuk hari ini.</span>
                        @else
                            Silakan tandai lokasi Anda hari ini.
                        @endif
                    </p>
                    
                    {{-- LOGIKA DISABLE TOMBOL --}}
                    <button type="button" id="geotag-btn"
                            data-url="{{ route('perjalanan.hadir', $perjalanan->id) }}"
                            {{ (!$isTodayInPeriod || $sudahAbsenHariIni) ? 'disabled' : '' }}
                            class="{{ (!$isTodayInPeriod || $sudahAbsenHariIni) 
                                ? 'bg-gray-400 cursor-not-allowed' 
                                : 'bg-blue-600 hover:bg-blue-700 hover:-translate-y-0.5' 
                            }} text-white font-bold py-3 px-8 rounded-lg shadow-md transition transform">
                        
                        @if(!$isTodayInPeriod)
                            🚫 Di Luar Jadwal
                        @elseif($sudahAbsenHariIni)
                            ✓ Sudah Absen
                        @else
                            📍 Tandai Lokasi (Check-In)
                        @endif
                    </button>
                </div>
            </div>

            <!-- Tabel -->
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Riwayat Perjalanan</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hari Ke</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Koordinat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($geotagHistory as $history)
                            <tr class="{{ $history['status'] == 'Sudah' ? 'bg-blue-50' : '' }}">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $history['hari_ke'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $history['tanggal'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $history['lokasi'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $history['waktu'] }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($history['status'] == 'Sudah')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Hadir</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN (Laporan) -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl shadow-md sticky top-24">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Laporan Kegiatan</h2>
                <form action="{{ route('perjalanan.storeLaporan', $perjalanan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Uraian Kegiatan</label>
                        <textarea name="uraian" rows="6" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Tuliskan progress..." {{ ($laporan && $laporan->is_final) ? 'disabled' : '' }}>{{ old('uraian', $laporan->uraian ?? '') }}</textarea>
                    </div>

                    @if($laporan && $laporan->bukti->count() > 0)
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Bukti Tersimpan:</p>
                        <ul class="space-y-2">
                            @foreach($laporan->bukti as $file)
                            <li class="flex items-center justify-between bg-gray-50 p-2 rounded text-xs">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-600">{{ $file->kategori }}</span>
                                    <a href="{{ asset('storage/'.$file->path_file) }}" target="_blank" class="text-blue-600 truncate max-w-[150px]">{{ $file->nama_file }}</a>
                                </div>
                                @if(!$laporan->is_final)
                                    <a href="{{ route('bukti.delete', $file->id) }}" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus file ini?')">x</a>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(!$laporan || !$laporan->is_final)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Baru</label>
                        <div id="upload-container" class="space-y-3">
                            <div class="flex gap-2">
                                <!-- [PERUBAHAN 1] Awalnya input type="text", sekarang diubah menjadi SELECT dropdown -->
                                <select name="kategori[]" class="w-1/3 text-xs border-gray-300 rounded" required>
                                    <option value="" disabled selected>Pilih Kategori</option>
                                        <option value="Tiket">Tiket</option>
                                        <option value="Uang Harian">Uang Harian</option>
                                        <option value="Penginapan">Penginapan</option>
                                        <option value="Uang Representasi">Uang Representasi</option>
                                        <option value="Sewa Kendaraan">Sewa Kendaraan</option>
                                        <option value="Pengeluaran Riil">Pengeluaran Riil</option>
                                        <option value="SSPB">SSPB</option>
                                </select>
                                <!-- [AKHIR PERUBAHAN 1] -->

                                <input type="file" name="bukti[]" class="w-2/3 text-xs text-gray-500" required>
                            </div>
                        </div>
                        <button type="button" id="add-upload" class="mt-2 text-xs text-blue-600 font-semibold hover:underline">+ Tambah File Lain</button>
                    </div>
                    @endif

                    <div class="flex flex-col gap-3 mt-6">
                        @if($laporan && $laporan->is_final)
                            <div class="bg-green-100 text-center p-3 rounded-lg text-green-800 font-bold">✓ Laporan Selesai</div>
                        @else
                            <button type="submit" name="action_type" value="draft" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition">💾 Simpan Progress</button>
                            @if($isLastDayPassed)
                                <button type="submit" name="action_type" value="finish" onclick="return confirm('Selesaikan laporan?')" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition shadow-md">🚀 Selesaikan & Kirim</button>
                            @else
                                <p class="text-xs text-center text-gray-400 mt-2">Tombol "Selesai" aktif pada {{ \Carbon\Carbon::parse($perjalanan->tgl_selesai)->format('d M Y') }}.</p>
                            @endif
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('map').setView([-2.548926, 118.0148634], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    const historyData = @json($geotagHistory);
    historyData.forEach(point => {
        if(point.lat_raw && point.long_raw) {
             L.marker([point.lat_raw, point.long_raw])
              .addTo(map)
              .bindPopup(`<b>Hari ke-${point.hari_ke}</b><br>${point.tanggal}<br>${point.waktu}`);
             map.setView([point.lat_raw, point.long_raw], 10);
        }
    });

    const geotagBtn = document.getElementById('geotag-btn');
    const statusEl = document.getElementById('geotag-status');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (geotagBtn && !geotagBtn.disabled) {
        geotagBtn.addEventListener('click', function() {
            statusEl.textContent = 'Mendeteksi lokasi...';
            navigator.geolocation.getCurrentPosition(pos => {
                const { latitude, longitude } = pos.coords;
                fetch(geotagBtn.dataset.url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ latitude, longitude, id_tipe: 1 })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        alert(data.message);
                        location.reload(); 
                    } else {
                        alert(data.message);
                    }
                });
            }, err => {
                alert('Gagal lokasi: ' + err.message);
            });
        });
    }

    const addUploadBtn = document.getElementById('add-upload');
    const uploadContainer = document.getElementById('upload-container');
    if(addUploadBtn) {
        addUploadBtn.addEventListener('click', () => {
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            
            // [PERUBAHAN 2]
            // Script Javascript untuk menambah baris baru juga diubah.
            // Sekarang menyisipkan elemen SELECT dropdown, bukan input text lagi.
            div.innerHTML = `
                <select name="kategori[]" class="w-1/3 text-xs border-gray-300 rounded" required>
                    <option value="" disabled selected>Pilih Kategori</option>
                    <option value="Tiket">Tiket</option>
                    <option value="Uang Harian">Uang Harian</option>
                    <option value="Penginapan">Penginapan</option>
                    <option value="Uang Representasi">Uang Representasi</option>
                    <option value="Sewa Kendaraan">Sewa Kendaraan</option>
                    <option value="Pengeluaran Riil">Pengeluaran Riil</option>
                    <option value="SSPB">SSPB</option>
                </select>
                <input type="file" name="bukti[]" class="w-2/3 text-xs text-gray-500" required>
            `;
            // [AKHIR PERUBAHAN 2]

            uploadContainer.appendChild(div);
        });
    }
});
</script>
@endsection