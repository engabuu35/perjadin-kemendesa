@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
{{-- 
  Konten <main>
  class="ml-[80px]" memberi ruang untuk sidebar.
--}}

<main class="transition-all duration-300 ml-0 sm:ml-[60px]">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
        <div class="mb-3 sm:mb-4">
                <!-- Judul -->
                <x-page-title 
                    title="Perjalanan Dinas"
                    subtitle="Daftar penugasan perjalanan dinas Anda." />
                
                <!-- Daftar Kartu Perjalanan -->
                <div class="space-y-6 w-full mx-auto mt-8">
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
            </div>
    </div>        
</main>
@endsection