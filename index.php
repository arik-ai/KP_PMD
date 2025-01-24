<?php 
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit;
}
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #ffffff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            position: fixed;
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
        .content {
            margin-left: 270px;
            padding: 20px;
            text-align: center;
        }
        .content h3 {
            font-size: 30px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .content p {
            font-size: 16px;
            color: #666;
            line-height: 1.5;
            text-align: center;
        }
        /* CSS untuk Administrasi */
        .card.administrasi {
            background-color: white;
            border: 50px solidrgb(255, 255, 255);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%; /* Membuat tinggi sama dengan kontainer */
            width: 300px;
            margin-bottom: 10px; /* Menambahkan margin ke bawah untuk memisahkan card */
        }

        .card.administrasi, .card.inventaris {
            background-color: white;
            border: 50px solidrgb(255, 255, 255);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 300px;
            margin: 0 auto; /* Menengah card ke tengah */
            margin-bottom: 10px; /* Menambahkan margin ke bawah untuk memisahkan card */
        }

        .card.administrasi img, .card.inventaris img {
            width: 60%;
            max-height: 150px;
            margin: 0 auto;
        }

        .card.administrasi .card-title, .card.inventaris .card-title {
            color: black;
            font-weight: bold;
        }


        .hidden {
            display: none;
        }
        footer {
            margin-top: 240px;
            margin-left: 0px;
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
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <h2>DINAS PEMBERDAYAAN MASYARAKAT DAN DESA</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['role']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>
    </div>
    <div class="content">
        <!-- Dashboard Content -->
        <div id="dashboard-content">
            <h3>Selamat Datang di Sistem Administrasi dan Inventori</h3>
            
            </div>



            <footer>
                <p>https://dpmd.pamekasankab.go.id/</p>
            </footer>
        </div>
</body>
</html>
