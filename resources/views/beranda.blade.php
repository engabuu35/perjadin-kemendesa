<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio Saya</title>
    <style>
        /* Menggunakan font yang umum ada di sistem */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            background-color: #f4f7f6;
            color: #333;
            line-height: 1.6;
        }

        /* Wadah utama untuk memusatkan konten */
        .container {
            max-width: 960px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header dengan latar belakang dan bayangan */
        .header {
            background-color: #ffffff;
            padding: 20px 40px;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        
        /* Navigasi */
        nav a {
            text-decoration: none;
            color: #555;
            margin-left: 20px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #e53935; /* Warna aksen merah seperti di welcome page Laravel */
        }
        
        /* Bagian Hero (Jumbotron) */
        .hero {
            background-color: #2c3e50;
            color: #ffffff;
            text-align: center;
            padding: 80px 20px;
        }

        .hero h2 {
            margin: 0 0 10px 0;
            font-size: 48px;
        }

        .hero p {
            font-size: 20px;
            opacity: 0.9;
        }

        /* Section untuk proyek */
        .projects {
            padding: 40px 0;
        }

        .projects h3 {
            text-align: center;
            font-size: 32px;
            margin-bottom: 40px;
            color: #2c3e50;
        }

        /* Grid untuk kartu proyek */
        .project-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        /* Tampilan kartu proyek */
        .card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        .card-content {
            padding: 25px;
        }

        .card h4 {
            margin-top: 0;
            color: #e53935;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: #fff;
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <header class="header">
        <h1>Proyek Keren Ganteng</h1>
        <nav>
            <a href="#">Beranda</a>
            <a href="#">Tentang</a>
            <a href="#">Kontak</a>
        </nav>
    </header>

    <section class="hero">
        <h2>Selamat Datang!</h2>
        <p>Ini adalah halaman untuk menampilkan semua proyek luar biasa yang sedang saya kerjakan.</p>
    </section>

    <main class="container">
        <section class="projects">
            <h3>Proyek Terbaru</h3>
            <div class="project-grid">
                <div class="card">
                    <div class="card-content">
                        <h4>Sistem Informasi SETIS MEMANG ASIK</h4>
                        <p>Aplikasi web untuk manajemen data siswa, guru, dan jadwal pelajaran. Dibangun dengan Laravel dan Vue.js.</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <h4>Website Toko Online</h4>
                        <p>Platform e-commerce untuk penjualan produk fashion dengan integrasi pembayaran online.</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <h4>Aplikasi Catatan Pribadi</h4>
                        <p>Aplikasi sederhana untuk mencatat ide dan tugas harian. Fokus pada antarmuka yang bersih dan cepat.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2025 - Hak Cipta Dilindungi</p>
    </footer>

</body>
</html>