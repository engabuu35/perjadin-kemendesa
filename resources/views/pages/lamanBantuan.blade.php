@extends('layouts.app')

@section('title', 'FAQ')

@section('title', 'Bantuan')

@section('content')
<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4 shadow-lg" style="background: linear-gradient(135deg, #2954B0 0%, #4075c0ff 100%);">
                <i class="fas fa-question-circle text-3xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-3">FAQ SIPERDIN</h1>
            <p class="text-base text-gray-600 max-w-2xl mx-auto">Temukan jawaban untuk pertanyaan yang sering diajukan seputar Sistem Informasi Perjalanan Dinas</p>
        </div>

        <!-- FAQ Container -->
        <div class="space-y-2">
            <!-- FAQ Item 1 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100">
                <button onclick="toggleFAQ(1)" class="w-full px-5 py-4 flex items-center justify-between text-left group">
                    <div class="flex items-center flex-1 pr-4">
                        <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center mr-4 transition-all duration-300" style="background-color: rgba(41, 84, 176, 0.1);">
                            <span class="text-xs font-bold" style="color: #2954B0;">1</span>
                        </div>
                        <span class="font-semibold text-gray-900 text-md group-hover:text-blue-700 transition-colors">Apa itu SIPERDIN?</span>
                    </div>
                    <div id="icon-1" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300" style="background-color: rgba(41, 84, 176, 0.1);">
                        <svg class="w-4 h-4 transition-transform duration-300" style="color: #2954B0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div id="answer-1" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="px-5 pb-5 pt-2">
                        <div class="pl-10 pr-10">
                            <div class="border-l-4 pl-4 py-3 bg-blue-50 rounded-r-lg" style="border-color: #2954B0;">
                                <p class="text-gray-700 text-sm leading-relaxed">SIPERDIN (Sistem Informasi Perjalanan Dinas) adalah platform digital yang digunakan untuk presensi dan pemantauan perjalanan dinas secara online, memudahkan proses administrasi dan pelaporan perjalanan dinas.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100">
                <button onclick="toggleFAQ(2)" class="w-full px-5 py-4 flex items-center justify-between text-left group">
                    <div class="flex items-center flex-1 pr-4">
                        <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center mr-4 mt-0.5 transition-all duration-300" style="background-color: rgba(41, 84, 176, 0.1);">
                            <span class="text-xs font-bold" style="color: #2954B0;">2</span>
                        </div>
                        <span class="font-semibold text-gray-900 text-md group-hover:text-blue-700 transition-colors">Bagaimana cara login ke SIPERDIN?</span>
                    </div>
                    <div id="icon-2" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300" style="background-color: rgba(41, 84, 176, 0.1);">
                        <svg class="w-4 h-4 transition-transform duration-300" style="color: #2954B0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div id="answer-2" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="px-5 pb-5 pt-2">
                        <div class="pl-10 pr-10">
                            <div class="border-l-4 pl-4 py-3 bg-blue-50 rounded-r-lg" style="border-color: #2954B0;">
                                <p class="text-gray-700 text-sm leading-relaxed">Anda dapat login menggunakan NIP dan password yang telah terdaftar. Pastikan Anda sudah memiliki akun yang diaktivasi oleh administrator sistem.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100">
                <button onclick="toggleFAQ(3)" class="w-full px-5 py-4 flex items-center justify-between text-left group">
                    <div class="flex items-center flex-1 pr-4">
                        <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center mr-4 mt-0.5 transition-all duration-300" style="background-color: rgba(41, 84, 176, 0.1);">
                            <span class="text-xs font-bold" style="color: #2954B0;">3</span>
                        </div>
                        <span class="font-semibold text-gray-900 text-md group-hover:text-blue-700 transition-colors">Apa yang harus dilakukan jika lupa password?</span>
                    </div>
                    <div id="icon-3" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300" style="background-color: rgba(41, 84, 176, 0.1);">
                        <svg class="w-4 h-4 transition-transform duration-300" style="color: #2954B0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div id="answer-3" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="px-5 pb-5 pt-2">
                        <div class="pl-10 pr-10">
                            <div class="border-l-4 pl-4 py-3 bg-blue-50 rounded-r-lg" style="border-color: #2954B0;">
                                <p class="text-gray-700 text-sm leading-relaxed">Klik tombol 'Lupa Password' pada halaman login, kemudian masukkan NIP yang terdaftar. Link reset password akan dikirimkan ke email Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100">
                <button onclick="toggleFAQ(4)" class="w-full px-5 py-4 flex items-center justify-between text-left group">
                    <div class="flex items-center flex-1 pr-4">
                        <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center mr-4 mt-0.5 transition-all duration-300" style="background-color: rgba(41, 84, 176, 0.1);">
                            <span class="text-xs font-bold" style="color: #2954B0;">4</span>
                        </div>
                        <span class="font-semibold text-gray-900 text-md group-hover:text-blue-700 transition-colors">Bagaimana cara mengubah profil saya?</span>
                    </div>
                    <div id="icon-4" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300" style="background-color: rgba(41, 84, 176, 0.1);">
                        <svg class="w-4 h-4 transition-transform duration-300" style="color: #2954B0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div id="answer-4" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="px-5 pb-5 pt-2">
                        <div class="pl-10 pr-10">
                            <div class="border-l-4 pl-4 py-3 bg-blue-50 rounded-r-lg" style="border-color: #2954B0;">
                                <p class="text-gray-700 text-sm leading-relaxed">Buka halaman 'Profile', klik tombol 'Edit' di bagian bawah, lalu ubah data yang diperlukan seperti nomor HP atau email. Jangan lupa klik 'Simpan' setelah selesai.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 5 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100">
                <button onclick="toggleFAQ(5)" class="w-full px-5 py-4 flex items-center justify-between text-left group">
                    <div class="flex items-center flex-1 pr-4">
                        <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center mr-4 mt-0.5 transition-all duration-300" style="background-color: rgba(41, 84, 176, 0.1);">
                            <span class="text-xs font-bold" style="color: #2954B0;">5</span>
                        </div>
                        <span class="font-semibold text-gray-900 text-md group-hover:text-blue-700 transition-colors">Bagaimana cara membuat laporan perjalanan dinas?</span>
                    </div>
                    <div id="icon-5" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300" style="background-color: rgba(41, 84, 176, 0.1);">
                        <svg class="w-4 h-4 transition-transform duration-300" style="color: #2954B0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div id="answer-5" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="px-5 pb-5 pt-2">
                        <div class="pl-10 pr-10">
                            <div class="border-l-4 pl-4 py-3 bg-blue-50 rounded-r-lg" style="border-color: #2954B0;">
                                <p class="text-gray-700 text-sm leading-relaxed">Setelah perjalanan dinas selesai, masuk ke menu 'Laporan', pilih pengajuan yang sudah dilakukan, lalu lengkapi laporan dengan detail kegiatan, hasil, dan dokumentasi. Upload bukti kuitansi pengeluaran.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 6 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100">
                <button onclick="toggleFAQ(6)" class="w-full px-5 py-4 flex items-center justify-between text-left group">
                    <div class="flex items-center flex-1 pr-4">
                        <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center mr-4 mt-0.5 transition-all duration-300" style="background-color: rgba(41, 84, 176, 0.1);">
                            <span class="text-xs font-bold" style="color: #2954B0;">6</span>
                        </div>
                        <span class="font-semibold text-gray-900 text-md group-hover:text-blue-700 transition-colors">Siapa yang bisa saya hubungi jika mengalami kendala?</span>
                    </div>
                    <div id="icon-6" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300" style="background-color: rgba(41, 84, 176, 0.1);">
                        <svg class="w-4 h-4 transition-transform duration-300" style="color: #2954B0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div id="answer-6" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="px-5 pb-5 pt-2">
                        <div class="pl-10 pr-10">
                            <div class="border-l-4 pl-4 py-3 bg-blue-50 rounded-r-lg" style="border-color: #2954B0;">
                                <p class="text-gray-700 text-sm leading-relaxed">Anda dapat menghubungi Admin SIPERDIN melalui email: admin@stis.ac.id atau telepon ke bagian Kepegawaian di nomor 08xxxxxxx pada jam kerja.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Card -->
        <div class="mt-8 rounded-xl shadow-lg overflow-hidden" style="background: linear-gradient(135deg, #2954B0 0%, #4075c0ff 100%);">
            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex-shrink-0 flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-info-circle text-xl text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-white mb-2">Butuh Bantuan Lebih Lanjut?</h3>
                        <p class="text-blue-100 text-sm leading-relaxed mb-4">
                            Jika pertanyaan Anda tidak terjawab di FAQ ini, jangan ragu untuk menghubungi kami.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="mailto:admin@stis.ac.id" class="inline-flex items-center px-4 py-2 bg-white text-blue-700 rounded-lg text-sm font-semibold hover:bg-blue-50 transition-all duration-200 shadow-md hover:shadow-lg">
                                <i class="fas fa-envelope mr-2"></i>
                                admin@stis.ac.id
                            </a>
                            <a href="tel:08xxxxxxx" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-10 text-white rounded-lg text-sm font-semibold hover:bg-opacity-20 transition-all duration-200 border border-white border-opacity-30">
                                <i class="fas fa-phone mr-2"></i>
                                08xxxxxxx
                            </a>
                        </div>
                    </div>
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
    
    const isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px';
    
    if (isOpen) {
        answer.style.maxHeight = '0px';
        icon.style.backgroundColor = 'rgba(41, 84, 176, 0.1)';
        svg.style.color = '#2954B0';
        svg.style.transform = 'rotate(0deg)';
    } else {
        answer.style.maxHeight = answer.scrollHeight + 'px';
        icon.style.backgroundColor = '#2954B0';
        svg.style.color = 'white';
        svg.style.transform = 'rotate(180deg)';
    }
}
</script>
@endsection