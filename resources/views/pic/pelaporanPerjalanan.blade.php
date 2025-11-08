<?php
session_start();


// Simulasi data pelaporan
if (!isset($_SESSION['pelaporan'])) {
    $pelaporan = [
        'nomor_surat' => 'ST/001/2025',
        'tujuan' => 'Jakarta',
        'biaya' => [
            ['no' => 1, 'kategori' => 'Transport', 'bukti' => 'Tiket Pesawat', 'biaya' => 'Rp 2.500.000'],
            ['no' => 2, 'kategori' => 'Akomodasi', 'bukti' => 'Hotel Invoice', 'biaya' => 'Rp 1.500.000'],
            ['no' => 3, 'kategori' => 'Konsumsi', 'bukti' => 'Struk Makan', 'biaya' => 'Rp 500.000'],
        ],
        'catatan' => '',
        'status' => '' 
    ];
} else {
    $pelaporan = $_SESSION['pelaporan'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'tolak') {
            $pelaporan['status'] = 'ditolak';
            $_SESSION['pelaporan'] = $pelaporan;
            $_SESSION['info_message'] = 'Pelaporan ditolak. Silakan isi catatan.';
        } elseif ($_POST['action'] === 'setujui') {
            $pelaporan['status'] = 'disetujui';
            $_SESSION['pelaporan'] = $pelaporan;
            $_SESSION['success_message'] = 'Pelaporan berhasil disetujui!';
        } elseif ($_POST['action'] === 'kirim_catatan') {
            $pelaporan['catatan'] = htmlspecialchars($_POST['catatan'] ?? '');
            $pelaporan['status'] = 'catatan_terkirim';
            $_SESSION['pelaporan'] = $pelaporan;
            $_SESSION['success_message'] = 'Catatan berhasil dikirim!';
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Jika bukan dari session, ambil data default
if (!isset($_SESSION['pelaporan'])) {
    $pelaporan = [
        'nomor_surat' => 'ST/001/2025',
        'tujuan' => 'Jakarta',
        'biaya' => [
            ['no' => 1, 'kategori' => 'Transport', 'bukti' => 'Tiket Pesawat', 'biaya' => 'Rp 2.500.000'],
            ['no' => 2, 'kategori' => 'Akomodasi', 'bukti' => 'Hotel Invoice', 'biaya' => 'Rp 1.500.000'],
            ['no' => 3, 'kategori' => 'Konsumsi', 'bukti' => 'Struk Makan', 'biaya' => 'Rp 500.000'],
        ],
        'catatan' => '',
        'status' => '' 
    ];
} else {
    $pelaporan = $_SESSION['pelaporan'];
}

$successMessage = $_SESSION['success_message'] ?? '';
$infoMessage = $_SESSION['info_message'] ?? '';
unset($_SESSION['success_message']);
unset($_SESSION['info_message']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPERDIN - Pelaporan Perjalanan Dinas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        <?php include 'styles.css'; ?>
        
        /* Style khusus untuk tabel biaya */
        .biaya-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        
        .biaya-table th {
            background: #2954B0;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #2954B0;
        }
        
        .biaya-table td {
            padding: 12px;
            border: 1px solid #ddd;
            background: white;
        }
        
        .biaya-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        /* Style untuk textarea catatan */
        textarea.form-input {
            min-height: 120px;
            padding: 12px;
            resize: vertical;
            font-family: 'Poppins', sans-serif;
        }
        
        /* Style untuk tombol tolak */
        .btn-reject {
            background: #9CA3AF;
            color: white;
        }
        
        .btn-reject:hover {
            background: #6B7280;
        }
        
        /* Info message style */
        .alert-info {
            background: #e3f2fd;
            border: 1px solid #90caf9;
            color: #1565c0;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://api.builder.io/api/v1/image/assets/TEMP/f14558b691a5583af0d306cff110d512631e6f64?width=2880" 
             alt="" class="bg-image">

        <nav class="navbar">
            <div class="navbar-content">
                <div class="navbar-left">
                    <button class="menu-btn" onclick="toggleMenu()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <h1 class="logo">SIPERDIN</h1>
                </div>
                <div class="navbar-right">
                    <span class="user-name">Raza Anu</span>
                    <div class="user-icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <?php if ($successMessage): ?>
            <div class="alert alert-success">
                <?= $successMessage ?>
            </div>
            <?php endif; ?>

            <?php if ($infoMessage): ?>
            <div class="alert-info">
                <?= $infoMessage ?>
            </div>
            <?php endif; ?>

            <div class="page-header">
                <h2 class="page-title">Pelaporan Perjalanan Dinas</h2>
                <button class="btn-back" onclick="window.history.back()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <div class="form-card">
                <div class="form-group">
                    <label>Nomor Surat Tugas</label>
                    <input type="text" value="<?= htmlspecialchars($pelaporan['nomor_surat']) ?>" class="form-input" disabled>
                </div>

                <div class="form-group">
                    <label>Tujuan</label>
                    <input type="text" value="<?= htmlspecialchars($pelaporan['tujuan']) ?>" class="form-input" disabled>
                </div>

                <div class="form-group">
                    <label>Biaya</label>
                    <table class="biaya-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Bukti</th>
                                <th>Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pelaporan['biaya'] as $item): ?>
                            <tr>
                                <td><?= $item['no'] ?></td>
                                <td><?= htmlspecialchars($item['kategori']) ?></td>
                                <td><?= htmlspecialchars($item['bukti']) ?></td>
                                <td><?= htmlspecialchars($item['biaya']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($pelaporan['status'] === 'ditolak'): ?>
                <!--Tampilkan Catatan dengan tombol Kirim -->
                <form method="POST" action="">
                    <input type="hidden" name="action" value="kirim_catatan">
                    <div class="form-group">
                        <label>Catatan:</label>
                        <textarea name="catatan" class="form-input" placeholder="Tulis catatan penolakan di sini..."><?= htmlspecialchars($pelaporan['catatan']) ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-save">Kirim</button>
                    </div>
                </form>
                <?php elseif ($pelaporan['status'] === 'disetujui' || $pelaporan['status'] === 'catatan_terkirim'): ?>
                <!-- Setelah disetujui atau catatan terkirim -->
                <div class="alert alert-success">
                    <?= ($pelaporan['status'] === 'disetujui') ? 'Pelaporan telah disetujui!' : 'Catatan telah terkirim!' ?>
                </div>
                <?php else: ?>
                <!--  Tolak dan Setujui -->
                <div class="form-actions">
                    <form method="POST" action="" style="flex: 1;">
                        <input type="hidden" name="action" value="tolak">
                        <button type="submit" class="btn btn-reject">Tolak</button>
                    </form>
                    <form method="POST" action="" style="flex: 1;">
                        <input type="hidden" name="action" value="setujui">
                        <button type="submit" class="btn btn-save">Setujui</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        <?php include 'script.js'; ?>
    </script>
</body>
</html>