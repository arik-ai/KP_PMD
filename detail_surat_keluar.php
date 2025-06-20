<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Periksa apakah ada parameter id di URL
if (isset($_GET['id'])) {
    $id = (int) $_GET['id']; // Pastikan ID adalah angka untuk keamanan

    // Query untuk mendapatkan detail surat perjanjian kontrak dan username penginput
    $sql = "SELECT surat_keluar.*, users.username 
            FROM surat_keluar 
            LEFT JOIN users ON surat_keluar.user_input_keluar = users.id
            WHERE surat_keluar.id_surat_keluar = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah ada data surat
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Surat tidak ditemukan.";
        exit;
    }
} else {
    echo "ID surat tidak ditemukan.";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Surat Keluar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="surat_masuk.php"><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php" class="active"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="surat_perjanjian_kontrak.php"><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
            <li><a href="surat_keputusan.php"><span class="icon">📋</span> Surat Keputusan</a></li>
            <li><a href="surat_tugas.php"><span class="icon">📄</span> Surat Tugas</a></li>
            <li><a href="arsip.php"><span class="icon">📚</span> Arsip Surat</a></li>
            <li><a href="laporan.php"><span class="icon">📊</span> Laporan</a></li>
            <li><a href="data_master.php"><span class="icon">⚙️</span> Data Master</a></li>
            <li><a href="logout.php"><span class="icon">🔒</span> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h2>Administrasi</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['role']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>

        <div class="container">
            <h2>Detail Surat Keluar</h2>

            <table class="table">
                <tr>
                    <th>No Surat</th>
                    <td><?= htmlspecialchars($row['no_surat']); ?></td>
                </tr>
                <tr>
                    <th>Perihal</th>
                    <td><?= htmlspecialchars($row['perihal_surat']); ?></td>
                </tr>
                <tr>
                    <th>Tanggal Surat</th>
                    <td><?= htmlspecialchars($row['tanggal_surat']); ?></td>
                </tr>
                <tr>
                    <th>Penerima</th>
                    <td><?= htmlspecialchars($row['penerima']); ?></td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td><?= htmlspecialchars($row['alamat']); ?></td>
                </tr>
                <tr>
                    <th>Sifat Surat</th>
                    <td><?= htmlspecialchars($row['nama_sifat_surat']); ?></td>
                </tr>
                <tr>
                    <th>Status Dokumen</th>
                    <td><?= empty($row['dokumen_surat']) ? 'Belum Upload' : 'Sudah Upload'; ?></td>
                </tr>
                <tr>
                    <th>Penginput</th>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                </tr>

            </table>

            <a href="surat_keluar.php" class="btn btn-primary">Kembali</a>
        </div>
    </div>

    <footer>
        <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>
