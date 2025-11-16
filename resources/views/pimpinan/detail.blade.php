@extends('layouts.appPimpinan')

@section('title', 'Detail Perjalanan Dinas')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('pimpinan.monitoring') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Dashboard
        </a>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $perjadin->nomor_surat }}</h1>
            <span class="bg-orange-400 text-white text-sm font-semibold px-3 py-1 rounded-full">
                {{ $perjadin->nama_status }}
            </span>
        </div>

        <!-- Detail Content akan ditambahkan di sini -->
    </div>
</div>
@endsection