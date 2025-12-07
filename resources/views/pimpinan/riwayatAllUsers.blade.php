@extends('layouts.app')

@section('title', 'Riwayat')

@section('content')
@php
    // Tab aktif default = pribadi
    $activeTab = request('tab', 'pribadi');
@endphp
<main class="transition-all duration-300 ml-0 sm:ml-[60px]">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
        <!-- Header -->
        <div class="mb-6">
            <x-page-title
                title="Riwayat Perjalanan Dinas" />

           {{-- Info Box --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-3 mb-3 flex items-start gap-3 hover:bg-blue-100 transition-colors">
                <i class="fa-solid fa-circle-info text-blue-600 text-lg mt-0.5 flex-shrink-0"></i>
                <p class="text-blue-800 text-sm">
                    Riwayat perjalanan dinas yang ditampilkan mencakup perjalanan dinas yang telah
                    <strong>berstatus Selesai atau Diselesaikan Manual</strong> dalam 1 tahun terakhir.
                    Tab <strong>Pribadi</strong> menampilkan perjalanan yang melibatkan Anda, sedangkan tab
                    <strong>Pegawai</strong> menampilkan seluruh perjalanan dinas pegawai.
                </p>
            </div>


            <!-- Tabs -->
            <div class="flex gap-3 mb-5">
                <button type="button"
                    data-tab-target="pribadi"
                    class="tab-btn px-6 py-2.5 text-sm sm:text-base font-semibold rounded-lg shadow-sm
                        {{ $activeTab === 'pribadi' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                    Pribadi
                </button>

                <button type="button"
                    data-tab-target="pegawai"
                    class="tab-btn px-6 py-2.5 text-sm sm:text-base font-semibold rounded-lg shadow-sm
                        {{ $activeTab === 'pegawai' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                    Pegawai
                </button>
            </div>

            <!-- Search Bar (opsional, saat ini hanya visual) -->
            <div class="relative mb-6 group">
                <i class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-base group-focus-within:text-blue-500 transition-colors"></i>
                <input
                    type="text"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari nomor surat atau lokasi..."
                    class="w-full pl-11 pr-4 py-3 text-sm sm:text-base border border-gray-300 rounded-xl
                        focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        hover:border-gray-400 transition-all duration-200"
                    readonly
                />
            </div>
        </div>

        <!-- TAB: PRIBADI -->
        <div id="tab-pribadi"
        class="tab-panel space-y-4 {{ $activeTab === 'pribadi' ? '' : 'hidden' }}">
            @forelse ($riwayatPribadi as $riwayat)
                <div class="rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 group"
                    style="background-color: #BCBCBF;">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-1.5">
                            <!-- Info kiri -->
                            <div class="flex-1 space-y-2.5">
                                <h3 class="text-white font-bold text-xl group-hover:translate-x-1 transition-transform duration-300">
                                    {{ $riwayat->nomor_surat }}
                                </h3>

                                <div class="space-y-1.5">
                                    <p class="flex items-center gap-3 text-white text-base group-hover:translate-x-1 transition-transform duration-300 delay-75">
                                        <i class="fa-solid fa-map-marker-alt w-5"></i>
                                        <span>{{ $riwayat->lokasi }}</span>
                                    </p>
                                    <p class="flex items-center gap-3 text-white text-base group-hover:translate-x-1 transition-transform duration-300 delay-100">
                                        <i class="fa-solid fa-calendar-days w-5"></i>
                                        <span>{{ $riwayat->tanggal }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Info kanan -->
                            <div class="flex flex-col items-start sm:items-end gap-3 sm:min-w-[140px]">
                                {{-- di TAB PRIBADI, ganti span status menjadi: --}}
                                @php
                                    $statusBg = 'bg-green-500 text-white';
                                @endphp

                                <span class="px-5 py-2 text-sm font-semibold text-white rounded-full {{ $statusBg }} shadow-sm flex items-center gap-2">
                                    <i class="fa-solid fa-circle text-xs animate-pulse"></i>
                                    {{ $riwayat->status }}
                                </span>

                                <a href="{{ route('pimpinan.detail', $riwayat->id) }}"
                                class="text-gray-200 hover:text-white hover:underline text-sm font-medium transition-all duration-200 flex items-center gap-1.5 group/link">
                                    <span>Lihat Detail</span>
                                    <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform duration-200"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm p-20 text-center text-gray-500 hover:shadow-md transition-shadow duration-300">
                    <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-3"></i>
                    <p class="text-lg">Belum ada riwayat perjalanan dinas pribadi yang selesai.</p>
                </div>
            @endforelse

                    {{-- PAGINATION PRIBADI --}}
        @if ($riwayatPribadi instanceof \Illuminate\Pagination\LengthAwarePaginator && $riwayatPribadi->total() > 0)
            <div class="mt-6 flex justify-center">

                <nav class="inline-flex items-center bg-blue-50 border border-blue-200 rounded-xl shadow-sm overflow-hidden">

                    {{-- Previous --}}
                    @if ($riwayatPribadi->onFirstPage())
                        <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❮</span>
                    @else
                        <a href="{{ $riwayatPribadi->previousPageUrl() }}&tab=pribadi"
                        class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                            ❮
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @for ($page = 1; $page <= $riwayatPribadi->lastPage(); $page++)
                        @if ($page == $riwayatPribadi->currentPage())
                            <span class="px-4 py-2 bg-blue-600 text-white font-semibold">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $riwayatPribadi->url($page) }}&tab=pribadi"
                            class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    {{-- Next --}}
                    @if ($riwayatPribadi->hasMorePages())
                        <a href="{{ $riwayatPribadi->nextPageUrl() }}&tab=pribadi"
                        class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                            ❯
                        </a>
                    @else
                        <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❯</span>
                    @endif

                </nav>

            </div>
            @endif

        </div>

        <!-- TAB: PEGAWAI -->
        <div id="tab-pegawai"
        class="tab-panel space-y-4 {{ $activeTab === 'pegawai' ? '' : 'hidden' }}">

            @forelse ($riwayatPegawai as $riwayat)
                <div class="rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 group"
                    style="background-color: #BCBCBF;">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-1.5">
                            <!-- Info kiri -->
                            <div class="flex-1 space-y-2.5">
                                <h3 class="text-white font-bold text-xl group-hover:translate-x-1 transition-transform duration-300">
                                    {{ $riwayat->nomor_surat }}
                                </h3>

                                <div class="space-y-1.5">
                                    <p class="flex items-center gap-3 text-white text-base group-hover:translate-x-1 transition-transform duration-300 delay-75">
                                        <i class="fa-solid fa-map-marker-alt w-5"></i>
                                        <span>{{ $riwayat->lokasi }}</span>
                                    </p>
                                    <p class="flex items-center gap-3 text-white text-base group-hover:translate-x-1 transition-transform duration-300 delay-100">
                                        <i class="fa-solid fa-calendar-days w-5"></i>
                                        <span>{{ $riwayat->tanggal }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Info kanan -->
                            <div class="flex flex-col items-start sm:items-end gap-3 sm:min-w-[140px]">
                                {{-- di TAB PRIBADI, ganti span status menjadi: --}}
                                @php
                                    $statusBg = 'bg-green-500 text-white';
                                @endphp

                                <span class="px-5 py-2 text-sm font-semibold text-white rounded-full {{ $statusBg }} shadow-sm flex items-center gap-2">
                                    <i class="fa-solid fa-circle text-xs animate-pulse"></i>
                                    {{ $riwayat->status }}
                                </span>


                                <a href="{{ route('pimpinan.detail', $riwayat->id) }}&tab=pegawai"
                                class="text-gray-200 hover:text-white hover:underline text-sm font-medium transition-all duration-200 flex items-center gap-1.5 group/link">
                                    <span>Lihat Detail</span>
                                    <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform duration-200"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-500 hover:shadow-md transition-shadow duration-300">
                    <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-3"></i>
                    <p class="text-lg">Belum ada riwayat perjalanan dinas pegawai yang selesai.</p>
                </div>
            @endforelse

            {{-- PAGINATION PEGAWAI --}}
            @if ($riwayatPegawai instanceof \Illuminate\Pagination\LengthAwarePaginator && $riwayatPegawai->total() > 0)
            <div class="mt-6 flex justify-center">

                <nav class="inline-flex items-center bg-blue-50 border border-blue-200 rounded-xl shadow-sm overflow-hidden">

                    {{-- Previous --}}
                    @if ($riwayatPegawai->onFirstPage())
                        <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❮</span>
                    @else
                        <a href="{{ $riwayatPegawai->previousPageUrl() }}&tab=pegawai"
                        class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                            ❮
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @for ($page = 1; $page <= $riwayatPegawai->lastPage(); $page++)
                        @if ($page == $riwayatPegawai->currentPage())
                            <span class="px-4 py-2 bg-blue-600 text-white font-semibold">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $riwayatPegawai->url($page) }}&tab=pegawai"
                            class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor



                    {{-- Next --}}
                    @if ($riwayatPegawai->hasMorePages())
                        <a href="{{ $riwayatPegawai->nextPageUrl() }}&tab=pegawai"
                        class="px-4 py-2 text-blue-600 hover:bg-blue-100 transition">
                            ❯
                        </a>
                    @else
                        <span class="px-4 py-2 text-blue-300 cursor-not-allowed">❯</span>
                    @endif

                </nav>

            </div>
            @endif
         </div>
    </div>
</main>

{{-- Script kecil untuk toggle tab --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btns  = document.querySelectorAll('.tab-btn');
    const tabs  = {
        pribadi: document.getElementById('tab-pribadi'),
        pegawai: document.getElementById('tab-pegawai'),
    };

    function setActive(tabName) {
        // toggle konten
        Object.keys(tabs).forEach(name => {
            if (name === tabName) {
                tabs[name].classList.remove('hidden');
            } else {
                tabs[name].classList.add('hidden');
            }
        });

        // toggle style button
        btns.forEach(btn => {
            const target = btn.getAttribute('data-tab-target');
            if (target === tabName) {
                btn.classList.remove('bg-gray-200', 'text-gray-600');
                btn.classList.add('bg-blue-600', 'text-white');
            } else {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-600');
            }
        });
    }

    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-tab-target');
            setActive(target);
        });
    });

    // set default
    setActive('{{ $activeTab }}');
});
</script>
@endsection
