<?php
// Koneksi ke database
include 'db.php';

// Header untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Arsip Surat Keluar.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Ambil data inventaris
$query = "SELECT * FROM surat_keluar";
$result = $conn->query($query);

// Output data ke Excel
echo "<table border='1'>";
echo "<tr>
                        <th>No</th>
                        <th>No. Surat</th>
                        <th>Perihal</th>
                        <th>Tanggal Surat</th>
                        <th>Penerima Surat</th>
                        <th>Sifat</th>
                        <th>Dokumen</th>
    </tr>";

if ($result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['no_surat']}</td>
                <td>{$row['perihal_surat']}</td>
                <td>{$row['tanggal_surat']}</td>
                <td>{$row['penerima']}</td>
                <td>{$row['sifat_surat']}</td>
                <td>{$row['dokumen_surat']}</td>
            </tr>";
        $no++;
    }
}
echo "</table>";
?>
