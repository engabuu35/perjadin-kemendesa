@extends('layouts.appPPK')

@section('title', 'Tabel Rekap Keuangan Perjalanan Dinas')

@section('content')
<main class="item-center max-w-6xl min-h-screen mx-auto px-5 py-8">
    {{-- HEADER HALAMAN --}}
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

    {{-- 
       DEFINISI BULAN INDONESIA 
       Kita buat variabel array sederhana di sini agar pasti Bahasa Indonesia
       tanpa tergantung settingan locale server/laravel.
    --}}
    @php
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    @endphp

    {{-- KOTAK FILTERING SIMPEL --}}
    <div class="bg-white rounded-2xl shadow border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                <i class="fa-solid fa-filter"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-700">Filter Periode</h2>
        </div>

        <form method="GET" action="{{ route('ppk.tabelrekap') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                
                {{-- DARI BULAN --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Dari Bulan</label>
                    <select name="bulan_mulai" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">-- Pilih --</option>
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ $bulanMulai == $i ? 'selected' : '' }}>
                                {{ $namaBulan[$i] }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- SAMPAI BULAN --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Sampai Bulan</label>
                    <select name="bulan_selesai" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">-- Pilih --</option>
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ $bulanSelesai == $i ? 'selected' : '' }}>
                                {{ $namaBulan[$i] }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- TAHUN --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Tahun</label>
                    <input type="number" name="tahun" value="{{ $tahun ?? date('Y') }}" 
                           class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" 
                           placeholder="Cth: 2025">
                </div>

                {{-- TOMBOL FILTER --}}
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold shadow-md transition">
                        Terapkan
                    </button>
                    <a href="{{ route('ppk.tabelrekap') }}" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- KARTU TABEL + TOMBOL EXPORT --}}
    <div class="bg-white rounded-2xl shadow border border-gray-200 p-4 sm:p-6">
        {{-- WRAPPER TABEL SCROLLABLE --}}
        <div class="max-h-[70vh] overflow-auto rounded-lg border border-gray-100">
            <div class="min-w-[1200px]">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2 border text-center">No</th>
                            <th class="px-4 py-2 border text-left">Nama Pegawai</th>
                            <th class="px-4 py-2 border text-left">NIP</th>
                            <th class="px-4 py-2 border text-left">Pangkat/Gol</th>
                            <th class="px-4 py-2 border text-left">Unit Kerja</th>
                            <th class="px-4 py-2 border text-left">Dalam Rangka / Tujuan</th>
                            <th class="px-4 py-2 border text-left whitespace-nowrap">Tgl Berangkat</th>
                            <th class="px-4 py-2 border text-left whitespace-nowrap">Tgl Kembali</th>
                            <th class="px-4 py-2 border text-center whitespace-nowrap">Lama (hari)</th>
                            <th class="px-4 py-2 border text-right whitespace-nowrap">Jumlah Dibayarkan (Rp)</th>
                            <th class="px-4 py-2 border text-left whitespace-nowrap">No SPM</th>
                            <th class="px-4 py-2 border text-left whitespace-nowrap">Tgl SPM</th>
                            <th class="px-4 py-2 border text-left whitespace-nowrap">No SP2D</th>
                            <th class="px-4 py-2 border text-left whitespace-nowrap">Tgl SP2D</th>
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
                                <td colspan="14" class="px-4 py-6 border text-center text-gray-400">
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
        </div>

        {{-- BARIS BAWAH: TOMBOL EXPORT KANAN BAWAH --}}
        <div class="mt-4 flex justify-end">
            <a href="{{ route('ppk.tabelrekap.export', ['tahun' => $tahun ?? null, 'bulan_mulai' => $bulanMulai ?? null, 'bulan_selesai' => $bulanSelesai ?? null]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700
                      text-white text-sm font-semibold rounded-lg shadow-sm transition">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>
</main>
@endsection