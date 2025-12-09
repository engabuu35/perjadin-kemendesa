@extends('layouts.appPPK')

@section('title', 'Detail Verifikasi Keuangan')

@section('content')
<main class="ml-0 sm:ml-[80px] max-w-[98%] mx-auto px-2 py-6">
    
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6 px-2">
        <x-page-title 
        title="Verifikasi Keuangan"
        subtitle="Surat Tugas: {{ $perjalanan->nomor_surat }}" />
       <div class="flex items-center gap-3">
        <span class="px-4 py-2 -mt-8 {{ $isSelesai ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }} rounded-lg font-bold text-sm border">
            Status: {{ $statusText }}
        </span>
            <div class="flex justify-end mb-8">
                <a href="{{ url('/ppk/pelaporan')}}" 
                class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
            </div>
        </div>

    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">{{ session('success') }}</div>
    @endif

    <!-- 1. TABEL LS RAMPUNG (SCROLLABLE) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="font-bold text-gray-700">Rincian LS Rampung (Data dari PIC)</h3>
        </div>
        
        <div class="overflow-x-auto p-4">
            <table class="min-w-[1800px] w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b text-center font-bold">
                    <tr>
                        <!-- LEVEL 1: HEADER UTAMA -->
                        <th rowspan="3" class="px-4 py-2 border-r border-gray-300 w-12 align-middle bg-gray-50">No</th>
                        <th rowspan="3" class="px-4 py-2 border-r border-gray-300 w-32 align-middle bg-gray-50">NIP</th>
                        <th rowspan="3" class="px-4 py-2 border-r border-gray-300 w-48 align-middle bg-gray-50">Nama</th>
                        
                        <!-- Grup Keuangan -->
                        <th colspan="10" class="px-4 py-2 border-b border-r border-gray-300 bg-blue-50">Rincian Pembayaran</th>
                        
                        <!-- Grup Penginapan -->
                        <th colspan="2" class="px-4 py-2 border-b border-r border-gray-300 bg-yellow-50">Penginapan</th>
                        
                        <!-- Grup Transportasi (BARU) -->
                        <th colspan="6" class="px-4 py-2 border-b border-gray-300 bg-green-50">Transportasi</th>
                    </tr>
                    <tr>
                        <!-- LEVEL 2: SUB HEADER KEUANGAN -->
                        <th rowspan="2" class="px-4 py-2 border-r border-b border-gray-300 bg-blue-100 text-blue-900 align-middle">Total Dibayarkan</th>
                        <th colspan="8" class="px-4 py-2 border-b border-r border-gray-300 bg-white">Rincian Biaya (Rp)</th>
                        <th rowspan="2" class="px-4 py-2 border-r border-b border-gray-300 text-red-600 bg-red-50 align-middle">SSPB</th>
                        
                        <!-- LEVEL 2: SUB HEADER PENGINAPAN -->
                        <th rowspan="2" class="px-4 py-2 border-r border-b border-gray-300 bg-white align-middle">Nama Hotel</th>
                        <th rowspan="2" class="px-4 py-2 border-r border-b border-gray-300 bg-white align-middle">Kota</th>
                        
                        <!-- LEVEL 2: SUB HEADER TRANSPORTASI -->
                        <th colspan="3" class="px-4 py-1 border-b border-r border-gray-300 bg-green-100 text-green-800">Pergi</th>
                        <th colspan="3" class="px-4 py-1 border-b border-gray-300 bg-green-100 text-green-800">Pulang</th>
                    </tr>
                    <tr class="bg-white">
                        <!-- LEVEL 3: DETAIL KEUANGAN -->
                        <th class="px-4 py-2 border-r border-gray-200 min-w-[100px]">Tiket</th>
                        <th class="px-4 py-2 border-r border-gray-200 min-w-[100px]">Uang Harian</th>
                        <th class="px-4 py-2 border-r border-gray-200 min-w-[100px]">Penginapan</th>
                        <th class="px-4 py-2 border-r border-gray-200 min-w-[100px]">Uang Rep.</th>
                        <th class="px-4 py-2 border-r border-gray-200 min-w-[100px]">Transport</th>
                        <th class="px-4 py-2 border-r border-gray-200 min-w-[100px]">Sewa Kend.</th>
                        <th class="px-4 py-2 border-r border-gray-200 min-w-[100px]">Riil</th>
                        <th class="px-4 py-2 border-r border-gray-200 min-w-[80px]">Lainnya</th>
                        
                        <!-- LEVEL 3: DETAIL PERGI -->
                        <th class="px-3 py-2 border-r border-gray-200 min-w-[120px]">Jenis Transportasi</th>
                        <th class="px-3 py-2 border-r border-gray-200 min-w-[100px]">Kode Tiket</th>
                        <th class="px-3 py-2 border-r border-gray-200 min-w-[120px]">Nama Transport</th>

                        <!-- LEVEL 3: DETAIL PULANG -->
                        <th class="px-3 py-2 border-r border-gray-200 min-w-[120px]">Jenis Transportasi</th>
                        <th class="px-3 py-2 border-r border-gray-200 min-w-[100px]">Kode Tiket</th>
                        <th class="px-3 py-2 border-gray-200 min-w-[12  0px]">Nama Transport</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapData as $index => $data)
                    <tr class="border-b hover:bg-gray-50 text-center text-xs">
                        <td class="px-4 py-3 border-r">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 border-r font-mono">{{ $data['nip'] }}</td>
                        <td class="px-4 py-3 border-r text-left font-bold text-gray-800 whitespace-nowrap">{{ $data['nama'] }}</td>
                        
                        <!-- Total & Rincian dengan File Link -->
                        <td class="px-4 py-3 border-r font-bold bg-blue-50 text-blue-800 text-sm">
                            {{ number_format($data['biaya']['Total'], 0, ',', '.') }}
                        </td>
                        
                        <!-- Tiket dengan File -->
                        <td class="px-2 py-3 border-r">
                            <div class="flex flex-col items-center gap-1">
                                <span class="font-semibold">{{ number_format($data['biaya']['Tiket']['nominal'], 0, ',', '.') }}</span>
                                @if(!empty($data['biaya']['Tiket']['path_file']))
                                    <a href="{{ asset('storage/'.$data['biaya']['Tiket']['path_file']) }}" 
                                       target="_blank" 
                                       class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded hover:bg-blue-200 transition flex items-center gap-1"
                                       title="{{ $data['biaya']['Tiket']['nama_file'] }}">
                                        <i class="fa-solid fa-file-pdf"></i> Lihat
                                    </a>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Uang Harian dengan File -->
                        <td class="px-2 py-3 border-r">
                            <div class="flex flex-col items-center gap-1">
                                <span class="font-semibold">{{ number_format($data['biaya']['Uang Harian']['nominal'], 0, ',', '.') }}</span>
                                @if(!empty($data['biaya']['Uang Harian']['path_file']))
                                    <a href="{{ asset('storage/'.$data['biaya']['Uang Harian']['path_file']) }}" 
                                       target="_blank" 
                                       class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded hover:bg-blue-200 transition flex items-center gap-1"
                                       title="{{ $data['biaya']['Uang Harian']['nama_file'] }}">
                                        <i class="fa-solid fa-file-pdf"></i> Lihat
                                    </a>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Penginapan dengan File -->
                        <td class="px-2 py-3 border-r">
                            <div class="flex flex-col items-center gap-1">
                                <span class="font-semibold">{{ number_format($data['biaya']['Penginapan']['nominal'], 0, ',', '.') }}</span>
                                @if(!empty($data['biaya']['Penginapan']['path_file']))
                                    <a href="{{ asset('storage/'.$data['biaya']['Penginapan']['path_file']) }}" 
                                       target="_blank" 
                                       class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded hover:bg-blue-200 transition flex items-center gap-1"
                                       title="{{ $data['biaya']['Penginapan']['nama_file'] }}">
                                        <i class="fa-solid fa-file-pdf"></i> Lihat
                                    </a>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Uang Representasi dengan File -->
                        <td class="px-2 py-3 border-r">
                            <div class="flex flex-col items-center gap-1">
                                <span class="font-semibold">{{ number_format($data['biaya']['Uang Representasi']['nominal'], 0, ',', '.') }}</span>
                                @if(!empty($data['biaya']['Uang Representasi']['path_file']))
                                    <a href="{{ asset('storage/'.$data['biaya']['Uang Representasi']['path_file']) }}" 
                                       target="_blank" 
                                       class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded hover:bg-blue-200 transition flex items-center gap-1"
                                       title="{{ $data['biaya']['Uang Representasi']['nama_file'] }}">
                                        <i class="fa-solid fa-file-pdf"></i> Lihat
                                    </a>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Transport dengan File -->
                        <td class="px-2 py-3 border-r">
                            <div class="flex flex-col items-center gap-1">
                                <span class="font-semibold">{{ number_format($data['biaya']['Transport']['nominal'], 0, ',', '.') }}</span>
                                @if(!empty($data['biaya']['Transport']['path_file']))
                                    <a href="{{ asset('storage/'.$data['biaya']['Transport']['path_file']) }}" 
                                       target="_blank" 
                                       class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded hover:bg-blue-200 transition flex items-center gap-1"
                                       title="{{ $data['biaya']['Transport']['nama_file'] }}">
                                        <i class="fa-solid fa-file-pdf"></i> Lihat
                                    </a>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Sewa Kendaraan dengan File -->
                        <td class="px-2 py-3 border-r">
                            <div class="flex flex-col items-center gap-1">
                                <span class="font-semibold">{{ number_format($data['biaya']['Sewa Kendaraan']['nominal'], 0, ',', '.') }}</span>
                                @if(!empty($data['biaya']['Sewa Kendaraan']['path_file']))
                                    <a href="{{ asset('storage/'.$data['biaya']['Sewa Kendaraan']['path_file']) }}" 
                                       target="_blank" 
                                       class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded hover:bg-blue-200 transition flex items-center gap-1"
                                       title="{{ $data['biaya']['Sewa Kendaraan']['nama_file'] }}">
                                        <i class="fa-solid fa-file-pdf"></i> Lihat
                                    </a>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Pengeluaran Riil dengan File -->
                        <td class="px-2 py-3 border-r">
                            <div class="flex flex-col items-center gap-1">
                                <span class="font-semibold">{{ number_format($data['biaya']['Pengeluaran Riil']['nominal'], 0, ',', '.') }}</span>
                                @if(!empty($data['biaya']['Pengeluaran Riil']['path_file']))
                                    <a href="{{ asset('storage/'.$data['biaya']['Pengeluaran Riil']['path_file']) }}" 
                                       target="_blank" 
                                       class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded hover:bg-blue-200 transition flex items-center gap-1"
                                       title="{{ $data['biaya']['Pengeluaran Riil']['nama_file'] }}">
                                        <i class="fa-solid fa-file-pdf"></i> Lihat
                                    </a>
                                @endif
                            </div>
                        </td>
                        
                        <td class="px-4 py-3 border-r">-</td> 
                        
                        <!-- SSPB dengan File -->
                        <td class="px-2 py-3 text-red-500 border-r font-bold bg-red-50">
                            <div class="flex flex-col items-center gap-1">
                                <span>{{ number_format($data['biaya']['SSPB']['nominal'], 0, ',', '.') }}</span>
                                @if(!empty($data['biaya']['SSPB']['path_file']))
                                    <a href="{{ asset('storage/'.$data['biaya']['SSPB']['path_file']) }}" 
                                       target="_blank" 
                                       class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded hover:bg-red-200 transition flex items-center gap-1"
                                       title="{{ $data['biaya']['SSPB']['nama_file'] }}">
                                        <i class="fa-solid fa-file-pdf"></i> Lihat
                                    </a>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Info Penginapan -->
                        <td class="px-4 py-3 border-r text-gray-700">{{ $data['info']['Nama Penginapan'] }}</td>
                        <td class="px-4 py-3 border-r text-gray-700">{{ $data['info']['Kota'] }}</td>
                        
                        <!-- Info Pergi -->
                        <td class="px-3 py-3 border-r text-gray-700">{{ $data['info']['Jenis Transportasi(Pergi)'] }}</td>
                        <td class="px-3 py-3 border-r text-gray-700 font-mono">{{ $data['info']['Kode Tiket(Pergi)'] }}</td>
                        <td class="px-3 py-3 border-r text-gray-700">{{ $data['info']['Nama Transportasi(Pergi)'] }}</td>

                        <!-- Info Pulang -->
                        <td class="px-3 py-3 border-r text-gray-700">{{ $data['info']['Jenis Transportasi(Pulang)'] }}</td>
                        <td class="px-3 py-3 border-r text-gray-700 font-mono">{{ $data['info']['Kode Tiket(Pulang)'] }}</td>
                        <td class="px-3 py-3 text-gray-700">{{ $data['info']['Nama Transportasi(Pulang)'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-bold text-gray-900 border-t-2 border-gray-300">
                    <tr class="text-center">
                        <td colspan="3" class="px-4 py-4 text-right border-r">TOTAL KESELURUHAN:</td>
                        <td class="px-4 py-4 bg-yellow-100 text-yellow-800 border-r text-md border-l">
                            Rp {{ number_format($totalSeluruhnya, 0, ',', '.') }}
                        </td>
                        <td colspan="13" class="bg-gray-50"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- FORM VERIFIKASI -->
    <div class="bg-white rounded-xl shadow-lg border border-yellow-200 p-8 max-w-4xl mx-auto">
        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
            <span class="bg-yellow-100 text-yellow-700 p-2 rounded"><i class="fa-regular fa-credit-card"></i></span>
            Verifikasi & Keputusan PPK
        </h3>

        <!-- FORM SETUJUI -->
        <form action="{{ route('ppk.verifikasi.approve', $perjalanan->id) }}" method="POST" id="form-approve">
            @csrf
            <input type="hidden" name="total_biaya_rampung" value="{{ $totalSeluruhnya }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Input SPM & SP2D -->
                <div class="space-y-4">
                    <h4 class="font-bold text-gray-600 border-b pb-2">Data SPM</h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor SPM</label>
                        
                        <input type="text" name="nomor_spm" 
                               value="{{ old('nomor_spm', $laporanKeuangan->nomor_spm ?? '') }}" 
                               class="w-full border-gray-300 rounded-lg disabled:bg-gray-100 disabled:text-gray-500 @error('nomor_spm') border-red-500 @enderror" 
                               required 
                               @disabled($isSelesai)
                               placeholder="Contoh: 251751303001100">
                        @error('nomor_spm')
                            <p class="text-red-500 text-sm mt-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal SPM</label>
                        <input type="date" name="tanggal_spm" 
                               value="{{ old('tanggal_spm', $laporanKeuangan->tanggal_spm ? \Carbon\Carbon::parse($laporanKeuangan->tanggal_spm)->format('Y-m-d') : '') }}"
                               class="w-full border-gray-300 rounded-lg disabled:bg-gray-100 disabled:text-gray-500" 
                               required 
                               @disabled($isSelesai)>
                    </div>
                </div>
                <div class="space-y-4">
                    <h4 class="font-bold text-gray-600 border-b pb-2">Data SP2D</h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor SP2D</label>
                        <input type="text" name="nomor_sp2d" 
                               value="{{ old('nomor_sp2d', $laporanKeuangan->nomor_sp2d ?? '') }}"
                               class="w-full border-gray-300 rounded-lg disabled:bg-gray-100 disabled:text-gray-500 @error('nomor_sp2d') border-red-500 @enderror" 
                               required 
                               @disabled($isSelesai)
                               placeholder="Contoh: 1.000.000">
                        @error('nomor_sp2d')
                            <p class="text-red-500 text-sm mt-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal SP2D</label>
                        <input type="date" name="tanggal_sp2d" 
                               value="{{ old('tanggal_sp2d', $laporanKeuangan->tanggal_sp2d ? \Carbon\Carbon::parse($laporanKeuangan->tanggal_sp2d)->format('Y-m-d') : '') }}"
                               class="w-full border-gray-300 rounded-lg disabled:bg-gray-100 disabled:text-gray-500" 
                               required 
                               @disabled($isSelesai)>
                    </div>
                </div>
            </div>
        </form>

        <!-- ACTION BUTTONS -->
        <!-- PERBAIKAN: Sembunyikan tombol jika sudah selesai -->
        @if(!$isSelesai)
        <div class="flex justify-between pt-6 border-t gap-4">
            <!-- TOMBOL TOLAK (Buka Modal/Accordion) -->
            <button type="button" onclick="document.getElementById('reject-section').classList.toggle('hidden')" 
            class="bg-red-100 text-red-600 px-6 py-2 rounded-xl font-bold hover:bg-red-200 transition">
                <i class="fa-regular fa-circle-xmark"></i> Tolak / Revisi
            </button>

            <!-- TOMBOL SETUJUI (Submit Form Approve) -->
            <button 
                type="button" 
                id="openApproveModal"
                class="bg-green-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-green-700 shadow-lg flex items-center gap-2">
                <i class="fa-regular fa-circle-check"></i>
                Setujui & Selesaikan
            </button>
        <!-- Modal Konfirmasi Approve -->
        <div id="approveModal"
            class="fixed inset-0 bg-black/60 flex items-center justify-center 
                    opacity-0 pointer-events-none transition-opacity duration-300 z-[9999]">

            <div id="approveBox"
                class="bg-white rounded-xl shadow-2xl w-[90%] max-w-md p-6 text-center 
                        transform scale-90 transition-transform duration-300">

                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-2xl"></i>
                </div>

                <h3 class="text-xl font-bold mb-2 text-gray-800">Setujui Laporan Keuangan?</h3>
                <p class="text-gray-600 mb-6">
                    Apakah Anda yakin ingin menyetujui pembayaran ini?  
                    <br><span class="text-red-600 font-semibold">
                            Proses ini tidak dapat dibatalkan.
                        </span>
                </p>

                <div class="flex justify-center gap-4">
                    <button id="cancelApprove"
                        class="py-2 px-6 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400 transition">
                        Batal
                    </button>

                    <button id="confirmApprove"
                        class="py-2 px-6 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                        Ya, Setujui
                    </button>
                </div>
            </div>
        </div>

        </div>

        <!-- FORM TOLAK (Hidden by default) -->
        <div id="reject-section" class="hidden mt-6 p-4 bg-red-50 rounded-xl border border-red-200">
            <form id="rejectForm" action="{{ route('ppk.verifikasi.reject', $perjalanan->id) }}" method="POST">
                @csrf
                <label class="block text-md font-bold text-red-700 mb-2">Alasan Penolakan / Catatan Revisi:</label>
                <textarea name="alasan_penolakan" rows="3" class="w-full text-sm border-red-300 rounded-lg focus:ring-red-500 mb-3" placeholder="Contoh: Nominal Tiket Pesawat Ketua tidak sesuai bukti..." required></textarea>
                <div class="text-right">
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-red-700">
                        <i class="fa-solid fa-rotate-left"></i> Kirim Revisi ke PIC
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="bg-green-50 text-green-800 p-4 rounded-xl border border-green-200 text-center font-bold">
            <i class="fa-solid fa-check-circle"></i> Laporan ini sudah disetujui
        </div>
        @endif
    </div>  
    
    <script>
    const approveModal = document.getElementById("approveModal");
    const approveBox = document.getElementById("approveBox");
    const openApproveBtn = document.getElementById("openApproveModal");

    // document.getElementById("openApproveModal").onclick = () => {
    //     approveModal.classList.remove("opacity-0", "pointer-events-none");
    //     approveModal.classList.add("opacity-100");

    //     setTimeout(() => {
    //         approveBox.classList.remove("scale-90");
    //         approveBox.classList.add("scale-100");
    //     }, 10);
    // };

    if(openApproveBtn) {
        openApproveBtn.onclick = () => {
            approveModal.classList.remove("opacity-0", "pointer-events-none");
            approveModal.classList.add("opacity-100");

            setTimeout(() => {
                approveBox.classList.remove("scale-90");
                approveBox.classList.add("scale-100");
            }, 10);
        };
    }

    function closeApproveModal() {
        approveModal.classList.add("opacity-0", "pointer-events-none");
        approveModal.classList.remove("opacity-100");

        approveBox.classList.remove("scale-100");
        approveBox.classList.add("scale-90");
    }

    // document.getElementById("cancelApprove").onclick = closeApproveModal;

    if(document.getElementById("cancelApprove")) {
        document.getElementById("cancelApprove").onclick = closeApproveModal;
    }

    // approveModal.onclick = closeApproveModal;
    // approveBox.onclick = (e) => e.stopPropagation();

    if(approveModal) {
        approveModal.onclick = closeApproveModal;
        approveBox.onclick = (e) => e.stopPropagation();
    }

// document.getElementById("confirmApprove").onclick = () => {
//     closeApproveModal(); 
//     setTimeout(() => {
//         showSuccessPopup(
//             "Berhasil!",
//             "Laporan keuangan telah disetujui dan pembayaran selesai."
//         );
//     }, 250);
//     document.getElementById("closeSuccessBtn").onclick = () => {
//         document.getElementById("form-approve").submit();
//     };
// };

    if(document.getElementById("confirmApprove")) {
        document.getElementById("confirmApprove").onclick = () => {
            closeApproveModal(); 
            setTimeout(() => {
                // MODIFIKASI: Kirim callback function sebagai parameter ke-3
                showSuccessPopup(
                    "Berhasil!",
                    "Laporan keuangan telah disetujui dan pembayaran selesai.",
                    () => {
                        // Aksi ini akan dijalankan saat tombol OK di klik
                        document.getElementById("form-approve").submit();
                    }
                );
            }, 250);
        };
    }

    // UPDATE: Menambahkan parameter onConfirm (callback function)
    function showSuccessPopup(title, message, onConfirm = null) {
        const modal = document.getElementById("successModal");
        const box   = document.getElementById("successBox");
        const closeBtn = document.getElementById("closeSuccessBtn");

        document.getElementById("successTitle").innerText = title;
        document.getElementById("successMessage").innerText = message;

        modal.classList.remove("opacity-0", "invisible");
        modal.classList.add("opacity-100");

        setTimeout(() => {
            box.classList.remove("scale-95");
            box.classList.add("scale-100");
        }, 20);

        // Define Close Action agar bisa dipakai di multiple event (button & outside click)
        const closeAction = () => {
            modal.classList.add("opacity-0", "invisible");
            // Animasi fade out modal
            setTimeout(() => {
                modal.classList.remove("opacity-100");
            }, 350);

            // JALANKAN CALLBACK JIKA ADA (Form Submit)
            if (onConfirm) {
                onConfirm();
            }
        };

        // Reset onclick handler agar bersih dan tidak tumpeng tindih
        // Cara terbaik: set properti .onclick langsung (bukan addEventListener) agar menimpa yang lama
        closeBtn.onclick = closeAction;

        // Klik di luar modal juga akan menutup dan trigger callback (opsional, tapi aman untuk UX)
        modal.onclick = (e) => {
            if (e.target === modal) {
                closeAction();
            }
        };

        box.onclick = (e) => e.stopPropagation();
    }

    // Form REJECT logic
    const rejectForm = document.getElementById("rejectForm");
    if(rejectForm) {
        rejectForm.addEventListener("submit", function(e){
            e.preventDefault();
            const form = e.target;

            // MODIFIKASI: Kirim callback function
            showSuccessPopup(
                "Berhasil!",
                "Revisi berhasil dikirim kembali ke PIC.",
                () => {
                    form.submit();
                }
            );
        });
    }

//     function showSuccessPopup(title, message) {
//     const modal = document.getElementById("successModal");
//     const box   = document.getElementById("successBox");

//     document.getElementById("successTitle").innerText = title;
//     document.getElementById("successMessage").innerText = message;

//     modal.classList.remove("opacity-0", "invisible");
//     modal.classList.add("opacity-100");

//     setTimeout(() => {
//         box.classList.remove("scale-95");
//         box.classList.add("scale-100");
//     }, 20);

//     document.getElementById("closeSuccessBtn").onclick = () => {
//         modal.classList.add("opacity-0", "invisible");
//         setTimeout(() => {
//             modal.classList.remove("opacity-100");
//         }, 350);
//     };

//     modal.onclick = () => {
//         modal.classList.add("opacity-0", "invisible");
//         setTimeout(() => {
//             modal.classList.remove("opacity-100");
//         }, 200);
//     };

//     box.onclick = (e) => e.stopPropagation();
// }
// document.getElementById("rejectForm").addEventListener("submit", function(e){
//     e.preventDefault();

//     showSuccessPopup(
//         "Berhasil!",
//         "Revisi berhasil dikirim kembali ke PIC."
//     );

//     document.getElementById("closeSuccessBtn").onclick = () => {
//         e.target.submit();
//     };
// });

</script>
<!-- GLOBAL SUCCESS MODAL -->
<div id="successModal" 
    class="fixed inset-0 bg-black/50 flex items-center justify-center opacity-0 invisible transition-opacity duration-300 z-[9999]">
    
    <div id="successBox"
        class="bg-white rounded-lg shadow-lg w-[90%] max-w-sm p-6 text-center transform scale-95 transition-transform duration-300">

        <div class="w-16 h-16 bg-green-100 rounded-full mx-auto mb-4 flex items-center justify-center animate-bounce">
            <i class="fas fa-check text-green-600 text-3xl"></i>
        </div>

        <h3 class="text-xl font-bold mb-2 text-gray-800" id="successTitle">Berhasil!</h3>
        <p class="text-gray-600 mb-6" id="successMessage">
            Laporan keuangan telah berhasil dikirim.
        </p>

        <button id="closeSuccessBtn"
            class="w-full py-3 px-4 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition shadow-md">
            OK
        </button>
    </div>
</div>

</main>
@endsection
