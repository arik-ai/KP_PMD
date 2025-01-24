<?php
session_start();
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM inventaris WHERE id_inventaris = $id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='data_inven.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Inventaris</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="data_inven.php"><span class="icon">🛒</span> Data Inventaris</a></li>
            <li><a href="logout.php"><span class="icon">🔒</span> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h2>Inventaris</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['role']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>

        <div class="container">
            <h2>Detail Inventaris</h2>
            <table class="table">
                <tr>
                    <th>Kode Barang</th>
                    <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                </tr>
                <tr>
                    <th>Waktu Pengadaan</th>
                    <td><?= htmlspecialchars($row['waktu_pengadaan']); ?></td>
                </tr>
                <tr>
                    <th>Nama Barang</th>
                    <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                </tr>
                <tr>
                    <th>Stok</th>
                    <td><?= htmlspecialchars($row['stok']); ?></td>
                </tr>
                <tr>
                    <th>Lokasi Barang</th>
                    <td><?= htmlspecialchars($row['lokasi_barang']); ?></td>
                </tr>
                <tr>
                    <th>Kondisi Barang</th>
                    <td><?= htmlspecialchars($row['kondisi_barang']); ?></td>
                </tr>
                <tr>
                    <th>Foto Barang</th>
                    <td>
                        <?php
                        $filePath = 'uploads/' . $row['foto_barang'];
                        if (!empty($row['foto_barang']) && file_exists($filePath)): ?>
                            <img src="<?= htmlspecialchars($filePath); ?>" alt="Foto Barang" width="200">
                        <?php else: ?>
                            Tidak ada foto
                        <?php endif; ?>
                    </td>

                </tr>
            </table>
        </
