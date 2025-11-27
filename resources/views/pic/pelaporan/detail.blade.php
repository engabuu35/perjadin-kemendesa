@extends('layouts.appPIC')

@section('title', 'Input Keuangan Perjadin')

@section('content')
<div class="max-w-[95%] mx-auto px-4 py-8">
    
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Input Rincian Biaya</h2>
            <p class="text-gray-600 text-sm mt-1">{{ $perjalanan->nomor_surat }} | {{ $perjalanan->tujuan }}</p>
        </div>
        <div class="flex gap-3">
            <span class="px-4 py-2 bg-gray-100 rounded-lg font-bold text-gray-600 text-sm border border-gray-200">
                Status: {{ $statusText }}
            </span>
            <a href="{{ route('pic.pelaporan.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm hover:bg-gray-700">Kembali</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- LOOP PEGAWAI -->
    <div class="space-y-10">
        @foreach($allPeserta as $peserta)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- HEADER KARTU -->
            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white text-blue-600 flex items-center justify-center font-bold text-sm">
                        {{ substr($peserta->name, 0, 2) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">{{ $peserta->name }}</h3>
                        <p class="text-xs opacity-90 font-mono">{{ $peserta->nip }} | {{ $peserta->role_perjadin }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[10px] opacity-80 uppercase tracking-wider">Total Reimburse</p>
                    <p class="font-bold text-xl">Rp {{ number_format($peserta->total_biaya, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- KIRI: DATA NOMINAL -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 mb-2 border-b pb-2 border-gray-100">
                        <span class="bg-green-100 text-green-700 p-1.5 rounded text-xs">💰</span>
                        <!-- JUDUL DIUBAH (Lebih sopan) -->
                        <h4 class="font-bold text-gray-700 text-sm uppercase tracking-wide">Rincian Biaya</h4>
                    </div>

                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <table class="w-full text-sm text-left">
                            @php $hasBiaya = false; @endphp
                            @if($peserta->laporan)
                                @foreach($peserta->laporan->bukti as $item)
                                    @if($item->nominal > 0)
                                    @php $hasBiaya = true; @endphp
                                    <tr class="border-b last:border-0 hover:bg-white transition">
                                        <td class="px-4 py-2 font-medium text-gray-700">{{ $item->kategori }}</td>
                                        <td class="px-4 py-2 text-right font-bold text-gray-800">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                        <td class="px-2 py-2 text-right w-8">
                                            @if(!$isReadOnly)
                                            <a href="{{ route('pic.pelaporan.deleteBukti', $item->id) }}" onclick="return confirm('Hapus?')" class="text-red-400 hover:text-red-600 font-bold">×</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            @endif
                            @if(!$hasBiaya)
                                <tr><td colspan="3" class="px-4 py-3 text-center text-xs text-gray-400 italic">Belum ada data biaya</td></tr>
                            @endif
                        </table>
                    </div>

                    @if(!$isReadOnly)
                    <form action="{{ route('pic.pelaporan.storeBukti', $perjalanan->id) }}" method="POST" enctype="multipart/form-data" 
                          class="bg-green-50/50 p-3 rounded-lg border border-green-100 mt-2">
                        @csrf
                        <input type="hidden" name="target_nip" value="{{ $peserta->nip }}">
                        <div class="flex flex-col gap-2">
                            <select name="kategori" class="w-full text-xs border-gray-300 rounded focus:ring-green-500 py-1.5" required>
                                <option value="" disabled selected>Pilih Kategori Biaya</option>
                                <option value="Tiket">Tiket</option>
                                <option value="Uang Harian">Uang Harian</option>
                                <option value="Penginapan">Penginapan</option>
                                <option value="Uang Representasi">Uang Representasi</option>
                                <option value="Sewa Kendaraan">Sewa Kendaraan</option>
                                <option value="Pengeluaran Riil">Pengeluaran Riil</option>
                                <option value="Transport">Transport</option>
                                <option value="SSPB">SSPB</option>
                            </select>
                            <div class="flex gap-2">
                                <input type="number" name="nominal" placeholder="Nominal (Rp)" class="w-2/3 text-xs border-gray-300 rounded py-1.5" required>
                                <button type="submit" class="w-1/3 bg-green-600 hover:bg-green-700 text-white font-bold rounded text-xs shadow-sm">+ Simpan</button>
                            </div>
                            <input type="file" name="bukti" class="w-full text-[10px] text-gray-500">
                        </div>
                    </form>
                    @endif
                </div>

                <!-- KANAN: DATA INFORMASI (TEKS) -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 mb-2 border-b pb-2 border-gray-100">
                        <span class="bg-blue-100 text-blue-700 p-1.5 rounded text-xs">📝</span>
                        <h4 class="font-bold text-gray-700 text-sm uppercase tracking-wide">Data Pendukung</h4>
                    </div>

                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <table class="w-full text-sm text-left">
                            @php $hasTeks = false; @endphp
                            @if($peserta->laporan)
                                @foreach($peserta->laporan->bukti as $item)
                                    @if($item->nominal == 0) 
                                    @php $hasTeks = true; @endphp
                                    <tr class="border-b last:border-0 hover:bg-white transition">
                                        <td class="px-4 py-2 font-medium text-gray-700 w-1/3">{{ $item->kategori }}</td>
                                        <!-- MENAMPILKAN KETERANGAN -->
                                        <td class="px-4 py-2 text-gray-600 text-xs">
                                            {{ $item->keterangan }}
                                        </td>
                                        <td class="px-2 py-2 text-right w-8">
                                            @if(!$isReadOnly)
                                            <a href="{{ route('pic.pelaporan.deleteBukti', $item->id) }}" onclick="return confirm('Hapus?')" class="text-red-400 hover:text-red-600 font-bold">×</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            @endif
                            @if(!$hasTeks)
                                <tr><td colspan="3" class="px-4 py-3 text-center text-xs text-gray-400 italic">Belum ada data</td></tr>
                            @endif
                        </table>
                    </div>

                    <!-- FORM INPUT TEKS (Tetap sama formnya, controller yg handle simpan ke keterangan) -->
                    @if(!$isReadOnly)
                    <form action="{{ route('pic.pelaporan.storeBukti', $perjalanan->id) }}" method="POST" 
                          class="bg-blue-50/50 p-3 rounded-lg border border-blue-100 mt-2"
                          x-data="{ kategori: '' }">
                        @csrf
                        <input type="hidden" name="target_nip" value="{{ $peserta->nip }}">
                        <div class="flex flex-col gap-2">
                            <select name="kategori" class="w-full text-xs border-gray-300 rounded focus:ring-blue-500 py-1.5" required id="kategori-select-{{ $loop->index }}" onchange="toggleInputs({{ $loop->index }})">
                                <option value="" disabled selected>Pilih Informasi</option>
                                <option value="Nama Penginapan">Nama Penginapan</option>
                                <option value="Kota">Kota</option>
                                <option value="Kode Tiket">Kode Tiket</option>
                                <option value="Maskapai">Maskapai</option>
                            </select>
                            
                            <!-- INPUTAN SPESIFIK -->
                            <input type="text" id="input-hotel-{{ $loop->index }}" name="nama_hotel" placeholder="Ketik Nama Penginapan" class="hidden w-full text-xs border-gray-300 rounded py-1.5">
                            <input type="text" id="input-kota-{{ $loop->index }}" name="kota" placeholder="Ketik Nama Kota" class="hidden w-full text-xs border-gray-300 rounded py-1.5">
                            <input type="text" id="input-kodetiket-{{ $loop->index }}" name="kode_tiket" placeholder="Ketik Kode Tiket" class="hidden w-full text-xs border-gray-300 rounded py-1.5">
                            <input type="text" id="input-maskapai-{{ $loop->index }}" name="maskapai" placeholder="Ketik Nama Maskapai" class="hidden w-full text-xs border-gray-300 rounded py-1.5">

                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold rounded text-xs shadow-sm py-1.5 mt-1">+ Simpan</button>
                        </div>
                    </form>
                    @endif
                </div>

            </div>
        </div>
        @endforeach
    </div>

    @if(!$isReadOnly)
    <div class="mt-10 p-6 bg-blue-50 rounded-2xl border border-blue-200 text-center">
        <h3 class="text-lg font-bold text-gray-800 mb-2">Finalisasi Laporan Keuangan</h3>
        <p class="text-sm text-gray-600 mb-6 max-w-2xl mx-auto">
            Pastikan seluruh data keuangan pegawai (Tiket, Penginapan, Uang Harian, dll) telah diinput dengan benar.
        </p>
        <form action="{{ route('pic.pelaporan.submit', $perjalanan->id) }}" method="POST" onsubmit="return confirm('Yakin kirim ke PPK?')">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold text-base hover:bg-blue-700 hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2 mx-auto">
                <i class="fa-solid fa-paper-plane"></i> Kirim ke PPK
            </button>
        </form>
    </div>
    @endif

</div>

<script>
function toggleInputs(index) {
    var select = document.getElementById('kategori-select-' + index);
    var val = select.value;
    
    // Sembunyikan semua dulu
    document.getElementById('input-hotel-' + index).classList.add('hidden');
    document.getElementById('input-kota-' + index).classList.add('hidden');
    document.getElementById('input-kodetiket-' + index).classList.add('hidden');
    document.getElementById('input-maskapai-' + index).classList.add('hidden');
    
    // Tampilkan sesuai pilihan
    if (val === 'Nama Penginapan') {
        document.getElementById('input-hotel-' + index).classList.remove('hidden');
    } else if (val === 'Kota') {
        document.getElementById('input-kota-' + index).classList.remove('hidden');
    } else if (val === 'Kode Tiket') {
        document.getElementById('input-kodetiket-' + index).classList.remove('hidden');
    } else if (val === 'Maskapai') {
        document.getElementById('input-maskapai-' + index).classList.remove('hidden');
    }
}
</script>
@endsection