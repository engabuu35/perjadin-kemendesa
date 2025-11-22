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
                    <th rowspan="1" class="border px-4 py-2">Transport</th>
                    <th rowspan="1" class="border px-4 py-2">Sewa Kendaraan</th>
                    <th rowspan="1" class="border px-4 py-2">Pengeluaran Riil</th>
                    <th rowspan="1" class="border px-4 py-2">SSPB</th>
                </tr>

                <!-- Level 4 -->
                <tr class="text-[11px] font-semibold leading-tight">
                    <th class="border px-4 py-1">1</th>
                    <th class="border px-4 py-1">2</th>
                    <th class="border px-4 py-1">3</th>
                    <th class="border px-4 py-1">0</th>
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

                <!-- 1 BARIS DATA CONTOH, ini dibuat dropdown aja sepertinya -->
                <tr class="hover:bg-gray-50">
                    <td class="border px-4 py-2">1</td>
                    <td class="border px-4 py-2">199909092029092009</td>
                    <td class="border px-4 py-2">Paijo Pegawai</td>

                    <td class="border px-4 py-2">4.500.000</td>

                    <td class="border px-4 py-2">1.200.000</td>
                    <td class="border px-4 py-2">900.000</td>
                    <td class="border px-4 py-2">1.800.000</td>
                    <td class="border px-4 py-2">300.000</td>
                    <td class="border px-4 py-2">200.000</td>
                    <td class="border px-4 py-2">0</td>
                    <td class="border px-4 py-2">100.000</td>
                    <td class="border px-4 py-2">0</td>

                    <td class="border px-4 py-2">Hotel Surya</td>
                    <td class="border px-4 py-2">Surabaya</td>
                    <td class="border px-4 py-2">TK2025A</td>
                    <td class="border px-4 py-2">Garuda Indonesia</td>
                </tr>

            </tbody>
        </table>
    </div>


</main>
@endsection