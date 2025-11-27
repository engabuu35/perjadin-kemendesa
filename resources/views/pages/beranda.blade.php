@extends('layouts.app')

@section('content')
{{-- 
  Konten <main>
  class="ml-[80px]" memberi ruang untuk sidebar.
--}}
<main class="item-center max-w-6xl min-h-screen mx-auto px-5 py-8">
    
    <!-- Judul -->
   <x-page-title 
    title="Perjalanan Dinas"
    subtitle="Daftar penugasan perjalanan dinas Anda." />

    <!-- Daftar Kartu Perjalanan -->
     <div class="space-y-6">
        @forelse ($perjalanan_list as $perjalanan)
        <x-perjalanan-card :perjalanan="$perjalanan" />
    @empty
        <x-empty-state 
            icon="fa-suitcase-rolling"
            title="Belum ada Perjalanan Dinas"
            message="Saat ini Anda tidak memiliki jadwal perjalanan dinas aktif."
        />
        @endforelse

    </div>

</main>
@endsection