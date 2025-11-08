@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Back Button -->
    <div class="flex justify-end mb-8">
        <a href="{{ url()->previous() }}" class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
    </div>

    <!-- Page Title -->
    <h1 class="text-5xl font-bold text-gray-800 mb-10">Profile</h1>

    <!-- Profile Card -->
    <div class="bg-white rounded-3xl shadow-2xl p-10 mb-10">
        <div class="flex flex-col lg:flex-row gap-12 items-start">
            <!-- Profile Picture Section -->
            <div class="flex flex-col items-center lg:items-start flex-shrink-0">
                <div class="w-48 h-48 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-6 overflow-hidden shadow-lg border-4 border-white">
                    <i class="fas fa-user text-8xl text-gray-400"></i>
                </div>
                <button class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-12 py-3 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-camera mr-2"></i>PILIH FOTO
                </button>
            </div>

            <!-- Profile Information -->
            <div class="flex-1 space-y-5 w-full">
                <!-- NIP -->
                <div class="flex flex-col sm:flex-row rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 font-bold flex items-center justify-center sm:w-48 text-center text-lg">
                        <i class="fas fa-id-card mr-2"></i>NIP
                    </div>
                    <div class="bg-blue-50 px-8 py-4 flex-1 flex items-center text-gray-700 font-medium text-lg">
                        {{ Auth::user()->nip ?? '22231299' }}
                    </div>
                </div>

                <!-- Email -->
                <div class="flex flex-col sm:flex-row rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 font-bold flex items-center justify-center sm:w-48 text-center text-lg">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </div>
                    <div class="bg-blue-50 px-8 py-4 flex-1 flex items-center break-all text-gray-700 font-medium text-lg">
                        {{ Auth::user()->email ?? '22312994@stis.ac.id' }}
                    </div>
                </div>

                <!-- Nomor HP -->
                <div class="flex flex-col sm:flex-row rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 font-bold flex items-center justify-center sm:w-48 text-center text-lg">
                        <i class="fas fa-phone mr-2"></i>Nomor HP
                    </div>
                    <div class="bg-blue-50 px-8 py-4 flex-1 flex items-center text-gray-700 font-medium text-lg">
                        {{ Auth::user()->no_hp ?? '08xxxxxxxx' }}
                    </div>
                </div>

                <!-- Nama Lengkap -->
                <div class="flex flex-col sm:flex-row rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 font-bold flex items-center justify-center sm:w-48 text-center text-lg">
                        <i class="fas fa-user-circle mr-2"></i>Nama Lengkap
                    </div>
                    <div class="bg-blue-50 px-8 py-4 flex-1 flex items-center break-words text-gray-700 font-medium text-lg">
                        {{ Auth::user()->nama_lengkap ?? 'Muhammad Saifullah Shalahuddin Al-Ayyubi Nasruddin Raharjo' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Button -->
    <div class="flex justify-end">
        <button class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-10 py-4 rounded-xl font-bold text-lg flex items-center gap-3 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <i class="fas fa-pen text-lg"></i>
            Edit Profile
        </button>
    </div>
</div>
@endsection