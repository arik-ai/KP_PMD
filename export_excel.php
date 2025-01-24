<?php
include 'db.php';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mendapatkan nilai filter dan pencarian
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$lokasiBarang = isset($_GET['lokasi_barang']) ? trim($_GET['lokasi_barang']) : '';

// Susun query berdasarkan filter dan pencarian
$whereClauses = [];
$params = [];
$paramTypes = '';

if (!empty($searchQuery)) {
    $whereClauses[] = "(nama_barang LIKE ? OR stok LIKE ? OR lokasi_barang LIKE ? OR kondisi_barang LIKE ?)";
    $searchWildcard = "%$searchQuery%";
    $params = array_merge($params, [$searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard]);
    $paramTypes .= 'ssss';
}

if (!empty($lokasiBarang)) {
    $whereClauses[] = "lokasi_barang = ?";
    $params[] = $lokasiBarang;
    $paramTypes .= 's';
}

$whereSql = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$dataSql = "SELECT * FROM inventaris $whereSql";
$stmt = $conn->prepare($dataSql);

if ($params) {
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Header untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment;filename=\"data_inventaris.xls\"");
header("Cache-Control: max-age=0");

// Mulai output HTML tabel
echo "<table border='1'>";

// Tambahkan judul lokasi
if (!empty($lokasiBarang)) {
    echo "<tr><th colspan='7'>Data Inventaris untuk Lokasi: " . htmlspecialchars($lokasiBarang) . "</th></tr>";
} else {
    echo "<tr><th colspan='7'>Data Inventaris untuk Semua Lokasi</th></tr>";
}

echo "<thead>";
echo "<tr>";
echo "<th>No</th>";
echo "<th>Kode Barang</th>";
echo "<th>Waktu Pengadaan</th>";
echo "<th>Nama Barang</th>";
echo "<th>Stok</th>";
echo "<th>Lokasi Barang</th>";
echo "<th>Kondisi Barang</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

// Cetak data
$no = 1;
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . htmlspecialchars($row['kode_barang']) . "</td>";
    echo "<td>" . htmlspecialchars($row['waktu_pengadaan']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_barang']) . "</td>";
    echo "<td>" . htmlspecialchars($row['stok']) . "</td>";
    echo "<td>" . htmlspecialchars($row['lokasi_barang']) . "</td>";
    echo "<td>" . htmlspecialchars($row['kondisi_barang']) . "</td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";

$conn->close();
?>
