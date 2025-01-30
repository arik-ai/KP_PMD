<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "db.php";
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Default tahun
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date("Y");

// Query untuk mendapatkan data Surat Masuk per bulan berdasarkan tahun
$sqlMasuk = "SELECT MONTH(tgl_surat) AS bulan, COUNT(*) AS jumlah 
             FROM surat_masuk 
             WHERE YEAR(tgl_surat) = $tahun 
             GROUP BY bulan";
$resultMasuk = $conn->query($sqlMasuk);

// Query untuk mendapatkan data Surat Keluar per bulan berdasarkan tahun
$sqlKeluar = "SELECT MONTH(tanggal_surat) AS bulan, COUNT(*) AS jumlah 
              FROM surat_keluar 
              WHERE YEAR(tanggal_surat) = $tahun 
              GROUP BY bulan";
$resultKeluar = $conn->query($sqlKeluar);

// Query untuk mendapatkan data Surat Perjanjian Kontrak per bulan berdasarkan tahun
$sqlKontrak = "SELECT MONTH(tgl_kontrak) AS bulan, COUNT(*) AS jumlah 
               FROM surat_kontrak 
               WHERE YEAR(tgl_kontrak) = $tahun 
               GROUP BY bulan";
$resultKontrak = $conn->query($sqlKontrak);
$sqlKeputusan = "SELECT MONTH(tgl_keputusan) AS bulan, COUNT(*) AS jumlah 
               FROM surat_keputusan 
               WHERE YEAR(tgl_keputusan) = $tahun 
               GROUP BY bulan";
$resultKeputusan = $conn->query($sqlKeputusan);

// Membuat data JSON untuk chart
$dataMasuk = array_fill(1, 12, 0); // Inisialisasi data kosong untuk 12 bulan
$dataKeluar = array_fill(1, 12, 0);
$dataKontrak = array_fill(1, 12, 0);
$dataKeputusan = array_fill(1, 12, 0); 
if ($resultMasuk->num_rows > 0) {
    while ($row = $resultMasuk->fetch_assoc()) {
        $dataMasuk[$row['bulan']] = $row['jumlah'];
    }
}

if ($resultKeluar->num_rows > 0) {
    while ($row = $resultKeluar->fetch_assoc()) {
        $dataKeluar[$row['bulan']] = $row['jumlah'];
    }
}

if ($resultKontrak->num_rows > 0) {
    while ($row = $resultKontrak->fetch_assoc()) {
        $dataKontrak[$row['bulan']] = $row['jumlah'];
    }
}
if ($resultKeputusan->num_rows > 0) {
    while ($row = $resultKeputusan->fetch_assoc()) {
        $dataKeputusan[$row['bulan']] = $row['jumlah'];
    }
}

// Query untuk mendapatkan daftar tahun (untuk dropdown)
$sqlTahun = "SELECT DISTINCT YEAR(tgl_surat) AS tahun FROM surat_masuk 
             UNION 
             SELECT DISTINCT YEAR(tanggal_surat) AS tahun FROM surat_keluar 
             UNION 
             SELECT DISTINCT YEAR(tgl_kontrak) AS tahun FROM surat_kontrak
             UNION
             SELECT DISTINCT YEAR(tgl_keputusan) AS tahun FROM surat_keputusan
             ORDER BY tahun";
$resultTahun = $conn->query($sqlTahun);

$listTahun = [];
if ($resultTahun->num_rows > 0) {
    while ($row = $resultTahun->fetch_assoc()) {
        $listTahun[] = $row['tahun'];
    }
}

// Menghitung total surat masuk, keluar, dan kontrak
$totalMasuk = array_sum($dataMasuk);
$totalKeluar = array_sum($dataKeluar);
$totalKontrak = array_sum($dataKontrak);
$totalKeputusan = array_sum($dataKeputusan);
$totalSemua = $totalMasuk + $totalKeluar + $totalKontrak + $totalKeputusan;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Grafik Surat Masuk-Keluar-Kontrak</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    h3 {
        text-align: center;
    }
    table {
        width: 50%;
        border-collapse: collapse;
        margin: 20px auto;
        background-color: white;
    }
    table thead {
        background-color: #34495e;
        color: white;
    }
    table th, table td {
        border: 1px solid #bdc3c7;
        padding: 10px;
        text-align: center;
    }
    table tbody tr:hover {
        background-color: #f2f2f2;
    }
    table tbody tr:last-child td {
        font-weight: bold;
    }
    .button-container {
        text-align: left;
        margin-top: 20px;
    }
    .button-container button {
        padding: 10px 20px;
        border: none;
        font-size: 16px;
    }
    .btn-success {
        background-color: #28a745;
        color: white;
        border-radius: 5px;
    }
    .btn-success:hover {
        background-color: #218838;
    }
    </style>
</head>

<body>
<div class="sidebar">
    <div class="logo">
        <img src="logo.png" alt="Logo" />
    </div>
    <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="surat_masuk.php" ><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="surat_perjanjian_kontrak.php"><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
            <li><a href="surat_keputusan.php"><span class="icon">📋</span> Surat Keputusan</a></li>
            <li><a href="surat_tugas.php"><span class="icon">📄</span> Surat Tugas</a></li>
            <li><a href="arsip.php"><span class="icon">📚</span> Arsip Surat</a></li>
            <li><a href="laporan.php"><span class="icon"  class="active">📊</span> Laporan</a></li>
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
         <h2>Laporan Surat Masuk, Keluar, dan Perjanjian Kontrak</h2>
        <form method="GET">
            <label for="tahun">Pilih Tahun:</label>
            <select id="tahun" name="tahun" onchange="this.form.submit()">
                <?php foreach ($listTahun as $t): ?>
                    <option value="<?= $t ?>" <?= $t == $tahun ? 'selected' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <canvas id="chartSurat" width="400" height="200"></canvas>

        <script>
            const dataMasuk = <?php echo json_encode(array_values($dataMasuk)); ?>;
            const dataKeluar = <?php echo json_encode(array_values($dataKeluar)); ?>;
            const dataKontrak = <?php echo json_encode(array_values($dataKontrak)); ?>;
            const dataKeputusan = <?php echo json_encode(array_values($dataKeputusan)); ?>;


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

        <h3>Total Surat</h3>
        <table>
            <thead>
                <tr>
                    <th>Jenis Surat</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Surat Masuk</td>
                    <td><?= $totalMasuk ?></td>
                </tr>
                <tr>
                    <td>Surat Keluar</td>
                    <td><?= $totalKeluar ?></td>
                </tr>
                <tr>
                    <td>Surat Perjanjian Kontrak</td>
                    <td><?= $totalKontrak ?></td>
                </tr>
                <tr>
                    <td>Surat Keputusan</td>
                    <td><?= $totalKeputusan ?></td>
                </tr>
                <tr>
                    <td><strong>Total Surat</strong></td>
                    <td><strong><?= $totalSemua ?></strong></td>
                </tr>
            </tbody>
        </table>
        <div class="button-container">
            <form action="cetak_laporan.php" method="POST" style="display: inline-block; margin-right: 10px;">
                <input type="hidden" name="tahun" value="<?= $tahun ?>">
                <button type="submit" class="btn btn-danger">Download PDF</button>
            </form>
            <form action="cetak_laporan_excel.php" method="POST" style="display: inline-block;">
                <input type="hidden" name="tahun" value="<?= $tahun ?>">
                <button type="submit" class="btn btn-success">Download Excel</button> <!-- Ganti dengan btn-success untuk warna hijau -->
            </form>
        </div>
    </div>
    
    </div>

</body>
</html>
