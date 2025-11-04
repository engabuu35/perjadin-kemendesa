<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Geotagging | Aplikasi Peta</title>

    <!-- Tailwind (otomatis jalan jika sudah di-setup via Vite) -->
    @vite('resources/css/app.css')

    <!-- Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans p-8">

    <!-- Navbar -->
    <nav class="w-full bg-red-600 text-white py-3 px-6 flex justify-between items-center rounded-lg shadow-md mb-6">
        <h1 class="font-bold text-lg">Aplikasi Geotagging</h1>
        <div class="space-x-4">
            <a href="{{ url('/') }}" class="hover:underline">📍 Geotagging</a>
            <a href="{{ route('laporan.index') }}" class="hover:underline">📊 Generate Excel</a>
        </div>
    </nav>

    <div class="flex flex-wrap justify-center gap-6">
        <!-- Peta -->
        <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4 text-center">Peta Lokasi</h2>
            <div id="map" class="h-80 rounded-lg mb-4"></div>
            <div class="text-center">
                <button id="geotag-btn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">
                    Tag Lokasi Saya
                </button>
                <p id="status" class="mt-3 text-sm text-gray-600"></p>
            </div>
        </div>

        <!-- Daftar Lokasi -->
        <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-sm">
            <h2 class="text-lg font-semibold mb-3 text-center">Lokasi Tersimpan</h2>
            <ul class="divide-y divide-gray-200">
                @forelse($locations as $location)
                    <li class="py-2 text-sm">📍 Lat: {{ $location->latitude }}, Lon: {{ $location->longitude }}</li>
                @empty
                    <li class="py-2 text-sm text-gray-500">Belum ada lokasi disimpan.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <script>
        const locations = @json($locations);
        const map = L.map('map').setView([-6.200000, 106.816666], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

        locations.forEach(loc => {
            L.marker([loc.latitude, loc.longitude]).addTo(map)
                .bindPopup(`Lokasi tersimpan:<br>${new Date(loc.created_at).toLocaleString()}`);
        });

        const geotagBtn = document.getElementById('geotag-btn');
        const statusEl = document.getElementById('status');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        geotagBtn.addEventListener('click', () => {
            statusEl.textContent = 'Mendeteksi lokasi Anda...';
            if (!navigator.geolocation) {
                statusEl.textContent = 'Geolocation tidak didukung browser.';
                return;
            }
            navigator.geolocation.getCurrentPosition(handleSuccess, handleError);
        });

        function handleSuccess(pos) {
            const { latitude, longitude } = pos.coords;
            statusEl.textContent = 'Lokasi ditemukan. Mengirim ke server...';
            fetch(`{{ route('locations.store') }}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ latitude, longitude })
            })
            .then(r => r.json())
            .then(data => {
                statusEl.textContent = data.success ? data.message : 'Gagal menyimpan.';
                if (data.success) setTimeout(() => location.reload(), 2000);
            })
            .catch(() => statusEl.textContent = 'Kesalahan jaringan.');
        }

        function handleError(e) { statusEl.textContent = e.message; }
    </script>
</body>
</html>
