@extends('layouts.appPIC')

@section('title', 'LS Rampung PIC')

@section('content')
<main class="item-center max-w-7xl min-h-screen mx-auto px-5 py-8 ">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-gray-700 text-2xl font-bold pb-3 relative">
                Tabel LS Rampung 
                <span class="absolute bottom-0 left-0 w-48 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
            </h2>
            <p class="text-gray-500 text-sm">
                Tabel Rincian Pembayaran Perjalanan Dinas yang Dikirimkan oleh Pegawai 
            </p>
        </div>
    </div>

    <!-- [BAGIAN BARU] Dropdown Pilih Pegawai -->
    <div class="bg-white p-4 rounded-xl shadow-sm border mb-6">
        <form action="{{ route('pic.lsrampung') }}" method="GET" class="flex items-center gap-4">
            <label class="text-sm font-bold text-gray-700">Pilih Pegawai (Pelapor):</label>
            <select name="laporan_id" onchange="this.form.submit()" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 w-1/2">
                <option value="">-- Pilih Pegawai / NIP --</option>
                @foreach($daftarLaporan as $lapor)
                    <option value="{{ $lapor->id }}" {{ request('laporan_id') == $lapor->id ? 'selected' : '' }}>
                        {{ $lapor->user->nama ?? 'Nama Tidak Ditemukan' }} - NIP: {{ $lapor->user->nip ?? '-' }} ({{ \Carbon\Carbon::parse($lapor->created_at)->format('d M Y') }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border bg-white shadow-sm">
        <table class="min-w-[2400px] text-xs">
            <thead class="bg-gray-100 text-center font-medium">

                <!-- HEADER LEVEL 1 -->
                <tr class="text-[12px] font-bold leading-tight">
                    <th rowspan="3" class="border px-4 py-2">No.</th>
                    <th rowspan="3" class="border px-4 py-2">NIP</th>
                    <th rowspan="3" class="border px-4 py-2">Nama</th>

                    <!-- Rincian Peembayaran -->
                    <th colspan="9" class="border px-4 py-2">Rincian Pembayaran</th>

                    <!-- Penginapan & Pesawat -->
                    <th colspan="2" class="border px-4 py-2">Penginapan</th>
                    <th colspan="2" class="border px-4 py-2">Pesawat</th>
                    <th rowspan="3" class="border px-4 py-2">Geotagging</th>
                </tr>

                <!-- HEADER LEVEL 2 -->
                <tr class="text-[11px] font-bold leading-tight">
                    <!-- Rincian Biaya Sub-Header -->
                    <th rowspan="2" class="border px-4 py-2">Jumlah Dibayarkan</th>
                    <th colspan="8" class="border px-4 py-2">Rincian Biaya (Rp)</th>
                    <th rowspan="2" class="border px-4 py-2">Nama Hotel</th>
                    <th rowspan="2" class="border px-4 py-2">Kota</th>
                    <th rowspan="2" class="border px-4 py-2">Kode Tiket</th>
                    <th rowspan="2" class="border px-4 py-2">Maskapai</th>
                </tr> 
                
                <!-- HEADER LEVEL 3 -->   
                <tr class="text-[11px] font-semibold leading-tight">
                    <th rowspan="1" class="border px-4 py-2">Tiket</th>
                    <th rowspan="1" class="border px-4 py-2">Uang Harian</th>
                    <th rowspan="1" class="border px-4 py-2">Penginapan</th>
                    <th rowspan="1" class="border px-4 py-2">Uang Representasi</th>
                    <th rowspan="1" class="border px-4 py-2">Transport</th> <!-- Asumsi kategori Transport masuk sini -->
                    <th rowspan="1" class="border px-4 py-2">Sewa Kendaraan</th>
                    <th rowspan="1" class="border px-4 py-2">Pengeluaran Riil</th>
                    <th rowspan="1" class="border px-4 py-2">SSPB</th>
                </tr>

                <!-- Level 4 (Nomor Kolom) -->
                <tr class="text-[11px] font-semibold leading-tight">
                    <th class="border px-4 py-1">1</th>
                    <th class="border px-4 py-1">2</th>
                    <th class="border px-4 py-1">3</th>
                    <th class="border px-4 py-1">0 (Total)</th>
                    <th class="border px-4 py-1">4</th>
                    <th class="border px-4 py-1">5</th>
                    <th class="border px-4 py-1">6</th>
                    <th class="border px-4 py-1">7</th>
                    <th class="border px-4 py-1">8</th>
                    <th class="border px-4 py-1">9</th>
                    <th class="border px-4 py-1">10</th>
                    <th class="border px-4 py-1">11</th>
                    <th class="border px-4 py-1">12</th>
                    <th class="border px-4 py-1">13</th>
                    <th class="border px-4 py-1">14</th>
                    <th class="border px-4 py-1">15</th>
                    <th class="border px-4 py-1">16</th>
                    </th>
            </thead>

            <tbody class="divide-y divide-gray-200">

                @if($selectedLaporan)
                    <tr class="hover:bg-gray-50 text-center">
                        <!-- 1. No -->
                        <td class="border px-4 py-2">1</td>
                        
                        <!-- 2. NIP -->
                        <td class="border px-4 py-2">{{ $selectedLaporan->user->nip ?? '-' }}</td>
                        
                        <!-- 3. Nama -->
                        <td class="border px-4 py-2 text-left">{{ $selectedLaporan->user->nama ?? '-' }}</td>

                        <!-- 0. Jumlah Dibayarkan (Total) -->
                        <td class="border px-4 py-2 font-bold bg-blue-50">
                            {{ number_format($rekapBiaya['Total'], 0, ',', '.') }}
                        </td>

                        <!-- 4. Tiket -->
                        <td class="border px-4 py-2">{{ number_format($rekapBiaya['Tiket'], 0, ',', '.') }}</td>
                        
                        <!-- 5. Uang Harian -->
                        <td class="border px-4 py-2">{{ number_format($rekapBiaya['Uang Harian'], 0, ',', '.') }}</td>
                        
                        <!-- 6. Penginapan -->
                        <td class="border px-4 py-2">{{ number_format($rekapBiaya['Penginapan'], 0, ',', '.') }}</td>
                        
                        <!-- 7. Uang Representasi -->
                        <td class="border px-4 py-2">{{ number_format($rekapBiaya['Uang Representasi'], 0, ',', '.') }}</td>
                        
                        <!-- 8. Transport -->
                        <td class="border px-4 py-2">{{ number_format($rekapBiaya['Transport'] ?? 0, 0, ',', '.') }}</td>
                        
                        <!-- 9. Sewa Kendaraan -->
                        <td class="border px-4 py-2">{{ number_format($rekapBiaya['Sewa Kendaraan'], 0, ',', '.') }}</td>
                        
                        <!-- 10. Pengeluaran Riil -->
                        <td class="border px-4 py-2">{{ number_format($rekapBiaya['Pengeluaran Riil'], 0, ',', '.') }}</td>
                        
                        <!-- 11. SSPB -->
                        <td class="border px-4 py-2 text-red-600">{{ number_format($rekapBiaya['SSPB'], 0, ',', '.') }}</td>

                        <!-- Kolom Detail Tambahan (Diambil dari data perjalanan dinas / bukti tiket pertama) -->
                        <!-- 12. Nama Hotel -->
                        <td class="border px-4 py-2">-</td> 
                        
                        <!-- 13. Kota -->
                        <td class="border px-4 py-2">{{ $selectedLaporan->perjadin->tujuan ?? '-' }}</td>
                        
                        <!-- 14. Kode Tiket -->
                        <td class="border px-4 py-2">-</td>
                        
                        <!-- 15. Maskapai -->
                        <td class="border px-4 py-2">-</td>

                         <!-- 16. Geotagging (Status) -->
                         <td class="border px-4 py-2">
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-[10px]">Terekam</span>
                         </td>
                    </tr>
                @else
                    <tr>
                        <td colspan="17" class="text-center py-10 text-gray-500 font-medium">
                            Silakan pilih pegawai dari dropdown di atas untuk melihat rincian LS Rampung.
                        </td>
                    </tr>
                @endif

            </tbody>
        </table>
    </div>
</main>
@endsection