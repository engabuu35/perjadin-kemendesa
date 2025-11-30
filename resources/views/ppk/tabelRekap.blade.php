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
                Rekap perjalanan dinas yang sudah memiliki laporan keuangan final (SPM/SP2D rampung).
            </p>
        </div>
    </div>

    @php
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    @endphp

    {{-- FILTER PERIODE --}}
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
                    <input type="number"
                           name="tahun"
                           value="{{ $tahun ?? date('Y') }}"
                           class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm"
                           placeholder="Cth: 2025">
                </div>

                {{-- TOMBOL --}}
                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold shadow-md transition">
                        Terapkan
                    </button>
                    <a href="{{ route('ppk.tabelrekap') }}"
                       class="px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- TABEL REKAP --}}
    <div class="bg-white rounded-2xl shadow border border-gray-200 p-4 sm:p-6">
        <div class="max-h-[70vh] overflow-auto rounded-lg border border-gray-100">
            {{-- min-width besar supaya muat semua kolom --}}
            <div class="min-w-[2200px]">
                <table class="w-full text-xs">
                    <thead class="sticky top-0 z-20">
                        {{-- BARIS HEADER 1 (GRUP BESAR) --}}
                        <tr style="background-color:#f4c6f4;" class="font-bold text-center">
                            <th rowspan="3" class="px-3 py-2 border-2 border-gray-300 align-middle">No.</th>
                            <th rowspan="3" class="px-3 py-2 border-2 border-gray-300 align-middle">Nama UKE-1</th>
                            <th rowspan="3" class="px-3 py-2 border-2 border-gray-300 align-middle">Nama UKE-2</th>

                            {{-- Surat Perjalanan Dinas --}}
                            <th colspan="7" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Surat Perjalanan Dinas
                            </th>

                            {{-- Rincian Pembayaran --}}
                            <th colspan="9" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Rincian Pembayaran
                            </th>

                            {{-- Penginapan --}}
                            <th colspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Penginapan
                            </th>

                            {{-- Pesawat --}}
                            <th colspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Pesawat
                            </th>

                            {{-- Dokumen Pembayaran --}}
                            <th colspan="4" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Dokumen Pembayaran
                            </th>
                        </tr>

                        {{-- BARIS HEADER 2 (SUB-GRUP) --}}
                        <tr style="background-color:#f4c6f4;" class="font-bold text-center">
                            {{-- Surat Perjalanan Dinas --}}
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Nama Lengkap Tanpa Gelar
                            </th>
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                NIP
                            </th>
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Pangkat Golongan
                            </th>
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Dalam Rangka
                            </th>
                            <th colspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Tanggal SPD
                            </th>
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Lama Hari
                            </th>

                            {{-- Rincian Pembayaran --}}
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Jumlah Dibayarkan (Rp)
                            </th>
                            <th colspan="8" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Rincian Biaya (Rp)
                            </th>

                            {{-- Penginapan --}}
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Nama Hotel
                            </th>
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Kota
                            </th>

                            {{-- Pesawat --}}
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Kode Tiket
                            </th>
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Maskapai
                            </th>

                            {{-- Dokumen Pembayaran --}}
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                No SPM
                            </th>
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Tgl SPM
                            </th>
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                No SP2D
                            </th>
                            <th rowspan="2" class="px-3 py-2 border-2 border-gray-300 align-middle">
                                Tgl SP2D
                            </th>
                        </tr>

                        {{-- BARIS HEADER 3 (LEAF UNTUK SUB-GRUP) --}}
                        <tr style="background-color:#f4c6f4;" class="font-bold text-center">
                            {{-- Tanggal SPD --}}
                            <th class="px-3 py-2 border-2 border-gray-300">Brgkt</th>
                            <th class="px-3 py-2 border-2 border-gray-300">Kmbl</th>

                            {{-- Rincian Biaya --}}
                            <th class="px-3 py-2 border-2 border-gray-300">Tiket</th>
                            <th class="px-3 py-2 border-2 border-gray-300">Uang Harian</th>
                            <th class="px-3 py-2 border-2 border-gray-300">Penginapan</th>
                            <th class="px-3 py-2 border-2 border-gray-300">Uang Representasi</th>
                            <th class="px-3 py-2 border-2 border-gray-300">Transport</th>
                            <th class="px-3 py-2 border-2 border-gray-300">Sewa Kendaraan</th>
                            <th class="px-3 py-2 border-2 border-gray-300">Pengeluaran Riil</th>
                            <th class="px-3 py-2 border-2 border-gray-300">SSPB</th>
                        </tr>

                        {{-- BARIS HEADER 4 (NOMOR KOLOM 0–26) --}}
                        <tr style="background-color:#f4c6f4;" class="font-bold text-center">
                            @for($i = 0; $i <= 26; $i++)
                                <th class="px-1 py-1 border-2 border-gray-300 text-[10px]">{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>

                    <tbody class="text-[11px]">
                        @forelse ($rekap as $row)
                            @php
                                $mulai   = $row->tgl_mulai ? \Carbon\Carbon::parse($row->tgl_mulai) : null;
                                $selesai = $row->tgl_selesai ? \Carbon\Carbon::parse($row->tgl_selesai) : null;
                                $lama    = ($mulai && $selesai) ? $mulai->diffInDays($selesai) + 1 : null;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                {{-- 1–3: No, UKE-1, UKE-2 --}}
                                <td class="px-3 py-2 border text-center">{{ $loop->iteration }}</td>
                                <td class="px-3 py-2 border text-center whitespace-nowrap">{{ $row->nama_uke1 ?? '-' }}</td>
                                <td class="px-3 py-2 border text-center whitespace-nowrap">{{ $row->nama_uke2 ?? '-' }}</td>

                                {{-- 4–10: SPD --}}
                                <td class="px-3 py-2 border text-center">{{ $row->nama_pegawai }}</td>
                                <td class="px-3 py-2 border text-center whitespace-nowrap">{{ $row->nip }}</td>
                                <td class="px-3 py-2 border text-center whitespace-nowrap">{{ $row->pangkat_golongan ?? '-' }}</td>
                                <td class="px-3 py-2 border text-center">{{ $row->tujuan ?? '-' }}</td>
                                <td class="px-3 py-2 border text-center whitespace-nowrap">
                                    {{ $mulai ? $mulai->format('d-m-Y') : '-' }}
                                </td>
                                <td class="px-3 py-2 border text-center whitespace-nowrap">
                                    {{ $selesai ? $selesai->format('d-m-Y') : '-' }}
                                </td>
                                <td class="px-3 py-2 border text-center">
                                    {{ $lama ?? '-' }}
                                </td>

                                {{-- 11: Jumlah Dibayarkan --}}
                                <td class="px-3 py-2 border text-right">
                                    {{ $row->jumlah_dibayarkan ? number_format($row->jumlah_dibayarkan, 0, ',', '.') : '-' }}
                                </td>

                                {{-- 12–19: Rincian Biaya --}}
                                <td class="px-3 py-2 border text-right">
                                    {{ $row->biaya_tiket ? number_format($row->biaya_tiket, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-3 py-2 border text-right">
                                    {{ $row->biaya_uang_harian ? number_format($row->biaya_uang_harian, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-3 py-2 border text-right">
                                    {{ $row->biaya_penginapan ? number_format($row->biaya_penginapan, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-3 py-2 border text-right">
                                    {{ $row->biaya_uang_representasi ? number_format($row->biaya_uang_representasi, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-3 py-2 border text-right">
                                    {{ $row->biaya_transport ? number_format($row->biaya_transport, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-3 py-2 border text-right">
                                    {{ $row->biaya_sewa_kendaraan ? number_format($row->biaya_sewa_kendaraan, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-3 py-2 border text-right">
                                    {{ $row->biaya_pengeluaran_riil ? number_format($row->biaya_pengeluaran_riil, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-3 py-2 border text-right">
                                    {{ $row->biaya_sspb ? number_format($row->biaya_sspb, 0, ',', '.') : '-' }}
                                </td>

                                {{-- 20–21: Penginapan --}}
                                <td class="px-3 py-2 border text-center">
                                    {{ $row->nama_hotel ?? '-' }}
                                </td>
                                <td class="px-3 py-2 border text-center">
                                    {{ $row->kota_hotel ?? '-' }}
                                </td>

                                {{-- 22–23: Pesawat --}}
                                <td class="px-3 py-2 border text-center">
                                    {{ $row->kode_tiket ?? '-' }}
                                </td>
                                <td class="px-3 py-2 border text-center">
                                    {{ $row->maskapai ?? '-' }}
                                </td>

                                {{-- 24–27: Dokumen Pembayaran --}}
                                <td class="px-3 py-2 border text-center whitespace-nowrap">
                                    {{ $row->nomor_spm ?? '-' }}
                                </td>
                                <td class="px-3 py-2 border text-center whitespace-nowrap">
                                    {{ $row->tanggal_spm ? \Carbon\Carbon::parse($row->tanggal_spm)->format('d-m-Y') : '-' }}
                                </td>
                                <td class="px-3 py-2 border text-center whitespace-nowrap">
                                    {{ $row->nomor_sp2d ?? '-' }}
                                </td>
                                <td class="px-3 py-2 border text-center whitespace-nowrap">
                                    {{ $row->tanggal_sp2d ? \Carbon\Carbon::parse($row->tanggal_sp2d)->format('d-m-Y') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="27" class="px-4 py-6 border text-center text-gray-400">
                                    Belum ada data LS rampung / selesai dibayar untuk ditampilkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if($rekap->count() > 0)
                        <tfoot class="bg-gray-50 font-semibold text-[11px]">
                            <tr>
                                <td colspan="10" class="px-3 py-2 border text-right">
                                    TOTAL JUMLAH DIBAYARKAN
                                </td>
                                <td class="px-3 py-2 border text-right">
                                    {{ number_format($totalDibayarkan, 0, ',', '.') }}
                                </td>
                                <td colspan="16" class="px-3 py-2 border"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- TOMBOL EXPORT --}}
        <div class="mt-4 flex justify-end">
            <a href="{{ route('ppk.tabelrekap.export', [
                    'tahun'         => $tahun ?? null,
                    'bulan_mulai'   => $bulanMulai ?? null,
                    'bulan_selesai' => $bulanSelesai ?? null
                ]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700
                      text-white text-sm font-semibold rounded-lg shadow-sm transition">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>
</main>
@endsection
