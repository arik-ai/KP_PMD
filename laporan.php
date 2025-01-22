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



// Membuat data JSON untuk chart
$dataMasuk = array_fill(1, 12, 0); // Inisialisasi data kosong untuk 12 bulan
$dataKeluar = array_fill(1, 12, 0);

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

// Query untuk mendapatkan daftar tahun (untuk dropdown)
$sqlTahun = "SELECT DISTINCT YEAR(tgl_surat) AS tahun FROM surat_masuk 
             UNION 
             SELECT DISTINCT YEAR(tanggal_surat) AS tahun FROM surat_keluar 
             ORDER BY tahun";
$resultTahun = $conn->query($sqlTahun);

$listTahun = [];
if ($resultTahun->num_rows > 0) {
    while ($row = $resultTahun->fetch_assoc()) {
        $listTahun[] = $row['tahun'];
    }
}
// Menghitung total surat masuk dan keluar
$totalMasuk = array_sum($dataMasuk);
$totalKeluar = array_sum($dataKeluar);
$totalSemua = $totalMasuk + $totalKeluar;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Grafik Surat Masuk-Keluar</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    /* Gaya Tabel */
    h3{
        text-align: center;
    }
    table {
        width: 50%;
        border-collapse: collapse;
        margin: 20px auto; /* Memastikan tabel berada di tengah */
        background-color: white;
    }

    table thead {
        background-color: #34495e;
        color: white;
    }

    table th,
    table td {
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
    margin-top: 20px; /* Menambah jarak dari konten sebelumnya */
    }

    .button-container form {
        display: inline-block; /* Pastikan form ditampilkan dalam satu baris */
        margin-right: 10px; /* Memberikan jarak antar tombol */
    }

    .button-container button {
        padding: 10px 20px; /* Menambah padding tombol untuk tampilan yang lebih baik */
        border: none;
        font-size: 16px; /* Menyesuaikan ukuran font tombol */
    }
    
/* Styling untuk tombol Download Excel dengan warna hijau */
    .btn-success {
        background-color: #28a745; /* Warna hijau */
        color: white; /* Teks putih */
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px; /* Agar tombol terlihat sedikit melengkung */
    }

    .btn-success:hover {
        background-color: #218838; /* Warna hijau yang lebih gelap saat hover */
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
        <li><a href="surat_masuk.php"><span class="icon">📂</span> Data Surat Masuk</a></li>
        <li><a href="surat_keluar.php"><span class="icon">📤</span> Data Surat Keluar</a></li>
        <li><a href="surat_perjanjian_kontrak.php"><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
        <li><a href="surat_keputusan.php"><span class="icon">📋</span> Surat Keputusan</a></li>
        <li><a href="surat_tugas.php"><span class="icon">📄</span> Surat Tugas</a></li>
        <li><a href="arsip.php"><span class="icon">📚</span> Arsip Surat</a></li>
        <li><a href="laporan.php" class="active"><span class="icon">📊</span> Laporan</a></li>
        <li><a href="data_master.php"><span class="icon">⚙️</span> Data Master</a></li>
        <li><a href="logout.php"><span class="icon">🔒</span> Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="topbar">
        <h2>Administrasi</h2>
        <div class="profile">
            <span><?= htmlspecialchars($_SESSION['role']); ?></span>
            <div class="profile-icon">👤</div>
        </div>
    </div>
    <div class="container">
        <h2>Laporan Surat Masuk dan Keluar</h2>

        <!-- Dropdown untuk memilih tahun -->
        <form method="GET">
            <label for="tahun">Pilih Tahun:</label>
            <select id="tahun" name="tahun" onchange="this.form.submit()">
                <?php foreach ($listTahun as $t): ?>
                    <option value="<?= $t ?>" <?= $t == $tahun ? 'selected' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Canvas untuk Chart -->
        <canvas id="chartSurat" width="400" height="200"></canvas>

        <script>
            // Data dari PHP ke JavaScript
            const dataMasuk = <?php echo json_encode(array_values($dataMasuk)); ?>;
            const dataKeluar = <?php echo json_encode(array_values($dataKeluar)); ?>;

            // Label bulan
            const labels = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni", 
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];

            // Konfigurasi Chart.js
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
                            text: `Laporan Surat Masuk dan Keluar Tahun <?= $tahun ?>`
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

        <!-- Tabel untuk menampilkan data surat masuk dan keluar -->
        <h3> Total Surat</h3>
        <table border="1" cellspacing="0" cellpadding="5">
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
