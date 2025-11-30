@extends('layouts.appPPK')

@section('title', 'Detail Verifikasi PPK')

@section('content')
<main class="ml-0 sm:ml-[80px] max-w-[98%] mx-auto px-2 py-6">
    
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6 px-2">
        <x-page-title 
        title="Verifikasi Pembayaran"
        subtitle="Surat Tugas: {{ $perjalanan->nomor_surat }}" />
       <div class="flex items-center gap-3">
        <span class="px-4 py-2 -mt-8 {{ $isSelesai ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }} rounded-lg font-bold text-sm border">
            Status: {{ $statusText }}
        </span>
            <x-back-button />
        </div>

    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">{{ session('success') }}</div>
    @endif

    <!-- 1. TABEL LS RAMPUNG (SCROLLABLE) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="font-bold text-gray-700">Rincian LS Rampung (Data dari PIC)</h3>
        </div>
        
        <div class="overflow-x-auto p-4">
            <table class="min-w-[1800px] w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b text-center font-bold">
                    <tr>
                        <th rowspan="3" class="px-4 py-2 border-r border-gray-300 w-12">No</th>
                        <th rowspan="3" class="px-4 py-2 border-r border-gray-300 w-32">NIP</th>
                        <th rowspan="3" class="px-4 py-2 border-r border-gray-300 w-48">Nama</th>
                        <th colspan="10" class="px-4 py-2 border-b border-r border-gray-300 bg-blue-50">Rincian Pembayaran</th>
                        <th colspan="2" class="px-4 py-2 border-b border-r border-gray-300 bg-yellow-50">Penginapan</th>
                        <th colspan="2" class="px-4 py-2 border-b border-gray-300 bg-green-50">Pesawat</th>
                    </tr>
                    <tr>
                        <th rowspan="2" class="px-4 py-2 border-r border-b border-gray-300 bg-blue-100 text-blue-900">Jumlah Dibayarkan</th>
                        <th colspan="8" class="px-4 py-2 border-b border-r border-gray-300 bg-white">Rincian Biaya (Rp)</th>
                        <th rowspan="2" class="px-4 py-2 border-r border-b border-gray-300 text-red-600 bg-red-50">SSPB</th>
                        <th rowspan="2" class="px-4 py-2 border-r border-b border-gray-300 bg-white">Nama Hotel</th>
                        <th rowspan="2" class="px-4 py-2 border-r border-b border-gray-300 bg-white">Kota</th>
                        <th rowspan="2" class="px-4 py-2 border-r border-b border-gray-300 bg-white">Kode Tiket</th>
                        <th rowspan="2" class="px-4 py-2 border-b border-gray-300 bg-white">Maskapai</th>
                    </tr>
                    <tr class="bg-white">
                        <th class="px-4 py-2 border-r border-gray-200">Tiket</th>
                        <th class="px-4 py-2 border-r border-gray-200">Uang Harian</th>
                        <th class="px-4 py-2 border-r border-gray-200">Penginapan</th>
                        <th class="px-4 py-2 border-r border-gray-200">Uang Rep.</th>
                        <th class="px-4 py-2 border-r border-gray-200">Transport</th>
                        <th class="px-4 py-2 border-r border-gray-200">Sewa Kend.</th>
                        <th class="px-4 py-2 border-r border-gray-200">Riil</th>
                        <th class="px-4 py-2 border-r border-gray-200">Lainnya</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapData as $index => $data)
                    <tr class="border-b hover:bg-gray-50 text-center text-xs">
                        <td class="px-4 py-3 border-r">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 border-r font-mono">{{ $data['nip'] }}</td>
                        <td class="px-4 py-3 border-r text-left font-bold text-gray-800">{{ $data['nama'] }}</td>
                        <td class="px-4 py-3 border-r font-bold bg-blue-50 text-blue-800 text-sm">
                            {{ number_format($data['biaya']['Total'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Tiket'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Uang Harian'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Penginapan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Uang Representasi'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Transport'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Sewa Kendaraan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Pengeluaran Riil'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r">-</td> 
                        <td class="px-4 py-3 text-red-500 border-r font-bold bg-red-50">{{ number_format($data['biaya']['SSPB'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r text-gray-700">{{ $data['info']['Nama Penginapan'] }}</td>
                        <td class="px-4 py-3 border-r text-gray-700">{{ $data['info']['Kota'] }}</td>
                        <td class="px-4 py-3 border-r text-gray-700">{{ $data['info']['Kode Tiket'] }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $data['info']['Maskapai'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-bold text-gray-900 border-t-2 border-gray-300">
                    <tr class="text-center">
                        <td colspan="3" class="px-4 py-4 text-right border-r">TOTAL KESELURUHAN:</td>
                        <td class="px-4 py-4 bg-yellow-100 text-yellow-800 border-r text-lg border-l">
                            Rp {{ number_format($totalSeluruhnya, 0, ',', '.') }}
                        </td>
                        <td colspan="13" class="bg-gray-50"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- FORM VERIFIKASI -->
    <div class="bg-white rounded-xl shadow-lg border border-yellow-200 p-8 max-w-4xl mx-auto">
        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
            <span class="bg-yellow-100 text-yellow-700 p-2 rounded"><i class="fa-regular fa-credit-card"></i></span>
            Verifikasi & Keputusan PPK
        </h3>

        <!-- FORM SETUJUI -->
        <form action="{{ route('ppk.verifikasi.approve', $perjalanan->id) }}" method="POST" id="form-approve">
            @csrf
            <input type="hidden" name="total_biaya_rampung" value="{{ $totalSeluruhnya }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Input SPM & SP2D -->
                <div class="space-y-4">
                    <h4 class="font-bold text-gray-600 border-b pb-2">Data SPM</h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor SPM</label>
                        
                        <input type="text" name="nomor_spm" 
                               value="{{ old('nomor_spm', $laporanKeuangan->nomor_spm ?? '') }}" 
                               class="w-full border-gray-300 rounded-lg disabled:bg-gray-100 disabled:text-gray-500" 
                               required 
                               @disabled($isSelesai)>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal SPM</label>
                        <input type="date" name="tanggal_spm" 
                               value="{{ old('tanggal_spm', $laporanKeuangan->tanggal_spm ? \Carbon\Carbon::parse($laporanKeuangan->tanggal_spm)->format('Y-m-d') : '') }}"
                               class="w-full border-gray-300 rounded-lg disabled:bg-gray-100 disabled:text-gray-500" 
                               required 
                               @disabled($isSelesai)>
                    </div>
                </div>
                <div class="space-y-4">
                    <h4 class="font-bold text-gray-600 border-b pb-2">Data SP2D</h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor SP2D</label>
                        <input type="text" name="nomor_sp2d" 
                               value="{{ old('nomor_sp2d', $laporanKeuangan->nomor_sp2d ?? '') }}"
                               class="w-full border-gray-300 rounded-lg disabled:bg-gray-100 disabled:text-gray-500" 
                               required 
                               @disabled($isSelesai)>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal SP2D</label>
                        <input type="date" name="tanggal_sp2d" 
                               value="{{ old('tanggal_sp2d', $laporanKeuangan->tanggal_sp2d ? \Carbon\Carbon::parse($laporanKeuangan->tanggal_sp2d)->format('Y-m-d') : '') }}"
                               class="w-full border-gray-300 rounded-lg disabled:bg-gray-100 disabled:text-gray-500" 
                               required 
                               @disabled($isSelesai)>
                    </div>
                </div>
            </div>
        </form>

        <!-- ACTION BUTTONS -->
        <!-- PERBAIKAN: Sembunyikan tombol jika sudah selesai -->
        @if(!$isSelesai)
        <div class="flex justify-between pt-6 border-t gap-4">
            <!-- TOMBOL TOLAK (Buka Modal/Accordion) -->
            <button type="button" onclick="document.getElementById('reject-section').classList.toggle('hidden')" class="bg-red-100 text-red-600 px-6 py-3 rounded-xl font-bold hover:bg-red-200 transition">
                <i class="fa-regular fa-circle-xmark"></i> Tolak / Revisi
            </button>

            <!-- TOMBOL SETUJUI (Submit Form Approve) -->
            <button type="button" onclick="if(confirm('Setujui pembayaran ini?')) document.getElementById('form-approve').submit()" class="bg-green-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-green-700 shadow-lg flex items-center gap-2">
                <i class="fa-regular fa-circle-check"></i>
                Setujui & Selesaikan
            </button>
        </div>

        <!-- FORM TOLAK (Hidden by default) -->
        <div id="reject-section" class="hidden mt-6 p-4 bg-red-50 rounded-xl border border-red-200">
            <form action="{{ route('ppk.verifikasi.reject', $perjalanan->id) }}" method="POST">
                @csrf
                <label class="block text-sm font-bold text-red-700 mb-2">Alasan Penolakan / Catatan Revisi:</label>
                <textarea name="alasan_penolakan" rows="3" class="w-full border-red-300 rounded-lg focus:ring-red-500 mb-3" placeholder="Contoh: Nominal Tiket Pesawat Ketua tidak sesuai bukti..." required></textarea>
                <div class="text-right">
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-red-700">Kirim Revisi ke PIC</button>
                </div>
            </form>
        </div>
        @else
        <div class="bg-green-50 text-green-800 p-4 rounded-xl border border-green-200 text-center font-bold">
            <i class="fa-solid fa-check-circle"></i> Laporan ini sudah disetujui dan dibayar.
        </div>
        @endif
    </div>          
</main>
@endsection