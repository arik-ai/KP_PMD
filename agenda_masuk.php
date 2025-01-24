<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Agenda</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .agenda-container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .agenda-header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 1.2rem;
        }

        .agenda-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .agenda-item:last-child {
            border-bottom: none;
        }

        .agenda-icon {
            background-color: #b71c1c;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            border-radius: 5px;
            padding: 10px;
            margin-right: 10px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .agenda-details {
            flex: 1;
        }

        .agenda-date {
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .agenda-perihal {
            color: #555;
            font-size: 0.85rem;
        }

        .empty-state {
            text-align: center;
            padding: 20px;
            font-size: 1rem;
            color: #777;
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
            <li><a href="surat_keluar.php"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="agenda.php" class="active"><span class="icon">📅</span> Agenda</a></li>
            <li><a href="arsip.php"><span class="icon">📚</span> Arsip Surat</a></li>
            <li><a href="laporan.php"><span class="icon">📊</span> Laporan</a></li>
            <li><a href="data_master.php"><span class="icon">⚙️</span> Data Master</a></li>
            <li><a href="logout.php"><span class="icon">🔒</span> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h2>Pengingat Agenda</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['role']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>

        <div class="agenda-container">
            <div class="agenda-header">Pengingat Agenda</div>
            <div class="agenda-list">
                <?php
                // Query untuk mengambil data agenda dan perihal dari tabel surat_masuk
                $query = "SELECT agenda, perihal FROM surat_masuk WHERE agenda IS NOT NULL AND agenda != '0000-00-00' ORDER BY agenda ASC";
                $result = mysqli_query($conn, $query);

                // Periksa apakah ada data
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $tanggal = date('d', strtotime($row['agenda']));
                        $bulan = date('F', strtotime($row['agenda']));
                        echo '<div class="agenda-item">';
                        echo '<div class="agenda-icon">' . htmlspecialchars($tanggal) . '</div>';
                        echo '<div class="agenda-details">';
                        echo '<div class="agenda-date">' . htmlspecialchars($bulan) . '</div>';
                        echo '<div class="agenda-perihal">' . htmlspecialchars($row['perihal']) . '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="empty-state">Belum ada agenda yang akan dilaksanakan.</div>';
                }

                // Tutup koneksi database
                mysqli_close($conn);
                ?>
            </div>
        </div>
    </div>
</body>
</html>
