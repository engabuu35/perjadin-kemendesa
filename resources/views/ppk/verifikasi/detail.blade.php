@extends('layouts.appPPK')

@section('title', 'Detail Verifikasi PPK')

@section('content')
<main class="transition-all duration-300 ml-0 sm:ml-[60px]">
     <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">   
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-4 px-2">
            <x-page-title 
            title="Verifikasi Pembayaran"
            subtitle="Surat Tugas: {{ $perjalanan->nomor_surat }}" />
            <div class="flex items-center gap-3">
            <span class="px-4 py-2 -mt-8 {{ $isSelesai ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }} rounded-lg font-bold text-sm border">
                Status: {{ $statusText }}
            </span>
                <x-back-button />
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
                            
                            <!-- Total & Rincian -->
                            <td class="px-4 py-3 border-r font-bold bg-blue-50 text-blue-800 text-sm">
                                {{ number_format($data['biaya']['Total'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Tiket'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Uang Harian'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Penginapan'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Uang Representasi'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Transport'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Sewa Kendaraan'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 border-r">{{ number_format($data['biaya']['Pengeluaran Riil'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 border-r">-</td> 
                            <td class="px-4 py-3 text-red-500 border-r font-bold bg-red-50">{{ number_format($data['biaya']['SSPB'], 0, ',', '.') }}</td>
                            
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
                            <td class="px-4 py-4 bg-yellow-100 text-yellow-800 border-r text-lg border-l">
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
                                class="w-full border-gray-300 rounded-lg disabled:bg-gray-100 disabled:text-gray-500" 
                                required 
                                @disabled($isSelesai)>
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
                                class="w-full border-gray-300 rounded-lg disabled:bg-gray-100 disabled:text-gray-500" 
                                required 
                                @disabled($isSelesai)>
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
                <button type="button" onclick="document.getElementById('reject-section').classList.toggle('hidden')" class="bg-red-100 text-red-600 px-6 py-3 rounded-xl font-bold hover:bg-red-200 transition">
                    <i class="fa-regular fa-circle-xmark"></i> Tolak / Revisi
                </button>

            <!-- TOMBOL SETUJUI -->
            <button type="button" id="openApproveModal" class="bg-green-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-green-700 shadow-lg flex items-center gap-2 transition">
                <i class="fa-regular fa-circle-check"></i>
                Setujui & Selesaikan
            </button>

            <!-- Modal Konfirmasi Approve -->
            <div id="approveModal" class="fixed inset-0 bg-black/60 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 z-[9999]">
                <div class="bg-white rounded-lg shadow-2xl w-[90%] max-w-md p-6 text-center transform scale-90 transition-transform duration-300" onclick="event.stopPropagation()">
                    <div class="w-12 h-12 bg-green-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fa-solid fa-check text-green-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Konfirmasi Persetujuan</h3>
                    <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menyetujui dan menyelesaikan pembayaran ini?</p>
                    <div class="flex justify-center gap-4">
                        <button id="cancelApprove" class="flex-1 max-w-[150px] py-3 px-6 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400 transition">
                            Batal
                        </button>
                        <button id="confirmApprove" class="flex-1 max-w-[150px] py-3 px-6 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                            Setujui
                        </button>
                    </div>
                </div>
            </div>
            </div>

            <!-- FORM TOLAK (Hidden by default) -->
            <div id="reject-section" class="hidden mt-6 p-4 bg-red-50 rounded-xl border border-red-200">
                <form action="{{ route('ppk.verifikasi.reject', $perjalanan->id) }}" method="POST">
                    @csrf
                    <label class="block text-sm font-bold text-red-700 mb-2">Alasan Penolakan / Catatan Revisi:</label>
                    <textarea name="alasan_penolakan" rows="3" class="w-full border-red-300 rounded-lg focus:ring-red-500 mb-3" placeholder="Contoh: Nominal Tiket Pesawat Ketua tidak sesuai bukti..." required></textarea>
                    <div class="text-right">
                        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-red-700">Kirim Revisi ke PIC</button>
                    </div>
                </form>
            </div>
            @else
            <div class="bg-green-50 text-green-800 p-4 rounded-xl border border-green-200 text-center font-bold">
                <i class="fa-solid fa-check-circle"></i> Laporan ini sudah disetujui dan dibayar.
            </div>
            @endif
        </div>    
    </div>  
    
    <script>
    // Ambil elemen
    const openApproveModalBtn = document.getElementById('openApproveModal');
    const approveModal = document.getElementById('approveModal');
    const approveBox = approveModal.querySelector('div');
    const cancelApproveBtn = document.getElementById('cancelApprove');
    const confirmApproveBtn = document.getElementById('confirmApprove');

    // Fungsi buka modal
    const openApproveModal = () => {
        approveModal.classList.remove('opacity-0', 'pointer-events-none');
        approveModal.classList.add('opacity-100', 'pointer-events-auto');
        setTimeout(() => {
            approveBox.classList.remove('scale-90');
            approveBox.classList.add('scale-100');
        }, 10);
    };

    // Fungsi tutup modal
    const closeApproveModal = () => {
        approveBox.classList.remove('scale-100');
        approveBox.classList.add('scale-90');
        setTimeout(() => {
            approveModal.classList.remove('opacity-100', 'pointer-events-auto');
            approveModal.classList.add('opacity-0', 'pointer-events-none');
        }, 300);
    };

    // Event listeners
    openApproveModalBtn.addEventListener('click', openApproveModal);
    cancelApproveBtn.addEventListener('click', closeApproveModal);

    // Tutup modal jika klik di luar
    approveModal.addEventListener('click', (e) => {
        if (e.target === approveModal) {
            closeApproveModal();
        }
    });

    // Konfirmasi approve - submit form
    confirmApproveBtn.addEventListener('click', () => {
        document.getElementById('form-approve').submit();
    });
</script>
</main>
@endsection