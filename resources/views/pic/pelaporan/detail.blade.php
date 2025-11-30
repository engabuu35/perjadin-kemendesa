@extends('layouts.appPIC')

@section('title', 'Input Keuangan Perjadin')

@section('content')
<main class="ml-0 sm:ml-[80px] max-w-[98%] mx-auto px-4 py-6">
    
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-blue-600"></i> Input Rincian Biaya
            </h2>
            <p class="text-gray-600 text-sm mt-1 font-mono bg-gray-100 inline-block px-2 py-1 rounded">
                {{ $perjalanan->nomor_surat }} | {{ $perjalanan->tujuan }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 {{ $statusText == 'Perlu Revisi' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200' }} rounded-lg font-bold text-sm border">
                Status: {{ $statusText }}
            </span>
            <x-back-button />
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <!-- FORM INPUT BULK -->
    <form action="{{ route('pic.pelaporan.storeBulk', $perjalanan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-12">
            @foreach($allPeserta as $index => $peserta)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                
                <!-- Header Peserta -->
                <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                            {{ $loop->iteration }}
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg">{{ $peserta->name }}</h3>
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded border border-blue-200 font-mono">
                                {{ $peserta->nip }}
                            </span>
                            <span class="text-xs text-gray-500 ml-2">{{ $peserta->role_perjadin }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] uppercase text-gray-500 font-bold tracking-wider">Total Biaya</span>
                        <div class="text-xl font-bold text-blue-600">
                            Rp {{ number_format($peserta->total_biaya, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 xl:grid-cols-3 gap-8">
                    
                    <!-- KOLOM KIRI: RINCIAN BIAYA (8 Field) -->
                    <div class="xl:col-span-2 space-y-4">
                        <h4 class="font-bold text-gray-700 text-sm uppercase tracking-wide border-b pb-2 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-coins text-yellow-500"></i> Rincian Biaya & Bukti
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                            @foreach($catBiaya as $kategori)
                                @php 
                                    $data = $peserta->buktiMap[$kategori] ?? null; 
                                    $nominal = $data ? $data->nominal : '';
                                    // Buat ID unik untuk manipulasi JS
                                    $uniqueId = $peserta->nip . '-' . str_replace(' ', '', $kategori);
                                @endphp
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 relative group hover:border-blue-300 transition">
                                    <label class="block text-xs font-bold text-gray-600 mb-1 uppercase">{{ $kategori }}</label>
                                    
                                    <!-- Input Nominal -->
                                    <div class="relative mb-2">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs font-bold">Rp</span>
                                        <input type="number" 
                                            name="items[{{ $peserta->nip }}][{{ $kategori }}][nominal]" 
                                            value="{{ $nominal }}" 
                                            class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                            placeholder="0"
                                            {{ $isReadOnly ? 'readonly' : '' }}>
                                    </div>

                                    <!-- Input File (Custom UI) -->
                                    <div class="pt-2 border-t border-gray-200">
                                        @if(!$isReadOnly)
                                            <div class="flex items-center gap-2">
                                                <label class="cursor-pointer bg-white border border-gray-300 text-gray-600 text-[10px] font-bold py-1 px-3 rounded hover:bg-gray-100 transition">
                                                    Choose File
                                                    <input type="file" 
                                                        id="file-{{ $uniqueId }}"
                                                        name="items[{{ $peserta->nip }}][{{ $kategori }}][file]" 
                                                        class="hidden"
                                                        onchange="updateFileName('{{ $uniqueId }}')">
                                                </label>
                                                <!-- Tempat Menampilkan Nama File Baru -->
                                                <span id="filename-{{ $uniqueId }}" class="text-[10px] text-gray-500 truncate max-w-[150px]">
                                                    Belum ada file baru
                                                </span>
                                            </div>
                                        @endif

                                        <!-- Link File Existing -->
                                        @if($data && $data->path_file)
                                            <div class="mt-1 flex items-center justify-between bg-green-50 px-2 py-1 rounded border border-green-100">
                                                <span class="text-[10px] text-green-700 truncate max-w-[120px]">
                                                    <i class="fa-solid fa-check-circle"></i> {{ $data->nama_file ?? 'File Tersimpan' }}
                                                </span>
                                                <a href="{{ asset('storage/'.$data->path_file) }}" target="_blank" 
                                                   class="text-[10px] font-bold text-green-700 hover:underline">
                                                    Lihat
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- KOLOM KANAN: DATA PENDUKUNG (4 Field) -->
                    <div class="xl:col-span-1 space-y-4">
                        <h4 class="font-bold text-gray-700 text-sm uppercase tracking-wide border-b pb-2 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-blue-500"></i> Informasi Tambahan
                        </h4>

                        <div class="space-y-4">
                            @foreach($catPendukung as $kategori)
                                @php 
                                    $data = $peserta->buktiMap[$kategori] ?? null; 
                                    $text = $data ? $data->keterangan : '';
                                @endphp
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">{{ $kategori }}</label>
                                    <input type="text" 
                                        name="items[{{ $peserta->nip }}][{{ $kategori }}][text]" 
                                        value="{{ $text }}" 
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="-"
                                        {{ $isReadOnly ? 'readonly' : '' }}>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Info Box -->
                        <div class="mt-6 p-3 bg-blue-50 text-blue-800 text-xs rounded border border-blue-100">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            <strong>Info File:</strong> Jika Anda tidak memilih file baru, file bukti yang sudah tersimpan sebelumnya (jika ada) <strong>tidak akan hilang</strong>.
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>

        <!-- AREA CATATAN REVISI (PALING BAWAH) -->
        <div class="mt-8 bg-gray-50 p-6 rounded-xl border border-gray-200">
            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-message text-gray-500"></i> Catatan / Revisi dari PPK
            </label>
            <textarea readonly rows="3" 
                class="w-full border-gray-300 rounded-lg bg-white text-gray-700 text-sm focus:ring-0 cursor-not-allowed {{ $perjalanan->catatan_penolakan ? 'border-red-300 bg-red-50 text-red-800' : '' }}"
                placeholder="Tidak ada catatan revisi.">{{ $perjalanan->catatan_penolakan ?? '-' }}</textarea>
        </div>

        <!-- FOOTER ACTION -->
        @if(!$isReadOnly)
        <div class="sticky bottom-4 z-40 mt-8">
            <div class="bg-white/90 backdrop-blur-sm p-4 rounded-xl shadow-lg border border-gray-200 flex justify-between items-center max-w-4xl mx-auto">
                <div class="text-sm text-gray-600">
                    <i class="fa-solid fa-floppy-disk text-blue-600 mr-1"></i>
                    Klik simpan untuk merekam semua perubahan di atas.
                </div>
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 shadow-md transition transform hover:-translate-y-0.5 flex items-center gap-2">
                    SIMPAN SEMUA DATA
                </button>
            </div>
        </div>
        @endif

    </form>

    <!-- AREA KIRIM KE PPK -->
    @if(!$isReadOnly)
    <div class="mt-12 p-8 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl border border-gray-200 text-center">
        <h3 class="text-lg font-bold text-gray-800 mb-2">Finalisasi & Kirim</h3>
        <p class="text-sm text-gray-600 mb-6 max-w-2xl mx-auto">
            Jika seluruh data keuangan pegawai telah diinput dan disimpan, silakan kirim ke PPK. <br>
            <span class="text-red-500 font-bold">Pastikan sudah klik "Simpan Semua Data" di atas sebelum mengirim!</span>
        </p>
        <form action="{{ route('pic.pelaporan.submit', $perjalanan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin data sudah lengkap dan ingin mengirim ke PPK?')">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-xl font-bold text-base hover:bg-green-700 hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2 mx-auto">
                <i class="fa-solid fa-paper-plane"></i> KIRIM KE PPK
            </button>
        </form>
    </div>
    @endif

</main>

<script>
    // Fungsi untuk Update Nama File saat dipilih
    function updateFileName(id) {
        // Ambil elemen input file
        const input = document.getElementById('file-' + id);
        // Ambil elemen label text di sebelahnya
        const label = document.getElementById('filename-' + id);
        
        // Cek apakah user memilih file
        if (input.files && input.files.length > 0) {
            // Ubah teks menjadi nama file
            label.textContent = input.files[0].name;
            
            // Ubah warna jadi biru agar terlihat beda
            label.classList.add('text-blue-600', 'font-bold');
            label.classList.remove('text-gray-500');
        } else {
            // Jika batal pilih file
            label.textContent = 'Belum ada file baru';
            label.classList.remove('text-blue-600', 'font-bold');
            label.classList.add('text-gray-500');
        }
    }
</script>
@endsection