<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
include 'db.php';

// Ambil nomor surat dari URL
$no_surat = isset($_GET['no_surat']) ? mysqli_real_escape_string($conn, $_GET['no_surat']) : null;

if (!$no_surat) {
    echo "Nomor surat tidak ditemukan.";
    exit;
}

// Ambil data surat berdasarkan nomor surat
$query = "SELECT * FROM surat_keluar WHERE no_surat = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $no_surat);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Data surat tidak ditemukan.";
    exit;
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Surat Keluar</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .detail-container {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #ffffff;
        }
        .detail-table th, .detail-table td {
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .detail-table th {
            color:rgb(2, 2, 2);
            font-size: 16px;
        }
        .detail-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .detail-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 20px;
            font-size: 14px;
            text-decoration: none;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
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
            <li><a href="arsip.php"><span class="icon">📚</span> Arsip Surat</a></li>
            <li><a href="laporan.php"><span class="icon">📊</span> Laporan</a></li>
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
            <div class="detail-container">
                <div class="detail-header">
                    <h2>Detail Surat Keluar</h2>
                    <p>Informasi lengkap mengenai surat keluar.</p>
                </div>
                <table class="detail-table">
                    <tr>
                        <th>No Surat</th>
                        <td><?= htmlspecialchars($data['no_surat']); ?></td>
                    </tr>
                    <tr>
                        <th>Perihal</th>
                        <td><?= htmlspecialchars($data['perihal_surat']); ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Surat</th>
                        <td><?= htmlspecialchars($data['tanggal_surat']); ?></td>
                    </tr>
                    <tr>
                        <th>Penerima</th>
                        <td><?= htmlspecialchars($data['penerima']); ?></td>
                    </tr>
                    <tr>
                        <th>Sifat Surat</th>
                        <td><?= htmlspecialchars($data['sifat_surat']); ?></td>
                    </tr>
                </table>
                <a href="surat_keluar.php" class="btn">Kembali ke Data Surat Keluar</a>
            </div>
        </div>
    </div>

    <footer>
    <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>
