<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; margin: 40px; background-color: #f4f4f9; }
        .container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .table-wrapper { 
            overflow-x: auto; /* Kunci untuk membuat tabel bisa di-scroll ke samping */
            margin-top: 20px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            min-width: 1000px; /* Lebar minimum tabel diperbesar agar scrolling terlihat */
        }
        th, td { padding: 12px 15px; border: 1px solid #ddd; text-align: left; white-space: nowrap; } /* white-space: nowrap penting untuk scrolling */
        th { background-color: #e53935; color: white; }
        tr:nth-of-type(even) { background-color: #f9f9f9; }
        .btn-excel {
            display: inline-block;
            background-color: #1d6f42; /* Warna hijau untuk Excel */
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Data Laporan Keuangan</h1>
        <a href="{{ route('laporan.excel') }}" class="btn-excel">Generate Excel</a>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>NIP</th>
                        <th>Uang Harian (Rp)</th>
                        <th>Biaya Penginapan (Rp)</th>
                        <th>Transport (Rp)</th>
                        <th>Nama Hotel</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_pegawai }}</td>
                            <td>{{ $item->nip }}</td>
                            <td>{{ number_format($item->uang_harian, 2, ',', '.') }}</td>
                            <td>{{ number_format($item->biaya_penginapan, 2, ',', '.') }}</td>
                            <td>{{ number_format($item->transport, 2, ',', '.') }}</td>
                            <td>{{ $item->nama_hotel }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center;">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

