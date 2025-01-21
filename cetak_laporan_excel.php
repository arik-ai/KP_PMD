<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require 'vendor/autoload.php'; // Autoload PhpSpreadsheet

include "db.php";
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil data tahun dari POST
$tahun = isset($_POST['tahun']) ? $_POST['tahun'] : date("Y");

// Query data Surat Masuk
$sqlMasuk = "SELECT MONTH(tgl_surat) AS bulan, COUNT(*) AS jumlah 
             FROM surat_masuk 
             WHERE YEAR(tgl_surat) = $tahun 
             GROUP BY bulan";
$resultMasuk = $conn->query($sqlMasuk);

// Query data Surat Keluar
$sqlKeluar = "SELECT MONTH(tanggal_surat) AS bulan, COUNT(*) AS jumlah 
              FROM surat_keluar 
              WHERE YEAR(tanggal_surat) = $tahun 
              GROUP BY bulan";
$resultKeluar = $conn->query($sqlKeluar);

// Data Surat Masuk
$dataMasuk = array_fill(1, 12, 0); // Inisialisasi 12 bulan
if ($resultMasuk->num_rows > 0) {
    while ($row = $resultMasuk->fetch_assoc()) {
        $dataMasuk[$row['bulan']] = $row['jumlah'];
    }
}

// Data Surat Keluar
$dataKeluar = array_fill(1, 12, 0);
if ($resultKeluar->num_rows > 0) {
    while ($row = $resultKeluar->fetch_assoc()) {
        $dataKeluar[$row['bulan']] = $row['jumlah'];
    }
}

// Membuat file Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Laporan Surat $tahun");

// Header
$sheet->setCellValue('A1', 'Bulan');
$sheet->setCellValue('B1', 'Surat Masuk');
$sheet->setCellValue('C1', 'Surat Keluar');
$sheet->setCellValue('D1', 'Total');

// Data Bulan
$bulan = [
    "Januari", "Februari", "Maret", "April", "Mei", "Juni", 
    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
];

for ($i = 1; $i <= 12; $i++) {
    $sheet->setCellValue("A" . ($i + 1), $bulan[$i - 1]);
    $sheet->setCellValue("B" . ($i + 1), $dataMasuk[$i]);
    $sheet->setCellValue("C" . ($i + 1), $dataKeluar[$i]);
    $sheet->setCellValue("D" . ($i + 1), $dataMasuk[$i] + $dataKeluar[$i]);
}

// Total Surat
$rowTotal = 14; // Setelah 12 bulan
$sheet->setCellValue('A' . $rowTotal, 'Total');
$sheet->setCellValue('B' . $rowTotal, array_sum($dataMasuk));
$sheet->setCellValue('C' . $rowTotal, array_sum($dataKeluar));
$sheet->setCellValue('D' . $rowTotal, array_sum($dataMasuk) + array_sum($dataKeluar));

// Style (opsional)
$sheet->getStyle('A1:D1')->getFont()->setBold(true);
$sheet->getStyle("A$rowTotal:D$rowTotal")->getFont()->setBold(true);

// Mengunduh file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=Laporan_Surat_$tahun.xlsx");
header('Cache-Control: max-age=0');

// Buat file Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>