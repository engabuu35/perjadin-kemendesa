@extends('layouts.app')

@section('content')

{{-- Konten main dengan margin kiri untuk sidebar dan centered --}}
<main class="item-center max-w-6xl min-h-screen mx-auto px-5 py-8">
        
        <!-- Header dengan Info -->
        <x-page-title title="Riwayat Perjalanan Dinas" />
            <!-- Info Box -->
            <x-info-box>
             Riwayat perjalanan dinas yang ditampilkan hanya untuk 1 tahun terakhir.
            </x-info-box>

            <!-- Search Bar -->
            <x-search-bar 
              id="searchInput"
               placeholder="Cari nomor surat atau lokasi..."
               />


         <!-- Daftar Kartu Riwayat -->
            <div class="space-y-4">

                @forelse ($riwayat_list as $riwayat)
                    <x-riwayat-card :riwayat="$riwayat" />
                @empty
                    <x-riwayat-empty />
    @endforelse

</div>

</main>

@endsection