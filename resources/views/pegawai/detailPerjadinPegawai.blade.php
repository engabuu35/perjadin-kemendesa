@extends('layouts.app')

@section('title', 'Detail Perjalanan Dinas')

@section('content')
<div class="ml-0 md:ml-20 lg:ml-24">
        <div class="max-w-4xl mx-auto p-6">
 <!-- Main Content -->
        <div class="bg-white rounded-lg shadow-sm p-8">
            <!-- Title with Back Button -->
            <div class="flex items-center justify-between mb-8">
                <div class="inline-block">
                    <h1 class="text-2xl font-bold text-gray-900">Detail Perjalanan Dinas</h1>
                    <!-- Garis horizontal mengikuti panjang judul -->
                    <div class="w-full h-0.5 bg-gradient-to-r from-blue-400 to-blue-200 mt-2"></div>
                </div>
                <button class="bg-blue-600 hover:bg-blue-700 text-white w-12 h-12 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </button>
            </div>

            <!-- Uraian Section -->
            <div class="mb-8">
                <label class="block text-gray-900 font-medium mb-3">Uraian</label>
                <textarea 
                    class="w-full border border-gray-300 rounded-lg p-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                    rows="5" 
                    placeholder="Minimal 100 karakter"
                ></textarea>
            </div>

            <!-- Biaya Perjalanan Dinas Section -->
            <div class="mb-8">
                <h2 class="text-gray-900 font-medium mb-4">Biaya Perjalanan Dinas</h2>
                
                <!-- Row 1 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <input 
                        type="text" 
                        class="border border-gray-300 rounded-lg px-4 py-3 text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        placeholder="Kategori"
                    >
                    <div class="relative">
                        <input 
                            type="text" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                            placeholder="Bukti"
                        >
                        <button class="absolute right-3 top-1/2 -translate-y-1/2 bg-gray-300 hover:bg-gray-400 text-white w-8 h-8 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <input 
                        type="text" 
                        class="border border-gray-300 rounded-lg px-4 py-3 text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        placeholder="Kategori"
                    >
                    <div class="relative">
                        <input 
                            type="text" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                            placeholder="Bukti"
                        >
                        <button class="absolute right-3 top-1/2 -translate-y-1/2 bg-gray-300 hover:bg-gray-400 text-white w-8 h-8 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <input 
                        type="text" 
                        class="border border-gray-300 rounded-lg px-4 py-3 text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        placeholder="Kategori"
                    >
                    <div class="relative">
                        <input 
                            type="text" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                            placeholder="Bukti"
                        >
                        <button class="absolute right-3 top-1/2 -translate-y-1/2 bg-gray-300 hover:bg-gray-400 text-white w-8 h-8 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Tambah Bukti Button -->
                <button class="text-blue-600 hover:text-blue-700 font-medium text-sm flex items-center gap-1">
                    <span class="text-lg">+</span> Tambah Bukti
                </button>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-8 py-3 rounded-lg transition-colors">
                    Kirim
                </button>
            </div>
        </div>

@endsection