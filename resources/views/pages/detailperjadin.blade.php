@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<div class="ml-[80px] p-6 mt-[90px] max-w-5xl mx-auto">
    
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detail Perjalanan Dinas</h1>
            <p class="text-gray-500 mt-1">Surat Tugas: <span class="font-semibold text-blue-600">{{ $perjalanan->nomor_surat }}</span></p>
            <p class="text-gray-500 text-sm">Tujuan: {{ $perjalanan->tujuan }}</p>
        </div>
        <a href="{{ url()->previous() }}" class="bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-xl hover:bg-gray-50 transition shadow-sm font-medium">← Kembali</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col gap-8">
        
        <!-- BAGIAN 1: GEOTAGGING (Sama seperti sebelumnya) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">📍 Geotagging Harian</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div id="map" class="w-full h-64 bg-gray-100 rounded-xl mb-4 z-0 border border-gray-200"></div>
                    <button type="button" id="geotag-btn"
                            data-url="{{ route('perjalanan.hadir', $perjalanan->id) }}"
                            {{ (!$isTodayInPeriod || $sudahAbsenHariIni) ? 'disabled' : '' }}
                            class="w-full {{ (!$isTodayInPeriod || $sudahAbsenHariIni) ? 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg' }} font-bold py-3 px-6 rounded-xl transition">
                        @if($sudahAbsenHariIni) ✓ Lokasi Tercatat @else 🎯 Tandai Lokasi Saya @endif
                    </button>
                    @if(!$isTodayInPeriod)
                        <p class="text-center text-xs text-red-500 mt-2 font-bold">{{ $statusMessage }}</p>
                    @endif
                </div>
                <div class="lg:col-span-1 bg-gray-50 rounded-xl p-4 h-64 overflow-y-auto">
                    <h3 class="text-sm font-bold text-gray-700 mb-3">Riwayat Absensi</h3>
                    <div class="space-y-3">
                        @foreach($geotagHistory as $h)
                        <div class="flex items-start gap-3 p-2 rounded-lg {{ $h['status'] == 'Sudah' ? 'bg-white border border-gray-200' : 'opacity-50' }}">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $h['status'] == 'Sudah' ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-500' }}">H{{ $h['hari_ke'] }}</div>
                            <div>
                                <p class="text-xs font-bold text-gray-800">{{ $h['tanggal'] }}</p>
                                <p class="text-[10px] text-gray-500">{{ $h['waktu'] }} | {{ $h['lokasi'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- BAGIAN 2: URAIAN KEGIATAN (Sama seperti sebelumnya) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-800 mb-4">📝 Uraian Kegiatan</h2>
            <form action="{{ route('perjalanan.storeUraian', $perjalanan->id) }}" method="POST">
                @csrf
                <textarea name="uraian" rows="4" class="w-full border-gray-300 rounded-xl shadow-sm text-sm p-4 bg-gray-50" placeholder="Progress pekerjaan..." {{ ($laporanSaya && $laporanSaya->is_final) ? 'disabled' : '' }}>{{ old('uraian', $laporanSaya->uraian ?? '') }}</textarea>
                <div class="flex justify-end gap-3 mt-4">
                    @if(!($laporanSaya && $laporanSaya->is_final))
                        <button type="submit" name="action_type" value="draft" class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg text-sm">Simpan Draft</button>
                        <button type="submit" name="action_type" value="finish" onclick="return confirm('Yakin final?')" class="bg-green-600 text-white px-5 py-2 rounded-lg text-sm">Simpan Final</button>
                    @else
                        <span class="text-green-600 font-bold bg-green-50 px-4 py-2 rounded-lg">✓ Final</span>
                    @endif
                </div>
            </form>
        </div>

        <!-- BAGIAN 3: BUKTI KEUANGAN (DIPERBAIKI JADI TABEL) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">📂 Rincian Biaya & Bukti</h2>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">Satu pintu untuk semua anggota</span>
            </div>

            <div class="space-y-8">
                @foreach($allPeserta as $peserta)
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <!-- Header Kartu -->
                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">{{ substr($peserta->nama, 0, 2) }}</div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm">{{ $peserta->nama }}</h3>
                                <p class="text-xs text-gray-500 font-mono">{{ $peserta->nip }} | {{ $peserta->role_perjadin }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 uppercase">Total Reimburse</p>
                            <p class="font-bold text-blue-600 text-lg">Rp {{ number_format($peserta->total_biaya, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- TABEL RINCIAN (PENGGANTI KOTAK-KOTAK SEBELUMNYA) -->
                        @if($peserta->laporan && $peserta->laporan->bukti->count() > 0)
                        <div class="overflow-x-auto mb-6">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2">Kategori</th>
                                        <th class="px-4 py-2">Nominal (Rp)</th>
                                        <th class="px-4 py-2">File Bukti</th>
                                        <th class="px-4 py-2 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($peserta->laporan->bukti as $file)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-4 py-2 font-medium text-gray-900">{{ $file->kategori }}</td>
                                        <td class="px-4 py-2">Rp {{ number_format($file->nominal, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2">
                                            @if($file->path_file)
                                                <a href="{{ asset('storage/'.$file->path_file) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1">
                                                    📄 Lihat File
                                                </a>
                                            @else
                                                <span class="text-gray-400 italic text-xs">Tidak ada file</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('bukti.delete', $file->id) }}" onclick="return confirm('Hapus item ini?')" class="text-red-500 hover:text-red-700 font-bold px-2">×</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <p class="text-center text-gray-400 text-sm italic mb-6">Belum ada rincian biaya yang dimasukkan.</p>
                        @endif

                        <!-- FORM TAMBAH (ADD ROW) -->
                        <form action="{{ route('perjalanan.storeBukti', $perjalanan->id) }}" method="POST" enctype="multipart/form-data" class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 flex flex-col md:flex-row gap-3 items-end">
                            @csrf
                            <input type="hidden" name="target_nip" value="{{ $peserta->nip }}">

                            <div class="w-full md:w-1/4">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Kategori</label>
                                <select name="kategori" class="w-full text-xs border-gray-300 rounded-lg py-2" required>
                                    <option value="" disabled selected>- Pilih -</option>
                                    <option value="Tiket">Tiket</option>
                                    <option value="Penginapan">Penginapan</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Sewa Kendaraan">Sewa Kendaraan</option>
                                    <option value="Uang Harian">Uang Harian</option>
                                    <option value="Uang Representasi">Uang Representasi</option>
                                    <option value="Pengeluaran Riil">Pengeluaran Riil</option>
                                    <option value="SSPB">SSPB</option>
                                </select>
                            </div>
                            
                            <div class="w-full md:w-1/4">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nominal (Rp)</label>
                                <input type="number" name="nominal" placeholder="0" class="w-full text-xs border-gray-300 rounded-lg py-2" required>
                            </div>

                            <div class="w-full md:w-1/3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">File Bukti (Opsional)</label>
                                <input type="file" name="bukti" class="w-full text-xs text-gray-500 bg-white border border-gray-300 rounded-lg file:py-2 file:px-4 file:bg-blue-600 file:text-white file:border-0 hover:file:bg-blue-700">
                            </div>

                            <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-xs shadow-sm whitespace-nowrap">
                                + Tambah
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logic Peta
    const map = L.map('map').setView([-2.548926, 118.0148634], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    const historyData = @json($geotagHistory);
    historyData.forEach(point => {
        if(point.lat_raw && point.long_raw) {
             L.marker([point.lat_raw, point.long_raw]).addTo(map)
              .bindPopup(`<b>H${point.hari_ke}: ${point.tanggal}</b><br>${point.waktu}`);
        }
    });

    // Geotag Button
    const geotagBtn = document.getElementById('geotag-btn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (geotagBtn && !geotagBtn.disabled) {
        geotagBtn.addEventListener('click', function() {
            geotagBtn.innerHTML = 'Sedang mendeteksi...';
            navigator.geolocation.getCurrentPosition(pos => {
                const { latitude, longitude } = pos.coords;
                fetch(geotagBtn.dataset.url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ latitude, longitude, id_tipe: 1 })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    location.reload(); 
                })
                .catch(err => alert('Error: ' + err));
            }, err => {
                alert('Gagal mendapatkan lokasi. Pastikan GPS aktif.');
                geotagBtn.innerHTML = '🎯 Tandai Lokasi Saya';
            });
        });
    }
});
</script>
@endsection