<?php
// Koneksi ke database
include 'db.php';

// Ambil parameter filter dari URL
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$filterYear = isset($_GET['year']) ? (int)$_GET['year'] : '';
$filterMonth = isset($_GET['month']) ? (int)$_GET['month'] : '';

// Menyiapkan kondisi pencarian dan filter
$conditions = "1";  // Default kondisi jika tidak ada filter
$params = [];
$paramTypes = "";

// Kondisi pencarian
if ($searchQuery !== '') {
    $conditions .= " AND (nomor_surat LIKE ? OR perihal LIKE ? OR pengirim LIKE ? OR nama_sifat_surat LIKE ?)";
    $searchWildcard = "%$searchQuery%";
    array_push($params, $searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard);
    $paramTypes .= "ssss";
}

// Kondisi filter tahun
if ($filterYear) {
    $conditions .= " AND YEAR(tgl_surat) = ?";
    array_push($params, $filterYear);
    $paramTypes .= "i";
}

// Kondisi filter bulan
if ($filterMonth) {
    $conditions .= " AND MONTH(tgl_surat) = ?";
    array_push($params, $filterMonth);
    $paramTypes .= "i";
}

// Query untuk mengambil data surat masuk sesuai dengan filter
$query = "SELECT * FROM surat_masuk WHERE $conditions";
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Header untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Arsip_Surat_Masuk.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Output data ke Excel
echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>No. Surat</th>
        <th>Perihal</th>
        <th>Tanggal Surat</th>
        <th>Diterima Tanggal</th>
        <th>Instansi Pengirim</th>
        <th>Sifat</th>
        <th>Dokumen</th>
    </tr>";

// Jika ada data, tampilkan dalam tabel Excel
if ($result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['nomor_surat']}</td>
                <td>{$row['perihal']}</td>
                <td>{$row['tgl_surat']}</td>
                <td>{$row['terima_tanggal']}</td>
                <td>{$row['pengirim']}</td>
                <td>{$row['nama_sifat_surat']}</td>
                <td>{$row['dokumen']}</td>
            </tr>";
        $no++;
    }
}
echo "</table>";
?>
