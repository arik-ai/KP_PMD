<?php
require 'vendor/autoload.php'; // Pastikan path-nya benar

use Dompdf\Dompdf;

// Mulai sesi dan koneksi database
session_start();
include "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Ambil tahun dari POST
$tahun = isset($_POST['tahun']) ? $_POST['tahun'] : date("Y");

// Query untuk data surat masuk
$sqlMasuk = "SELECT MONTH(tgl_surat) AS bulan, COUNT(*) AS jumlah 
             FROM surat_masuk 
             WHERE YEAR(tgl_surat) = $tahun 
             GROUP BY bulan";
$resultMasuk = $conn->query($sqlMasuk);

// Query untuk data surat keluar
$sqlKeluar = "SELECT MONTH(tanggal_surat) AS bulan, COUNT(*) AS jumlah 
              FROM surat_keluar 
              WHERE YEAR(tanggal_surat) = $tahun 
              GROUP BY bulan";
$resultKeluar = $conn->query($sqlKeluar);

// Data surat masuk dan keluar per bulan
$dataMasuk = array_fill(1, 12, 0);
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

// Total surat masuk, keluar, dan semua
$totalMasuk = array_sum($dataMasuk);
$totalKeluar = array_sum($dataKeluar);
$totalSemua = $totalMasuk + $totalKeluar;

// Template untuk PDF
$html = "
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Surat Masuk dan Keluar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        h1, h3 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        table thead {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Laporan Surat Masuk dan Keluar</h1>
    <h3>Tahun $tahun</h3>

    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Surat Masuk</th>
                <th>Surat Keluar</th>
            </tr>
        </thead>
        <tbody>";

// Daftar bulan
$bulan = [
    1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April",
    5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus",
    9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"
];

foreach ($bulan as $key => $namaBulan) {
    $html .= "<tr>
                <td>$namaBulan</td>
                <td>{$dataMasuk[$key]}</td>
                <td>{$dataKeluar[$key]}</td>
              </tr>";
}

$html .= "
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>$totalMasuk</strong></td>
                <td><strong>$totalKeluar</strong></td>
            </tr>
        </tbody>
    </table>

    <p><strong>Total Surat:</strong> $totalSemua</p>
</body>
</html>";

// Inisialisasi DOMPDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Opsional) Set ukuran kertas dan orientasi
$dompdf->setPaper('A4', 'portrait');

// Render HTML menjadi PDF
$dompdf->render();

// Kirim file PDF ke browser
$dompdf->stream("laporan_surat_$tahun.pdf", ["Attachment" => true]);
?>
