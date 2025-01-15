<?php
// Koneksi ke database
include 'db.php';

// Header untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_inventaris.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Ambil data inventaris
$query = "SELECT * FROM inventaris";
$result = $conn->query($query);

// Output data ke Excel
echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Nama Barang</th>
        <th>Stok</th>
        <th>Lokasi Barang</th>
        <th>Kondisi Barang</th>
    </tr>";

if ($result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['nama_barang']}</td>
                <td>{$row['stok']}</td>
                <td>{$row['lokasi_barang']}</td>
                <td>{$row['kondisi_barang']}</td>
            </tr>";
        $no++;
    }
}
echo "</table>";
?>
