<?php
session_start();

// Simpan data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomorSurat = $_POST['nomor_surat'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $tujuan = $_POST['tujuan'] ?? '';
    $pegawai = $_POST['pegawai'] ?? [];

    $_SESSION['penugasan'] = [
        'nomor_surat' => htmlspecialchars($nomorSurat),
        'tanggal' => htmlspecialchars($tanggal),
        'tujuan' => htmlspecialchars($tujuan),
        'pegawai' => array_map(function ($p) {
            return array_map('htmlspecialchars', $p);
        }, $pegawai),
    ];

    $success = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPERDIN - Penugasan Perjalanan Dinas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        <?php include 'styles.css'; ?>
        
        /* Override atau tambahan style khusus untuk halaman penugasan */
        .pegawai-section {
            margin-top: 30px;
        }
        
        .pegawai-section h3 {
            color: #3D2D6E;
            font-size: 20px;
            margin-bottom: 20px;
        }
        
        .pegawai-card {
            background: #f1f5ff;
            border: 1px solid #c9d7ff;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            position: relative;
        }
        
        .hapus-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff6b6b;
            border: none;
            border-radius: 8px;
            padding: 8px 15px;
            cursor: pointer;
            color: white;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .hapus-btn:hover {
            background: #ff5252;
        }
        
        .add-btn {
            border-radius: 10px;
            border: 2px dashed #2954B0;
            color: #2954B0;
            font-weight: 700;
            background: white;
            padding: 12px 20px;
            margin-top: 15px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
        }
        
        .add-btn:hover {
            background: #f0f5ff;
            border-color: #1e3d8f;
        }
        
        .form-actions-penugasan {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .form-actions-penugasan .btn {
            flex: 1;
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
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    Data penugasan berhasil disimpan!
                </div>
            <?php endif; ?>

            <div class="page-header">
                <h2 class="page-title">Penugasan Perjalanan Dinas</h2>
            </div>

            <div class="form-card">
                <form method="POST" id="penugasanForm">
                    <div class="form-group">
                        <label>Nomor Surat Tugas</label>
                        <input type="text" name="nomor_surat" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label>Tujuan</label>
                        <input type="text" name="tujuan" class="form-input" required>
                    </div>

                    <div class="pegawai-section">
                        <h3>Daftar Pegawai</h3>
                        <div id="pegawaiList">
                            <div class="pegawai-card">
                                <button type="button" class="hapus-btn" onclick="hapusPegawai(this)">Hapus</button>
                                <div class="form-group">
                                    <label>NIP</label>
                                    <input type="text" name="pegawai[0][nip]" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="pegawai[0][nama]" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label>Nomor Telepon</label>
                                    <input type="text" name="pegawai[0][telepon]" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="pegawai[0][email]" class="form-input">
                                </div>
                            </div>
                        </div>

                        <button type="button" class="add-btn" onclick="tambahPegawai()">+ Tambah Pegawai</button>
                    </div>

                    <div class="form-actions-penugasan">
                        <button type="button" class="btn btn-cancel" onclick="window.location.reload()">Batal</button>
                        <button type="submit" class="btn btn-save">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        <?php include 'script.js'; ?>
        
        let pegawaiCount = 1;

        function tambahPegawai() {
            const list = document.getElementById('pegawaiList');
            const div = document.createElement('div');
            div.className = 'pegawai-card';
            div.innerHTML = `
                <button type="button" class="hapus-btn" onclick="hapusPegawai(this)">Hapus</button>
                <div class="form-group">
                    <label>NIP</label>
                    <input type="text" name="pegawai[${pegawaiCount}][nip]" class="form-input">
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="pegawai[${pegawaiCount}][nama]" class="form-input">
                </div>
                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" name="pegawai[${pegawaiCount}][telepon]" class="form-input">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="pegawai[${pegawaiCount}][email]" class="form-input">
                </div>
            `;
            list.appendChild(div);
            pegawaiCount++;
        }

        function hapusPegawai(btn) {
            btn.parentElement.remove();
        }
    </script>

</body>
</html>