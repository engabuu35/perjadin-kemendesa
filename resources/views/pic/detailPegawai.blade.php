<?php
session_start();

// Simulasi data pegawai (nanti bisa dari database)
$pegawai = [
    'namaPegawai' => 'Amanda Atika Putri',
    'nip' => '0000000000000',
    'jabatan' => 'Staff PPK',
    'namaUke1' => 'Inspektorat Jendral',
    'namaUke2' => 'Sekretariat Inspektorat Jendral',
    'pangkatGolongan' => 'III',
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'simpan') {
        $pegawai = [
            'namaPegawai' => htmlspecialchars($_POST['namaPegawai'] ?? ''),
            'nip' => htmlspecialchars($_POST['nip'] ?? ''),
            'jabatan' => htmlspecialchars($_POST['jabatan'] ?? ''),
            'namaUke1' => htmlspecialchars($_POST['namaUke1'] ?? ''),
            'namaUke2' => htmlspecialchars($_POST['namaUke2'] ?? ''),
            'pangkatGolongan' => htmlspecialchars($_POST['pangkatGolongan'] ?? ''),
        ];

        $_SESSION['pegawai'] = $pegawai;
        $_SESSION['success_message'] = 'Data berhasil disimpan!';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

if (isset($_SESSION['pegawai'])) {
    $pegawai = $_SESSION['pegawai'];
}

$successMessage = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPERDIN - Detail Pegawai</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        <?php include 'styles.css'; ?>
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

            <div class="page-header">
                <h2 class="page-title">Detail Pegawai</h2>
                <button class="btn-edit" onclick="toggleEdit()">Edit</button>
            </div>

            <div class="form-card">
                <form method="POST" action="" id="pegawaiForm">
                    <input type="hidden" name="action" value="simpan">
                    <div class="form-group">
                        <label>Nama Pegawai</label>
                        <input type="text" name="namaPegawai" value="<?= htmlspecialchars($pegawai['namaPegawai']) ?>" class="form-input" disabled>
                    </div>

                    <div class="form-group">
                        <label>NIP</label>
                        <input type="text" name="nip" value="<?= htmlspecialchars($pegawai['nip']) ?>" class="form-input" disabled>
                    </div>

                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" value="<?= htmlspecialchars($pegawai['jabatan']) ?>" class="form-input" disabled>
                    </div>

                    <div class="form-group">
                        <label>Nama UKE-1</label>
                        <input type="text" name="namaUke1" value="<?= htmlspecialchars($pegawai['namaUke1']) ?>" class="form-input" disabled>
                    </div>

                    <div class="form-group">
                        <label>Nama UKE-2</label>
                        <input type="text" name="namaUke2" value="<?= htmlspecialchars($pegawai['namaUke2']) ?>" class="form-input" disabled>
                    </div>

                    <div class="form-group">
                        <label>Pangkat Golongan</label>
                        <input type="text" name="pangkatGolongan" value="<?= htmlspecialchars($pegawai['pangkatGolongan']) ?>" class="form-input" disabled>
                    </div>
                </form>
            </div>

            <div class="form-actions" id="formActions">
                <button type="button" class="btn btn-cancel" onclick="cancelEdit()" disabled>Batal</button>
                <button type="submit" class="btn btn-save" form="pegawaiForm" disabled>Simpan</button>
            </div>
        </div>
    </div>

    <script>
        <?php include 'script.js'; ?>
    </script>
</body>
</html>
