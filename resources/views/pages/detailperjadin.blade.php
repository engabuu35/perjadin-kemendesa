@extends('layouts.app')

{{-- 
  CSS & JS Peta sekarang dimuat langsung di sini, 
  bukan lagi menggunakan @push.
--}}

@section('content')

{{-- 1. TAMBAHKAN CSRF TOKEN SECARA MANUAL --}}
{{-- JavaScript akan mencari tag ini untuk keamanan --}}
<meta name="csrf-token" content="{{ csrf_token() }}">


{{-- 2. CSS LEAFLET (PETA) DIMUAT LANGSUNG --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
<style>
    /* Memberi tinggi pada #map */
    #map {
        height: 20rem; /* 320px */
    }
    
    /* Perbaikan styling input file bawaan Tailwind */
    input[type="file"] {
        @apply w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-lg file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100;
    }
</style>


{{-- 
  2. PERBAIKAN LAYOUT: 
     Wrapper ini memberi margin agar konten tidak 
     tertimpa Navbar (mt-[90px]) dan Sidebar (ml-[80px]).
--}}
<div class="ml-[80px] p-6">

    <!-- Header Halaman -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Detail Perjalanan Dinas
        </h1>
        
        <a href="{{ url()->previous() }}" class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full shadow-md transition-transform transform hover:scale-105 hover:bg-blue-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
    </div>

    <!-- Card Utama -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl">
        
        <form action="{{ route('perjalanan.storeLaporan', $perjalanan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Grid untuk Nomor Surat & Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Nomor Surat Tugas -->
                <div>
                    <label for="nomor_surat" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Surat Tugas
                    </label>
                    <input type="text" id="nomor_surat" name="nomor_surat"
                           value="{{ $perjalanan->nomor_surat ?? 'N/A' }}"
                           class="w-full bg-gray-100 border-gray-300 rounded-lg shadow-sm focus:ring-0 focus:border-gray-300"
                           readonly disabled>
                </div>

                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal
                    </label>
                    <input type="text" id="tanggal" name="tanggal"
                           value="{{ $perjalanan->tanggal ?? 'N/A' }}"
                           class="w-full bg-gray-100 border-gray-300 rounded-lg shadow-sm focus:ring-0 focus:border-gray-300"
                           readonly disabled>
                </div>
            </div>

            <!-- Peta (dari geotag.blade.php) -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Peta
                </label>
                {{-- Ini adalah div untuk Leaflet Map --}}
                <div id="map" class="w-full bg-gray-100 border border-gray-300 rounded-lg shadow-sm z-0"></div>
                {{-- Ini adalah paragraf status --}}
                <p id="geotag-status" class="mt-2 text-sm text-gray-600">Tekan tombol di bawah untuk menandai kehadiran Anda di lokasi.</p>
            </div>


            <!-- Tombol Tandai Kehadiran (sekarang menjadi tombol Geotag) -->
            <div class="flex justify-center mb-10">
                <button type="button" id="geotag-btn"
                        data-id="{{ $perjalanan->id }}"
                        data-url="{{ route('perjalanan.hadir', $perjalanan->id) }}"
                        class="bg-blue-600 text-white font-bold py-3 px-10 rounded-lg shadow-md hover:bg-blue-700 transition duration-300 ease-in-out transform hover:-translate-y-0.5">
                    Tandai Kehadiran (Tag Lokasi)
                </button>
            </div>

            <hr class="border-gray-200 border-dashed my-8">

            <!-- Uraian -->
            <div class="mb-8">
                <label for="uraian" class="block text-sm font-medium text-gray-700 mb-2">
                    Uraian
                </label>
                <textarea id="uraian" name="uraian" rows="5"
                          placeholder="Minimal 100 karakter"
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            
            <!-- Biaya Perjalanan Dinas -->
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                Biaya Perjalanan Dinas
            </h2>

            {{-- STYLING DIPERBARUI --}}
            <div class="space-y-4 mb-6" id="biaya-list">
                
                <!-- Baris Biaya 1 -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                    <div>
                        <label for="kategori_1" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <input type="text" id="kategori_1" name="kategori[]" placeholder="Contoh: Transportasi"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="bukti_1" class="block text-sm font-medium text-gray-700 mb-1">
                            Bukti
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </label>
                        <input type="file" id="bukti_1" name="bukti[]">
                    </div>
                </div>

                <!-- Baris Biaya 2 -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                    <div>
                        <label for="kategori_2" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <input type="text" id="kategori_2" name="kategori[]" placeholder="Contoh: Akomodasi"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="bukti_2" class="block text-sm font-medium text-gray-700 mb-1">Bukti</label>
                        <input type="file" id="bukti_2" name="bukti[]">
                    </div>
                </div>

                <!-- Baris Biaya 3 -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                    <div>
                        <label for="kategori_3" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <input type="text" id="kategori_3" name="kategori[]" placeholder="Contoh: Uang Harian"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="bukti_3" class="block text-sm font-medium text-gray-700 mb-1">Bukti</label>
                        <input type="file" id="bukti_3" name="bukti[]">
                    </div>
                </div>

            </div>

            <!-- Tombol Tambah Bukti -->
            <div class="mb-10">
                <button type="button" id="btn-tambah-bukti"
                        class="w-full border-2 border-blue-600 border-dashed text-blue-600 font-bold py-3 px-6 rounded-lg hover:bg-blue-50 transition duration-300 ease-in-out">
                    + Tambah Bukti
                </button>
            </div>


            <!-- Tombol Kirim -->
            <div class="flex justify-end">
                <button type="submit"
                        class="bg-blue-600 text-white font-bold py-3 px-12 rounded-lg shadow-md hover:bg-blue-700 transition duration-300 ease-in-out transform hover:-translate-y-0.5">
                    Kirim
                </button>
            </div>

        </form>
    </div>
</div> {{-- Penutup wrapper margin --}}
@endsection


{{-- 
  3. SEMUA SCRIPT DIMUAT LANGSUNG DI BAWAH @section 
     Bukan di @push('scripts')
--}}

{{-- Script Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>

{{-- Script Geotagging & Tambah Bukti --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. LOGIKA GEOTAGGING ---
    
    // Inisialisasi Peta Leaflet
    // Set view default ke Indonesia
    const map = L.map('map').setView([-2.548926, 118.0148634], 5); 
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const geotagBtn = document.getElementById('geotag-btn');
    const statusEl = document.getElementById('geotag-status');
    
    // --- PERBAIKAN: Ambil CSRF Token dari meta tag yang baru kita tambahkan ---
    const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
    let csrfToken = null;
    if (csrfTokenEl) {
        csrfToken = csrfTokenEl.getAttribute('content');
    } else {
        console.error('CSRF Token meta tag not found!');
        statusEl.textContent = 'Error: CSRF Token tidak ditemukan. Hubungi admin.';
        statusEl.classList.add('text-red-500');
    }

    let currentMarker = null; // Untuk menyimpan marker saat ini

    if (geotagBtn && csrfToken) { // Hanya jalankan jika tombol DAN token ada
        geotagBtn.addEventListener('click', function() {
            statusEl.textContent = 'Mendeteksi lokasi Anda...';
            statusEl.classList.remove('text-red-500', 'text-green-500');
            statusEl.classList.add('text-gray-600');
            geotagBtn.disabled = true;
            geotagBtn.textContent = 'Mencari...';

            if (!navigator.geolocation) {
                statusEl.textContent = 'Geolocation tidak didukung browser Anda.';
                statusEl.classList.add('text-red-500');
                geotagBtn.disabled = false;
                geotagBtn.textContent = 'Tandai Kehadiran (Tag Lokasi)';
                return;
            }
            
            navigator.geolocation.getCurrentPosition(handleSuccess, handleError, {
                enableHighAccuracy: true,
                timeout: 10000, // 10 detik timeout
                maximumAge: 0 // Paksa ambil lokasi baru
            });
        });
    }

    function handleSuccess(pos) {
        const { latitude, longitude } = pos.coords;
        statusEl.textContent = 'Lokasi ditemukan. Mengirim ke server...';

        const url = geotagBtn.dataset.url;

        // Kirim request AJAX (Fetch)
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken, // Token diambil dari variabel
                'Accept': 'application/json'
            },
            body: JSON.stringify({ latitude, longitude }) // Kirim data lokasi
        })
        .then(response => {
            // --- PERBAIKAN: Cek jika response-nya error (misal: 419 CSRF mismatch) ---
            if (!response.ok) {
                // response.statusText sering kosong, jadi kita beri pesan umum
                let errorMsg = `Gagal terhubung ke server (Error: ${response.status}).`;
                if (response.status === 419) {
                    errorMsg = 'Sesi Anda telah habis. Silakan refresh halaman dan coba lagi.';
                }
                throw new Error(errorMsg);
            }
            return response.json(); // Lanjut jika OK
        })
        .then(data => {
            if(data.status === 'success') {
                statusEl.textContent = data.message;
                statusEl.classList.add('text-green-500');
                
                // Matikan tombol
                geotagBtn.disabled = true;
                geotagBtn.textContent = 'Kehadiran Dicatat';
                geotagBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                geotagBtn.classList.add('bg-green-500', 'cursor-not-allowed');

                // Tambahkan marker ke peta
                if (currentMarker) {
                    map.removeLayer(currentMarker);
                }
                currentMarker = L.marker([latitude, longitude]).addTo(map)
                    .bindPopup(`<b>Lokasi Anda</b><br>Kehadiran dicatat.`).openPopup();
                
                // Fokus peta ke lokasi
                map.setView([latitude, longitude], 16);
                
            } else {
                // Jika server merespon 'status' != 'success'
                handleFetchError(data.message || 'Gagal menyimpan lokasi.');
            }
        })
        .catch(error => {
            // Tangkap semua error (network error, atau error dari throw di atas)
            console.error('Error:', error);
            handleFetchError(error.message || 'Terjadi kesalahan jaringan.');
        });
    }

    function handleError(e) {
        // --- PERBAIKAN: Pesan error lebih jelas ---
        let errorMsg = 'Gagal mendapatkan lokasi: ';
        switch(e.code) {
            case e.PERMISSION_DENIED:
                errorMsg += 'Anda menolak izin lokasi.';
                break;
            case e.POSITION_UNAVAILABLE:
                errorMsg += 'Informasi lokasi tidak tersedia.';
                break;
            case e.TIMEOUT:
                errorMsg += 'Waktu mencari lokasi habis (timeout).';
                break;
            default:
                errorMsg += 'Terjadi error tidak diketahui.';
        }
        
        statusEl.textContent = errorMsg;
        statusEl.classList.add('text-red-500');
        geotagBtn.disabled = false;
        geotagBtn.textContent = 'Coba Lagi Tandai Kehadiran';
    }

    function handleFetchError(message) {
        statusEl.textContent = message;
        statusEl.classList.add('text-red-500');
        geotagBtn.disabled = false;
        geotagBtn.textContent = 'Coba Lagi Tandai Kehadiran';
    }

    // --- 2. LOGIKA TAMBAH BUKTI ---
    
    const addBuktiBtn = document.getElementById('btn-tambah-bukti');
    const biayaList = document.getElementById('biaya-list');
    let buktiCounter = 3; // Mulai dari 3 karena sudah ada 3

    if (addBuktiBtn && biayaList) {
        addBuktiBtn.addEventListener('click', function() {
            buktiCounter++;
            
            const newRow = document.createElement('div');
            newRow.className = 'grid grid-cols-1 sm:grid-cols-2 gap-4 items-start';
            
            // HTML ini di-update untuk mencocokkan styling baru
            newRow.innerHTML = `
                <div>
                    <label for="kategori_${buktiCounter}" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <input type="text" id="kategori_${buktiCounter}" name="kategori[]" placeholder="Kategori"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="bukti_${buktiCounter}" class="block text-sm font-medium text-gray-700 mb-1">Bukti</label>
                    <input type="file" id="bukti_${buktiCounter}" name="bukti[]">
                </div>
            `;
            
            biayaList.appendChild(newRow);
        });
    }

});
</script>