<footer class="relative bg-footer-pattern bg-[length:100%_100%] bg-no-repeat text-white w-full">
    <div class="relative max-w-7xl mx-auto px-6 py-12 md:py-20">
        <div class="flex flex-col md:flex-row justify-between gap-8 md:gap-12">

            <!-- Logo Section -->
            <div class="flex flex-col items-center md:items-start md:w-auto">
                <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-3 items-center">
                    <img src="{{ asset('img/logo_kementerian.png') }}" 
                        alt="Logo Kementerian" 
                        class="w-24 h-24 md:w-32 md:h-32">

                    <div class="flex flex-col leading-tight">
                        <h2 class="text-2xl md:text-3xl font-bold">Inspektorat Jenderal</h2>
                        <p class="text-sm md:text-md font-medium">
                            Kementerian Desa Dan<br>
                            Pembangunan Daerah Tertinggal
                        </p>
                    </div>
                </div>

                <div class="flex justify-center md:justify-start mt-6 md:mt-8">
                    <img src="{{ asset('img/logo_berakhlak.webp') }}" 
                        alt="Logo BerAKHLAK" 
                        class="w-auto h-20 md:h-24">
                </div>
            </div>

            <!-- Contact Section -->
            <div class="flex flex-col md:w-auto">
                <h3 class="text-sm font-semibold mb-4">Itjen Kemendesa PDT</h3>
                <ul class="space-y-3 text-gray-200 text-sm">
                    <li class="flex items-start gap-3"> 
                        <i class="fa-solid fa-location-dot w-5 mt-1 flex-shrink-0"></i>
                        <span class="text-xs">Jalan TMP Kalibata Nomor 17<br>Jakarta Selatan, Daerah Khusus Jakarta</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-phone w-5 flex-shrink-0"></i>
                        <span class="text-xs">021-7989925</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-fax w-5 flex-shrink-0"></i> 
                        <span class="text-xs">021-7974488</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope w-5 flex-shrink-0"></i>
                        <span class="text-xs">itjen.kemendesa@gmail.com</span>
                    </li>
                </ul>
            </div>

            <!-- Social Media Section -->
            <div class="flex flex-col md:w-auto">
                <h3 class="text-sm font-semibold mb-4">Sosial Media</h3>
                <ul class="space-y-3 text-gray-200 text-xs">
                    <li>
                        <a href="#" class="flex items-center gap-3 hover:text-pink-500 transition-colors">
                            <i class="fa-brands fa-instagram w-5 text-lg flex-shrink-0"></i>
                            <span>@itjen.kemendes</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 hover:text-red-500 transition-colors">
                            <i class="fa-brands fa-youtube w-5 text-lg flex-shrink-0"></i>
                            <span>Itjen Kemendes</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 hover:text-white transition-colors"> 
                            <i class="fa-brands fa-tiktok w-5 text-lg flex-shrink-0"></i>
                            <span>@itjenkemendespdt</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 hover:text-blue-400 transition-colors">
                            <i class="fa-brands fa-facebook w-5 text-lg flex-shrink-0"></i>
                            <span>@itjenkemendes</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 hover:text-blue-400 transition-colors">
                            <i class="fa-brands fa-twitter w-5 text-lg flex-shrink-0"></i>
                            <span>@itjenkemendes</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="mt-12 pt-6 border-t border-white/20">
            <div class="flex flex-col md:flex-row items-center justify-center md:justify-end gap-2 text-gray-200 text-xs text-center md:text-right">
                <span>Hak Cipta © - Politeknik Statistika STIS 2025</span>
            </div>
        </div>
    </div>
</footer>