<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aplikasi Geotagging</title>
    
    <!-- Library Leaflet.js untuk Peta Interaktif -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            background-color: #f0f2f5;
            padding: 20px;
            gap: 20px;
            flex-wrap: wrap;
        }
        .main-container {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }
        .list-container {
            width: 100%;
            max-width: 400px;
        }
        h1, h2 {
            color: #333;
            text-align: center;
        }
        /* Style untuk peta */
        #map { 
            height: 350px; 
            width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .action-area {
            text-align: center;
        }
        .geotag-btn {
            background-color: #e53935; /* Warna merah khas Laravel */
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .geotag-btn:hover {
            background-color: #c62828;
        }
        #status {
            margin-top: 15px;
            font-weight: 500;
            color: #555;
            min-height: 20px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <div class="main-container">
        <h1>Peta Lokasi</h1>
        <div id="map"></div>
        <div class="action-area">
            <button id="geotag-btn" class="geotag-btn">Tag Lokasi Saya Saat Ini</button>
            <p id="status"></p>
        </div>
    </div>

    <div class="main-container list-container">
        <h2>Lokasi Tersimpan</h2>
        <ul id="location-list">
            @forelse($locations as $location)
                <li>Lat: {{ $location->latitude }}, Lon: {{ $location->longitude }}</li>
            @empty
                <li>Belum ada lokasi yang disimpan.</li>
            @endforelse
        </ul>
    </div>

    <script>
        // Mengubah data lokasi dari PHP ke JavaScript
        const locations = @json($locations);

        // Inisialisasi Peta, berpusat di Jakarta
        const map = L.map('map').setView([-6.200000, 106.816666], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Tambahkan marker untuk setiap lokasi yang sudah ada
        locations.forEach(loc => {
            L.marker([loc.latitude, loc.longitude]).addTo(map)
                .bindPopup(`Lokasi tersimpan pada:<br>${new Date(loc.created_at).toLocaleString()}`)
                .openPopup();
        });

        // Ambil elemen dari HTML
        const geotagBtn = document.getElementById('geotag-btn');
        const statusEl = document.getElementById('status');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Tambahkan event saat tombol diklik
        geotagBtn.addEventListener('click', () => {
            statusEl.textContent = 'Mencari lokasi Anda...';

            if (!navigator.geolocation) {
                statusEl.textContent = 'Geolocation tidak didukung oleh browser Anda.';
                return;
            }

            navigator.geolocation.getCurrentPosition(handleSuccess, handleError);
        });

        // Fungsi jika berhasil mendapatkan lokasi
        function handleSuccess(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            statusEl.textContent = `Lokasi ditemukan! Mengirim ke server...`;
            sendLocationToBackend(latitude, longitude);
        }

        // Fungsi jika gagal mendapatkan lokasi
        function handleError(error) {
            statusEl.textContent = `Error: ${error.message}`;
        }

        // Fungsi untuk mengirim data lokasi ke backend Laravel
        function sendLocationToBackend(latitude, longitude) {
            fetch(`{{ route('locations.store') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusEl.textContent = data.message;
                    // Muat ulang halaman setelah 2 detik untuk melihat pin baru
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    statusEl.textContent = 'Gagal menyimpan lokasi.';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statusEl.textContent = 'Terjadi kesalahan jaringan.';
            });
        }
    </script>
</body>
</html>
