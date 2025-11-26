@extends('layouts.appPPK')

@section('content')
@php
    /**
     * Helper mapping kelas badge berdasarkan warna logis.
     * Bisa disesuaikan kembali kalau Anda punya kolom warna di DB.
     */
    if (! function_exists('statusBgClass')) {
        function statusBgClass($color) {
            return match($color) {
                'red' => 'bg-red-100 text-red-800 border-red-200',
                'green' => 'bg-green-100 text-green-800 border-green-200',
                'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                'orange' => 'bg-orange-100 text-orange-800 border-orange-200',
                default => 'bg-gray-100 text-gray-800 border-gray-200',
            };
        }
    }
@endphp

<main class="item-center max-w-6xl min-h-screen mx-auto px-5 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-gray-700 text-3xl font-bold pb-2 relative">
                Pelaporan Pegawai
                <span class="absolute bottom-0 left-0 w-32 h-1 bg-blue-500 rounded"></span>
            </h2>
            <p class="text-gray-500 mt-2">Daftar pelaporan perjalanan dinas — tampilan tile yang terhubung ke database.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($perjalanans as $perjalanan)
            @php
                // Ambil label status dari relasi laporanKeuangan -> status (statuslaporan)
                $lap = $perjalanan->laporanKeuangan ?? null;
                $status_label = $lap && $lap->status ? $lap->status->nama_status : 'Belum Lapor';

                // Pilih warna sederhana berdasarkan label (ubah sesuai kebutuhan)
                $lower = strtolower($status_label);
                if (str_contains($lower, 'selesai') || str_contains($lower, 'terverifikasi') || str_contains($lower, 'lunas')) {
                    $status_color = 'green';
                } elseif (str_contains($lower, 'menunggu') || str_contains($lower, 'pending') || str_contains($lower, 'on progress')) {
                    $status_color = 'orange';
                } elseif (str_contains($lower, 'ditolak') || str_contains($lower, 'rejected') || str_contains($lower, 'batal')) {
                    $status_color = 'red';
                } else {
                    $status_color = 'gray';
                }

                $badge_classes = statusBgClass($status_color);

                // Field uraian/hasil: fallback karena model/DB sedikit berbeda penamaan
                $uraian = $perjalanan->uraian ?? $perjalanan->hasil_perjadin ?? null;

                // Tanggal: tampil apa adanya (sesuai permintaan)
                $tanggal_display = $perjalanan->tanggal_surat
                                    ?? ($perjalanan->tgl_mulai && $perjalanan->tgl_selesai ? ($perjalanan->tgl_mulai . ' - ' . $perjalanan->tgl_selesai) : ($perjalanan->tgl_mulai ?? $perjalanan->tgl_selesai ?? ''));
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-md transition overflow-hidden flex flex-col">
                <div class="p-6 flex-1">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $badge_classes }}">
                            {{ $perjalanan->nomor_surat }}
                        </span>

                        <span class="text-sm font-semibold {{ $status_color == 'green' ? 'text-green-600' : ($status_color == 'red' ? 'text-red-600' : ($status_color == 'orange' ? 'text-orange-600' : 'text-gray-600')) }}">
                            {{ $status_label }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2">
                        {{ $perjalanan->tujuan ?? $perjalanan->lokasi ?? '-' }}
                    </h3>

                    <p class="flex items-center gap-2 text-gray-700 text-base mb-2">
                        <i class="fa-solid fa-calendar-days w-4 text-center text-gray-400"></i>
                        <span>
                            {{ $perjalanan->tgl_mulai ? \Carbon\Carbon::parse($perjalanan->tgl_mulai)->format('d M Y') : '-' }}
                            @if($perjalanan->tgl_mulai && $perjalanan->tgl_selesai)
                                - {{ \Carbon\Carbon::parse($perjalanan->tgl_selesai)->format('d M Y') }}
                            @elseif($perjalanan->tgl_selesai)
                                {{ \Carbon\Carbon::parse($perjalanan->tgl_selesai)->format('d M Y') }}
                            @endif
                        </span>
                    </p>

                    @if($lap && !is_null($lap->biaya_rampung))
                        <div class="text-sm text-gray-700 mb-2">
                            Biaya rampung: Rp {{ number_format($lap->biaya_rampung, 0, ',', '.') }}
                        </div>
                    @endif
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                    <a href="{{ route('ppk.detailPelaporan', $perjalanan->id) }}"
                       class="block w-full text-center bg-white border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white font-semibold py-2 rounded-lg transition">
                        Lihat Detail
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-400 text-lg">Belum ada laporan yang masuk.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination (pastikan controller mengirim paginate) --}}
    @if(isset($perjalanans) && method_exists($perjalanans, 'links'))
        <div class="mt-6">
            {{ $perjalanans->links() }}
        </div>
    @endif
</main>
@endsection
