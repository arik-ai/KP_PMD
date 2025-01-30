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

// Query data Surat Perjanjian Kontrak
$sqlKontrak = "SELECT MONTH(tgl_kontrak) AS bulan, COUNT(*) AS jumlah 
               FROM surat_kontrak 
               WHERE YEAR(tgl_kontrak) = $tahun 
               GROUP BY bulan";
$resultKontrak = $conn->query($sqlKontrak);

// Query data Surat Keputusan
$sqlKeputusan = "SELECT MONTH(tgl_keputusan) AS bulan, COUNT(*) AS jumlah 
               FROM surat_keputusan 
               WHERE YEAR(tgl_keputusan) = $tahun 
               GROUP BY bulan";
$resultKeputusan = $conn->query($sqlKeputusan);

// Inisialisasi array data untuk 12 bulan
$dataMasuk = array_fill(1, 12, 0);
$dataKeluar = array_fill(1, 12, 0);
$dataKontrak = array_fill(1, 12, 0);
$dataKeputusan = array_fill(1, 12, 0);

// Isi array dengan hasil query
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

// Membuat file Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Laporan Surat $tahun");

// Tambahkan Judul di Atas Tabel
$sheet->setCellValue('A1', "LAPORAN JUMLAH SURAT TAHUN $tahun");
$sheet->mergeCells('A1:F1'); // Gabungkan sel untuk judul
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12); // Ukuran lebih besar & bold
$sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Posisi tengah

// Header Tabel
$sheet->setCellValue('A2', 'Bulan');
$sheet->setCellValue('B2', 'Surat Masuk');
$sheet->setCellValue('C2', 'Surat Keluar');
$sheet->setCellValue('D2', 'Surat Kontrak');
$sheet->setCellValue('E2', 'Surat Keputusan');
$sheet->setCellValue('F2', 'Total');

// Data Bulan
$bulan = [
    "Januari", "Februari", "Maret", "April", "Mei", "Juni", 
    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
];

for ($i = 1; $i <= 12; $i++) {
    $sheet->setCellValue("A" . ($i + 2), $bulan[$i - 1]);
    $sheet->setCellValue("B" . ($i + 2), $dataMasuk[$i]);
    $sheet->setCellValue("C" . ($i + 2), $dataKeluar[$i]);
    $sheet->setCellValue("D" . ($i + 2), $dataKontrak[$i]);
    $sheet->setCellValue("E" . ($i + 2), $dataKeputusan[$i]);
    $sheet->setCellValue("F" . ($i + 2), $dataMasuk[$i] + $dataKeluar[$i] + $dataKontrak[$i] + $dataKeputusan[$i]);
} 

// Total Surat
$rowTotal = 16; // Setelah 12 bulan + header
$sheet->setCellValue('A' . $rowTotal, 'Total');
$sheet->setCellValue('B' . $rowTotal, array_sum($dataMasuk));
$sheet->setCellValue('C' . $rowTotal, array_sum($dataKeluar));
$sheet->setCellValue('D' . $rowTotal, array_sum($dataKontrak));
$sheet->setCellValue('E' . $rowTotal, array_sum($dataKeputusan));
$sheet->setCellValue('F' . $rowTotal, array_sum($dataMasuk) + array_sum($dataKeluar) + array_sum($dataKontrak) + array_sum($dataKeputusan));

// Style (opsional)
$sheet->getStyle('A2:F2')->getFont()->setBold(true);
$sheet->getStyle("A$rowTotal:F$rowTotal")->getFont()->setBold(true);

// Mengunduh file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=Laporan_Surat_$tahun.xlsx");
header('Cache-Control: max-age=0');

// Buat file Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
