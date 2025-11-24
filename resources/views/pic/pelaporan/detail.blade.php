@extends('layouts.appPIC')

@section('title', 'Detail Keuangan')

@section('content')
<main class="max-w-[95%] mx-auto px-4 py-8">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Rincian Biaya Perjalanan</h2>
            <p class="text-gray-600 text-sm mt-1">{{ $perjalanan->nomor_surat }} | {{ $perjalanan->tujuan }}</p>
        </div>
        <div class="flex gap-3">
            <span class="px-4 py-2 bg-gray-100 rounded-lg font-bold text-gray-600 text-sm border">
                Status: {{ $statusText }}
            </span>
            <a href="{{ route('pic.pelaporan.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm hover:bg-gray-700">Kembali</a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b text-center">
                    <tr>
                        <th rowspan="2" class="px-4 py-3 border-r">No</th>
                        <th rowspan="2" class="px-4 py-3 border-r">NIP</th>
                        <th rowspan="2" class="px-4 py-3 border-r">Nama</th>
                        <th colspan="9" class="px-4 py-2 border-b">Rincian Biaya (Rp)</th>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 border-r">Total Dibayar</th>
                        <th class="px-4 py-2 border-r">Tiket</th>
                        <th class="px-4 py-2 border-r">Uang Harian</th>
                        <th class="px-4 py-2 border-r">Penginapan</th>
                        <th class="px-4 py-2 border-r">Transport</th>
                        <th class="px-4 py-2 border-r">Sewa Kend.</th>
                        <th class="px-4 py-2 border-r">Uang Rep.</th>
                        <th class="px-4 py-2 border-r">Riil</th>
                        <th class="px-4 py-2 text-red-600">SSPB</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapData as $index => $data)
                    <tr class="border-b hover:bg-gray-50 text-center">
                        <td class="px-4 py-3 border-r">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 border-r font-mono text-xs">{{ $data['nip'] }}</td>
                        <td class="px-4 py-3 border-r text-left font-medium text-gray-800">{{ $data['nama'] }}</td>
                        
                        <!-- Total (Highlight) -->
                        <td class="px-4 py-3 border-r font-bold bg-blue-50 text-blue-700">
                            {{ number_format($data['biaya']['Total'], 0, ',', '.') }}
                        </td>

                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Tiket'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Uang Harian'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Penginapan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Transport'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Sewa Kendaraan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Uang Representasi'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Pengeluaran Riil'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-red-500 font-medium">{{ number_format($data['biaya']['SSPB'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection