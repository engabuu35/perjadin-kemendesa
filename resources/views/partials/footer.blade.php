<footer class="bg-cover bg-center text-white" style="background-image: url('{{ asset('images/pattern/footer.png') }}');">
    <div class="bg-black/60">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="flex flex-col md:flex-row justify-between gap-8">

                <!-- Bagian Kiri: Logo dan Nama Kementerian -->
                <div class="flex flex-col md:w-1/3">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo_kemendesa.png') }}" alt="Kemendesa" class="w-14 h-14">
                        <h2 class="text-2xl font-semibold leading-tight">Kementerian Desa,<br>Pembangunan Daerah Tertinggal, dan Transmigrasi</h2>
                    </div>
                    <p class="mt-4 text-sm text-gray-200 leading-relaxed">
                        Kementerian yang berkomitmen untuk membangun desa mandiri, berdaya, dan berkelanjutan demi kesejahteraan masyarakat Indonesia.
                    </p>
                </div>

                <!-- Bagian Tengah: Informasi Kontak -->
                <div class="flex flex-col md:w-1/3">
                    <h3 class="text-lg font-semibold mb-4">Kontak Kami</h3>
                    <ul class="space-y-3 text-gray-200 text-sm">
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-location-dot w-5"></i>
                            <span>Jl. TMP Kalibata No.17, Jakarta Selatan, Indonesia</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-phone w-5"></i>
                            <span>(021) 799-4747</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-fax w-5"></i>
                            <span>(021) 799-4500</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope w-5"></i>
                            <span>info@kemendesa.go.id</span>
                        </li>
                    </ul>
                </div>

                <!-- Bagian Kanan: Media Sosial -->
                <div class="flex flex-col md:w-1/3">
                    <h3 class="text-lg font-semibold mb-4">Ikuti Kami</h3>
                    <div class="flex gap-4 text-gray-200 text-lg">
                        <a href="#" class="hover:text-blue-400"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="hover:text-sky-400"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="hover:text-pink-500"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="hover:text-red-500"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            <!-- Garis Pemisah -->
            <div class="border-t border-gray-500 mt-10 pt-6 text-center text-sm text-gray-300">
                © {{ date('Y') }} Kementerian Desa, Pembangunan Daerah Tertinggal, dan Transmigrasi Republik Indonesia. Semua Hak Dilindungi.
            </div>
        </div>
    </div>
</footer>
