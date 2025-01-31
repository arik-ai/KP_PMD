<?php 
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Ambil tahun saat ini
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date("Y");

// Query untuk mendapatkan data Surat berdasarkan tahun
$sqlMasuk = "SELECT MONTH(tgl_surat) AS bulan, COUNT(*) AS jumlah FROM surat_masuk WHERE YEAR(tgl_surat) = $tahun GROUP BY bulan";
$sqlKeluar = "SELECT MONTH(tanggal_surat) AS bulan, COUNT(*) AS jumlah FROM surat_keluar WHERE YEAR(tanggal_surat) = $tahun GROUP BY bulan";
$sqlKontrak = "SELECT MONTH(tgl_kontrak) AS bulan, COUNT(*) AS jumlah FROM surat_kontrak WHERE YEAR(tgl_kontrak) = $tahun GROUP BY bulan";
$sqlKeputusan = "SELECT MONTH(tgl_keputusan) AS bulan, COUNT(*) AS jumlah FROM surat_keputusan WHERE YEAR(tgl_keputusan) = $tahun GROUP BY bulan";
$sqlTugas = "SELECT MONTH(tgl_tugas) AS bulan, COUNT(*) AS jumlah FROM surat_tugas WHERE YEAR(tgl_tugas) = $tahun GROUP BY bulan";

$resultMasuk = $conn->query($sqlMasuk);
$resultKeluar = $conn->query($sqlKeluar);
$resultKontrak = $conn->query($sqlKontrak);
$resultKeputusan = $conn->query($sqlKeputusan);
$resultTugas = $conn->query($sqlTugas);

// Inisialisasi array data
$dataMasuk = array_fill(1, 12, 0);
$dataKeluar = array_fill(1, 12, 0);
$dataKontrak = array_fill(1, 12, 0);
$dataKeputusan = array_fill(1, 12, 0);
$dataTugas = array_fill(1, 12, 0);

while ($row = $resultMasuk->fetch_assoc()) {
    $dataMasuk[$row['bulan']] = $row['jumlah'];
}
while ($row = $resultKeluar->fetch_assoc()) {
    $dataKeluar[$row['bulan']] = $row['jumlah'];
}
while ($row = $resultKontrak->fetch_assoc()) {
    $dataKontrak[$row['bulan']] = $row['jumlah'];
}
while ($row = $resultKeputusan->fetch_assoc()) {
    $dataKeputusan[$row['bulan']] = $row['jumlah'];
}
while ($row = $resultTugas->fetch_assoc()) {
    $dataTugas[$row['bulan']] = $row['jumlah'];
}
$sqlInventaris = "SELECT COUNT(*) AS total_inven FROM inventaris";
$resultInven = $conn->query($sqlInventaris);
$totalInven = $resultInven->fetch_assoc()['total_inven'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin/Operator</title>
    <link rel="stylesheet" href="style.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        /* Sidebar styles */
        .containerbar{
            display: flex;
        }

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
        .welcome-container {
            text-align: center;
            width: 88%;
            margin-left: 50px;
            padding: 50px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .welcome-container h1 {
            font-size: 24px;
            color: #333;
        }

        .welcome-container p {
            font-size: 16px;
            color: #555;
        }
        .chartbar{
            text-align: center;
            width: 70%;
            margin-left: 210px;
            padding: 50px;
        }
        .row{
            background: #ffffff;
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
    <div class="containerbar">
                     <!-- Welcome Message -->
        <div class="welcome-container">
            <h1>Selamat Datang</h1>
            <p>Selamat datang di sistem Administrasi dan Inventaris Dinas Pemberdayaan Masyarakat dan Desa Kabupaten Pamekasan.</p>
            <div class="chartbar">
            <canvas id="chartSurat" width="400" height="200"></canvas>

<script>
    const dataMasuk = <?php echo json_encode(array_values($dataMasuk)); ?>;
    const dataKeluar = <?php echo json_encode(array_values($dataKeluar)); ?>;
    const dataKontrak = <?php echo json_encode(array_values($dataKontrak)); ?>;
    const dataKeputusan = <?php echo json_encode(array_values($dataKeputusan)); ?>;
    const dataTugas = <?php echo json_encode(array_values($dataTugas)); ?>;


    const labels = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni", 
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];

    const ctx = document.getElementById('chartSurat').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Surat Masuk',
                    data: dataMasuk,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgb(255, 0, 55)',
                    borderWidth: 1
                },
                {
                    label: 'Surat Keluar',
                    data: dataKeluar,
                    backgroundColor: 'rgba(96, 54, 235, 0.2)',
                    borderColor: 'rgb(0, 0, 255)',
                    borderWidth: 1
                },
                {
                    label: 'Surat Perjanjian Kontrak',
                    data: dataKontrak,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                },
                {
                    label: 'Surat Keputusan',
                    data: dataKeputusan,
                    backgroundColor: 'rgba(150, 233, 16, 0.2)',
                    borderColor: 'rgb(220, 233, 76)',
                    borderWidth: 1
                },
                {
                    label: 'Surat Tugas',
                    data: dataTugas,
                    backgroundColor: 'rgba(0, 255, 85, 0.2)',
                    borderColor: 'rgb(121, 236, 111)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: `Laporan Surat Tahun <?= $tahun ?>`
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</div>
    <div class="row">
        <h5>Jumlah Inventaris Dinas PMD</h5>
        <h6><?= $totalInven; ?> Barang</h6>
    </div>
</div>

    <div class="right-sidebar">
        <!-- Agenda Surat Masuk -->
        <div class="agenda-container">
            <div class="agenda-header">Agenda</div>
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
                <?php
                $query = "SELECT agenda_keluar, perihal_surat FROM surat_keluar WHERE agenda_keluar IS NOT NULL AND agenda_keluar != '0000-00-00' ORDER BY agenda_keluar ASC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $agenda_date = $row['agenda_keluar'];
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
                        echo '<div class="agenda-perihal">' . htmlspecialchars($row['perihal_surat']) . '</div>';
                        echo '</div>';
                        echo '</div>';  
                    }
                } else {
                    echo '<div class="empty-state">Belum ada agenda untuk surat keluar.</div>';
                }
                ?>
            </div>
            
    </div>
  
</body>
</html>
