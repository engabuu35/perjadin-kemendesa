<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; } /* Font yang mendukung banyak karakter */
        h1 { text-align: center; }
        p { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>Dicetak pada: {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>NIP</th>
                <th>Uang Harian (Rp)</th>
                <th>Biaya Penginapan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nama_pegawai }}</td>
                <td>{{ $item->nip }}</td>
                <td>{{ number_format($item->uang_harian, 2, ',', '.') }}</td>
                <td>{{ number_format($item->biaya_penginapan, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
