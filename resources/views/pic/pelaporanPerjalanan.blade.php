@extends('layouts.appPIC')

@section('title', 'Pelaporan Perjalanan Dinas')

@section('content')
<!-- Main Content -->
<div class="max-w-5xl mx-auto px-5 py-8">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-5">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-5">
            {{ session('info') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-5 pb-4">
        <h2 class="text-gray-700 text-2xl font-bold relative">
            Pelaporan Perjalanan Dinas
            <span class="absolute bottom-0 left-0 w-64 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
        </h2>
        <button type="button" onclick="window.history.back()" 
                class="px-6 py-2 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition flex items-center gap-2">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Kembali
        </button>
    </div>
    
    <div class="bg-white rounded-xl p-8" style="box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.2);">
        <div class="mb-5">
            <label class="block text-gray-700 text-sm font-medium mb-2">Nomor Surat Tugas</label>
            <input type="text" value="{{ $pelaporan->nomor_surat ?? 'ST/001/2025' }}" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 bg-gray-50" 
                   disabled>
        </div>

        <div class="mb-5">
            <label class="block text-gray-700 text-sm font-medium mb-2">Tujuan</label>
            <input type="text" value="{{ $pelaporan->tujuan ?? 'Jakarta' }}" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 bg-gray-50" 
                   disabled>
        </div>

        <div class="mb-5">
            <label class="block text-gray-700 text-sm font-medium mb-2">Biaya</label>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-blue-700 text-white">
                            <th class="border border-blue-700 px-4 py-3 text-left font-semibold">No</th>
                            <th class="border border-blue-700 px-4 py-3 text-left font-semibold">Kategori</th>
                            <th class="border border-blue-700 px-4 py-3 text-left font-semibold">Bukti</th>
                            <th class="border border-blue-700 px-4 py-3 text-left font-semibold">Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($biaya ?? [
                            ['no' => 1, 'kategori' => 'Transport', 'bukti' => 'Tiket Pesawat', 'biaya' => 'Rp 2.500.000'],
                            ['no' => 2, 'kategori' => 'Akomodasi', 'bukti' => 'Hotel Invoice', 'biaya' => 'Rp 1.500.000'],
                            ['no' => 3, 'kategori' => 'Konsumsi', 'bukti' => 'Struk Makan', 'biaya' => 'Rp 500.000']
                        ] as $item)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="border border-gray-300 px-4 py-3">{{ $item['no'] }}</td>
                            <td class="border border-gray-300 px-4 py-3">{{ $item['kategori'] }}</td>
                            <td class="border border-gray-300 px-4 py-3">{{ $item['bukti'] }}</td>
                            <td class="border border-gray-300 px-4 py-3">{{ $item['biaya'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="border border-gray-300 px-4 py-3 text-center text-gray-500">
                                Tidak ada data biaya
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(($pelaporan->status ?? '') === 'ditolak')
        <!-- Form Catatan -->
        <form method="POST" action="{{ route('pelaporan.kirimCatatan', $pelaporan->id ?? 1) }}">
            @csrf
            <div class="mb-5">
                <label for="catatan" class="block text-gray-700 text-sm font-medium mb-2">Catatan:</label>
                <textarea id="catatan" name="catatan" rows="5"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-600 transition resize-y"
                          placeholder="Tulis catatan penolakan di sini...">{{ old('catatan', $pelaporan->catatan ?? '') }}</textarea>
            </div>
            
            <div class="flex flex-col gap-3 mt-8">
                <button type="submit" class="w-full py-3.5 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                    Kirim
                </button>
            </div>
        </form>

        @elseif(in_array($pelaporan->status ?? '', ['disetujui', 'catatan_terkirim']))
        <!-- Status Selesai -->
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ ($pelaporan->status ?? '') === 'disetujui' ? 'Pelaporan telah disetujui!' : 'Catatan telah terkirim!' }}
        </div>

        @else
        <!-- Tombol Tolak dan Setujui -->
        <div class="flex gap-3 mt-8">
                @csrf
                <button type="submit" class="w-full py-3.5 bg-gray-400 text-white rounded-lg font-semibold hover:bg-gray-500 transition">
                    Tolak
                </button>
            </form>
                @csrf
                <button type="submit" class="w-full py-3.5 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                    Setujui
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection