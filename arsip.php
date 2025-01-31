<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Query untuk menghitung jumlah surat masuk
$sqlSuratMasuk = "SELECT COUNT(*) AS total_masuk FROM surat_masuk";
$resultMasuk = $conn->query($sqlSuratMasuk);
$totalMasuk = $resultMasuk->fetch_assoc()['total_masuk'];

// Query untuk menghitung jumlah surat keluar
$sqlSuratKeluar = "SELECT COUNT(*) AS total_keluar FROM surat_keluar";
$resultKeluar = $conn->query($sqlSuratKeluar);
$totalKeluar = $resultKeluar->fetch_assoc()['total_keluar'];

// Query untuk menghitung jumlah surat perjanjian kontrak
$sqlSuratKontrak = "SELECT COUNT(*) AS total_kontrak FROM surat_kontrak";
$resultKontrak = $conn->query($sqlSuratKontrak);
$totalKontrak = $resultKontrak->fetch_assoc()['total_kontrak'];

// Query untuk menghitung jumlah surat keputusan
$sqlSuratKeputusan = "SELECT COUNT(*) AS total_keputusan FROM surat_keputusan";
$resultKeputusan = $conn->query($sqlSuratKeputusan);
$totalKeputusan = $resultKeputusan->fetch_assoc()['total_keputusan'];

// Query untuk menghitung jumlah surat tugas
$sqlSuratTugas = "SELECT COUNT(*) AS total_tugas FROM surat_tugas";
$resultTugas = $conn->query($sqlSuratTugas);
$totalTugas = $resultTugas->fetch_assoc()['total_tugas'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Surat</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
            border-top: 1px solid #ddd;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            flex: 1;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            background-color: #ffffff;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .card h5 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #007bff;
        }

        .card p {
            font-size: 18px;
            margin: 0;
        }

        .card[data-link] {
            text-decoration: none;
            color: inherit;
        }

        .card[data-link]:hover {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="surat_masuk.php"><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="surat_perjanjian_kontrak.php"><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
            <li><a href="surat_keputusan.php"><span class="icon">📋</span> Surat Keputusan</a></li>
            <li><a href="surat_tugas.php"><span class="icon">📄</span> Surat Tugas</a></li>
            <li><a href="arsip.php" class="active"><span class="icon">📚</span> Arsip Surat</a></li>
            <li><a href="laporan.php"><span class="icon">📊</span> Laporan</a></li>
            <li><a href="data_master.php"><span class="icon">⚙️</span> Data Master</a></li>
            <li><a href="logout.php"><span class="icon">🔒</span> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <h2>Administrasi</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['role']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>

        <!-- Content -->
        <div class="container">
            <h2>Arsip Surat</h2>
            <div class="row">
                <div class="card" onclick="window.location.href='arsip_masuk.php'">
                    <h5>Jumlah Surat Masuk</h5>
                    <p><?= $totalMasuk; ?> surat</p>
                </div>
                <div class="card" onclick="window.location.href='arsip_keluar.php'">
                    <h5>Jumlah Surat Keluar</h5>
                    <p><?= $totalKeluar; ?> surat</p>
                </div>
                <div class="card" onclick="window.location.href='arsip_kontrak.php'">
                    <h5>Jumlah Surat Perjanjian Kontrak</h5>
                    <p><?= $totalKontrak; ?> surat</p>
                </div>
            </div>
            <div class="row" style="justify-content: center;">
                <div class="card" onclick="window.location.href='arsip_keputusan.php'">
                    <h5>Jumlah Surat Keputusan</h5>
                    <p><?= $totalKeputusan; ?> surat</p>
                </div>
                <div class="card" onclick="window.location.href='arsip_tugas.php'">
                    <h5>Jumlah Surat Tugas</h5>
                    <p><?= $totalTugas; ?> surat</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>
