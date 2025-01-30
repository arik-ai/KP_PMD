<?php 
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, arahkan ke halaman login
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
    <title>Dashboard Admin/Operator</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        /* Sidebar styles */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #ffffff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100; /* Ensure sidebar is above content */
        }

        .sidebar h5 {
            font-size: 18px;
            margin-bottom: 30px;
            color: #333;
        }

        .sidebar a {
            text-decoration: none;
            color: #333;
            display: block;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar a.active, .sidebar a:hover {
            background-color: #007bff;
            color: #ffffff;
        }

        /* Content styles */
        .content {
            margin-left: 270px; /* Account for sidebar width */
            padding: 20px;
            text-align: center;
            width: calc(100% - 270px); /* Prevent overlap */
            margin-top: 30px; /* Add space for the topbar */
        }

        /* Topbar styles */
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 10px 20px;
            z-index: 200; /* Ensure it stays on top */
        }

        .topbar h2 {
            font-size: 24px;
            color: #333;
            margin: 0;
        }

        .profile {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            font-size: 14px;
        }

        /* Right sidebar styles */
        .right-sidebar {
            width: 300px;
            position: fixed;
            right: 0;
            background-color: #ffffff;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            height: 100vh;
            overflow-y: auto;
            padding-top: 20px; /* Adjust for the header in right sidebar */
        }

        /* Agenda container styles */
        .agenda-container {
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .agenda-header {
            background-color: rgb(148, 181, 211);
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

        .agenda-icon.past {
            background-color: #ddd;
            color: #888;
        }

        .agenda-icon.active {
            background-color: #4CAF50;
            color: white;
        }

        .agenda-icon.default {
            background-color: rgb(155, 199, 253);
            color: white;
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

        /* Footer styles */
        footer {
            margin-top: 30px; /* Ensure footer is spaced from content */
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="logo.png" alt="Administrasi" class="img-fluid">
        <ul class="sidebar-menu">
            <li><a href="index.php" class="active"><span class="icon">🏠</span> DASHBOARD</a></li>
            <li><a href="surat_masuk.php"><span class="icon">📂</span>ADMINISTRASI</a></li>
            <li><a href="data_inven.php"><span class="icon">🛒</span>INVENTARIS</a></li>
            <li><a href="logout.php"><span class="icon">🔒</span> LOGOUT</a></li>
        </ul>
    </div>
    <div class="right-sidebar">
        <!-- Agenda Surat Masuk -->
        <div class="agenda-container">
            <div class="agenda-header">Agenda Surat Masuk</div>
            <div class="agenda-list">
                <?php
                $query = "SELECT agenda, perihal FROM surat_masuk WHERE agenda IS NOT NULL AND agenda != '0000-00-00' ORDER BY agenda ASC";
                $result = mysqli_query($conn, $query);
                $today = date('Y-m-d');

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $agenda_date = $row['agenda'];
                        $tanggal = date('d', strtotime($agenda_date));
                        $bulan = date('F', strtotime($agenda_date));

                        $icon_class = "default";
                        $item_class = "";

                        if ($agenda_date < $today) {
                            $icon_class = "past";
                            $item_class = "past";
                        } elseif ($agenda_date == $today) {
                            $icon_class = "active";
                            $item_class = "active";
                        }

                        echo '<div class="agenda-item ' . $item_class . '">';
                        echo '<div class="agenda-icon ' . $icon_class . '">' . htmlspecialchars($tanggal) . '</div>';
                        echo '<div class="agenda-details">';
                        echo '<div class="agenda-date">' . htmlspecialchars($bulan) . '</div>';
                        echo '<div class="agenda-perihal">' . htmlspecialchars($row['perihal']) . '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="empty-state">Belum ada agenda untuk surat masuk.</div>';
                }
                ?>
            </div>
        </div>

        <!-- Agenda Surat Perjanjian Kontrak -->
        <div class="agenda-container">
            <div class="agenda-header">Agenda Surat Perjanjian Kontrak</div>
            <div class="agenda-list">
                <?php
                $query = "SELECT agenda_kontrak, perihal_kontrak FROM surat_kontrak WHERE agenda_kontrak IS NOT NULL AND agenda_kontrak != '0000-00-00' ORDER BY agenda_kontrak ASC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $agenda_date = $row['agenda_kontrak'];
                        $tanggal = date('d', strtotime($agenda_date));
                        $bulan = date('F', strtotime($agenda_date));

                        $icon_class = "default";
                        $item_class = "";

                        if ($agenda_date < $today) {
                            $icon_class = "past";
                            $item_class = "past";
                        } elseif ($agenda_date == $today) {
                            $icon_class = "active";
                            $item_class = "active";
                        }

                        echo '<div class="agenda-item ' . $item_class . '">';
                        echo '<div class="agenda-icon ' . $icon_class . '">' . htmlspecialchars($tanggal) . '</div>';
                        echo '<div class="agenda-details">';
                        echo '<div class="agenda-date">' . htmlspecialchars($bulan) . '</div>';
                        echo '<div class="agenda-perihal">' . htmlspecialchars($row['perihal_kontrak']) . '</div>';
                        echo '</div>';
                        echo '</div>';  
                    }
                } else {
                    echo '<div class="empty-state">Belum ada agenda untuk surat perjanjian kontrak.</div>';
                }
                ?>
            </div>
</body>
</html>
