<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Generate Excel | Aplikasi Laporan</title>

    <!-- Tailwind (otomatis via Vite) -->
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 text-gray-800 font-sans p-8">

    <!-- Navbar -->
    <nav class="w-full bg-red-600 text-white py-3 px-6 flex justify-between items-center rounded-lg shadow-md mb-6">
        <h1 class="font-bold text-lg">Aplikasi Laporan Keuangan</h1>
        <div class="space-x-4">
            <a href="{{ url('/') }}" class="hover:underline">📍 Geotagging</a>
            <a href="{{ route('laporan.index') }}" class="underline font-semibold">📊 Generate Excel</a>
        </div>
    </nav>

    <div class="flex justify-center">
        <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-xl text-center">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Generate Laporan Excel</h2>
            <p class="text-gray-600 mb-6">
                Klik tombol di bawah untuk mengunduh laporan keuangan dalam format <b>Excel (.xlsx)</b>.
            </p>

            <a href="{{ route('laporan.excel') }}"
               class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold text-lg transition duration-200">
                ⬇️ Download Excel
            </a>
        </div>
    </div>

    <div class="flex justify-center mt-10">
        <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-3xl">
            <h3 class="text-lg font-semibold mb-4 text-center">📘 Data Laporan Keuangan</h3>
            <table class="w-full border border-gray-300 rounded-lg text-sm">
                <thead class="bg-gray-200 text-gray-700 font-semibold">
                    <tr>
                        <th class="border px-3 py-2">No</th>
                        <th class="border px-3 py-2">Tanggal</th>
                        <th class="border px-3 py-2">Keterangan</th>
                        <th class="border px-3 py-2">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $index => $data)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2 text-center">{{ $index + 1 }}</td>
                            <td class="border px-3 py-2 text-center">{{ $data->tanggal }}</td>
                            <td class="border px-3 py-2">{{ $data->keterangan }}</td>
                            <td class="border px-3 py-2 text-right">{{ number_format($data->jumlah, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="border px-3 py-3 text-center text-gray-500">
                                Belum ada data laporan keuangan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
