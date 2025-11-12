@extends('layouts.app')

@section('title', 'FAQ')

@section('content')
<div class="min-h-screen p-6">
    <div class="max-w-4xl mx-auto bg-gradient-to-br from-blue-50 to-indigo-50 rounded-3xl p-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-md p-8 mb-6">
            <div class="flex items-center justify-between mb-2">
                <h1 class="text-3xl font-bold text-gray-800">FAQ SIPERDIN</h1>
                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #0F55C9;">
                    <span class="text-white text-2xl font-bold">?</span>
                </div>
            </div>
            <p class="text-gray-600">Pertanyaan yang Sering Diajukan</p>
        </div>

        <!-- FAQ List -->
        <div class="space-y-4" id="faq-container">
            <!-- FAQ Item 1 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <button onclick="toggleFAQ(1)" class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-blue-50 transition-colors duration-200">
                    <span class="font-semibold text-gray-800 pr-4">Apa itu SIPERDIN?</span>
                    <div id="icon-1" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-gray-200 transition-all duration-300">
                        <svg class="w-5 h-5 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div id="answer-1" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="px-6 pb-5 pt-2">
                        <div class="border-t pt-4" style="border-color: #0F55C9;">
                            <p class="text-gray-700 leading-relaxed">SIPERDIN (Sistem Informasi Perjalanan Dinas) adalah platform digital yang digunakan untuk presensi dan pemantauan perjalanan dinas secara online, memudahkan proses administrasi dan pelaporan perjalanan dinas.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <button onclick="toggleFAQ(2)" class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-blue-50 transition-colors duration-200">
                    <span class="font-semibold text-gray-800 pr-4">Bagaimana cara login ke SIPERDIN?</span>
                    <div id="icon-2" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-gray-200 transition-all duration-300">
                        <svg class="w-5 h-5 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div id="answer-2" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="px-6 pb-5 pt-2">
                        <div class="border-t pt-4" style="border-color: #0F55C9;">
                            <p class="text-gray-700 leading-relaxed">Anda dapat login menggunakan NIP dan password yang telah terdaftar. Pastikan Anda sudah memiliki akun yang diaktivasi oleh administrator sistem.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <button onclick="toggleFAQ(6)" class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-blue-50 transition-colors duration-200">
                    <span class="font-semibold text-gray-800 pr-4">Apa yang harus dilakukan jika lupa password?</span>
                    <div id="icon-6" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-gray-200 transition-all duration-300">
                        <svg class="w-5 h-5 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div id="answer-6" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="px-6 pb-5 pt-2">
                        <div class="border-t pt-4" style="border-color: #0F55C9;">
                            <p class="text-gray-700 leading-relaxed">Klik tombol 'Lupa Password' pada halaman login, kemudian masukkan NIP dan email terdaftar. Link reset password akan dikirimkan ke email Anda.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <button onclick="toggleFAQ(7)" class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-blue-50 transition-colors duration-200">
                    <span class="font-semibold text-gray-800 pr-4">Bagaimana cara mengubah profil saya?</span>
                    <div id="icon-7" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-gray-200 transition-all duration-300">
                        <svg class="w-5 h-5 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div id="answer-7" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="px-6 pb-5 pt-2">
                        <div class="border-t pt-4" style="border-color: #0F55C9;">
                            <p class="text-gray-700 leading-relaxed">Buka halaman 'Profile', klik tombol 'Edit' di bagian bawah, lalu ubah data yang diperlukan seperti nomor HP atau email. Jangan lupa klik 'Simpan' setelah selesai.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 5 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <button onclick="toggleFAQ(9)" class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-blue-50 transition-colors duration-200">
                    <span class="font-semibold text-gray-800 pr-4">Bagaimana cara membuat laporan perjalanan dinas?</span>
                    <div id="icon-9" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-gray-200 transition-all duration-300">
                        <svg class="w-5 h-5 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div id="answer-9" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="px-6 pb-5 pt-2">
                        <div class="border-t pt-4" style="border-color: #0F55C9;">
                            <p class="text-gray-700 leading-relaxed">Setelah perjalanan dinas selesai, masuk ke menu 'Laporan', pilih pengajuan yang sudah dilakukan, lalu lengkapi laporan dengan detail kegiatan, hasil, dan dokumentasi. Upload bukti kuitansi pengeluaran.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 6 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <button onclick="toggleFAQ(10)" class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-blue-50 transition-colors duration-200">
                    <span class="font-semibold text-gray-800 pr-4">Siapa yang bisa saya hubungi jika mengalami kendala?</span>
                    <div id="icon-10" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-gray-200 transition-all duration-300">
                        <svg class="w-5 h-5 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div id="answer-10" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="px-6 pb-5 pt-2">
                        <div class="border-t pt-4" style="border-color: #0F55C9;">
                            <p class="text-gray-700 leading-relaxed">Anda dapat menghubungi Admin SIPERDIN melalui email: admin@stis.ac.id atau telepon ke bagian Kepegawaian di nomor 08xxxxxxx pada jam kerja.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="mt-8 bg-white rounded-xl shadow-md p-6">
            <div class="flex items-start space-x-3">
                <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center mt-1" style="background-color: #0F55C9;">
                    <span class="text-white text-sm font-bold">i</span>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 mb-1">Butuh Bantuan Lebih Lanjut?</h3>
                    <p class="text-gray-600 text-sm">
                        Jika pertanyaan Anda tidak terjawab di FAQ ini, silakan hubungi Admin SIPERDIN melalui email: <span class="font-semibold" style="color: #0F55C9;">admin@stis.ac.id</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFAQ(id) {
    const answer = document.getElementById('answer-' + id);
    const icon = document.getElementById('icon-' + id);
    const svg = icon.querySelector('svg');
    
    // Check current state
    const isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px';
    
    if (isOpen) {
        // Close accordion
        answer.style.maxHeight = '0px';
        icon.style.backgroundColor = '#E5E7EB';
        svg.classList.remove('rotate-180', 'text-white');
        svg.classList.add('text-gray-600');
    } else {
        // Open accordion
        answer.style.maxHeight = answer.scrollHeight + 'px';
        icon.style.backgroundColor = '#0F55C9';
        svg.classList.remove('text-gray-600');
        svg.classList.add('rotate-180', 'text-white');
    }
}
</script>

<style>
.rotate-180 {
    transform: rotate(180deg);
}
</style>
@endsection