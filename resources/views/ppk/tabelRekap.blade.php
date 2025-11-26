@extends('layouts.appPPK')

@section('title', 'Tabel Rekap Keuangan Perjalanan Dinas')

@section('content')
<main class="item-center max-w-6xl min-h-screen mx-auto px-5 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-gray-700 text-3xl font-bold pb-2 relative">
                Tabel Rekap Keuangan Perjalanan Dinas
                <span class="absolute bottom-0 left-0 w-32 h-1 bg-blue-500 rounded"></span>
            </h1>
            <p class="text-gray-500 mt-2">
                Rekap perjalanan dinas yang sudah memiliki laporan keuangan (SPM/SP2D) dan biaya rampung.
            </p>
        </div>
    </div>

    {{-- (Optional) Filter tahun / bulan --}}
    {{-- 
    <form method="GET" class="mb-4 flex flex-wrap gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Tahun</label>
            <input type="number" name="tahun" value="{{ $filterTahun }}" class="mt-1 border rounded px-2 py-1 w-32">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Bulan</label>
            <input type="number" name="bulan" value="{{ $filterBulan }}" min="1" max="12" class="mt-1 border rounded px-2 py-1 w-24">
        </div>
        <div class="flex items-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Filter</button>
        </div>
    </form>
    --}}

    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-xs uppercase">
                <tr>
                    <th class="px-4 py-2 border">No</th>
                    <th class="px-4 py-2 border">Nama Pegawai</th>
                    <th class="px-4 py-2 border">NIP</th>
                    <th class="px-4 py-2 border">Pangkat/Gol</th>
                    <th class="px-4 py-2 border">Unit Kerja</th>
                    <th class="px-4 py-2 border">Dalam Rangka / Tujuan</th>
                    <th class="px-4 py-2 border">Tgl Berangkat</th>
                    <th class="px-4 py-2 border">Tgl Kembali</th>
                    <th class="px-4 py-2 border">Lama (hari)</th>
                    <th class="px-4 py-2 border">Jumlah Dibayarkan (Rp)</th>
                    <th class="px-4 py-2 border">No SPM</th>
                    <th class="px-4 py-2 border">Tgl SPM</th>
                    <th class="px-4 py-2 border">No SP2D</th>
                    <th class="px-4 py-2 border">Tgl SP2D</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekap as $row)
                    @php
                        $mulai   = $row->tgl_mulai ? \Carbon\Carbon::parse($row->tgl_mulai) : null;
                        $selesai = $row->tgl_selesai ? \Carbon\Carbon::parse($row->tgl_selesai) : null;
                        $lama    = ($mulai && $selesai) ? $mulai->diffInDays($selesai) + 1 : null;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border text-center">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 border">{{ $row->nama_pegawai }}</td>
                        <td class="px-4 py-2 border whitespace-nowrap">{{ $row->nip }}</td>
                        <td class="px-4 py-2 border">{{ $row->pangkat_golongan ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $row->unit_kerja ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $row->tujuan }}</td>
                        <td class="px-4 py-2 border whitespace-nowrap">
                            {{ $mulai ? $mulai->format('d-m-Y') : '-' }}
                        </td>
                        <td class="px-4 py-2 border whitespace-nowrap">
                            {{ $selesai ? $selesai->format('d-m-Y') : '-' }}
                        </td>
                        <td class="px-4 py-2 border text-center">
                            {{ $lama ?? '-' }}
                        </td>
                        <td class="px-4 py-2 border text-right">
                            {{ $row->biaya_rampung ? number_format($row->biaya_rampung, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-4 py-2 border whitespace-nowrap">
                            {{ $row->nomor_spm ?? '-' }}
                        </td>
                        <td class="px-4 py-2 border whitespace-nowrap">
                            {{ $row->tanggal_spm ? \Carbon\Carbon::parse($row->tanggal_spm)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="px-4 py-2 border whitespace-nowrap">
                            {{ $row->nomor_sp2d ?? '-' }}
                        </td>
                        <td class="px-4 py-2 border whitespace-nowrap">
                            {{ $row->tanggal_sp2d ? \Carbon\Carbon::parse($row->tanggal_sp2d)->format('d-m-Y') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="px-4 py-6 text-center text-gray-400">
                            Belum ada data LS rampung / selesai dibayar untuk ditampilkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            @if($rekap->count() > 0)
            <tfoot class="bg-gray-50 font-semibold">
                <tr>
                    <td colspan="9" class="px-4 py-2 border text-right">
                        TOTAL DIBAYARKAN
                    </td>
                    <td class="px-4 py-2 border text-right">
                        {{ number_format($totalDibayarkan, 0, ',', '.') }}
                    </td>
                    <td colspan="4" class="px-4 py-2 border"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</main>s
@endsection
