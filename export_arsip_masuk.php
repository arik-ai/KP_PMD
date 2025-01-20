<?php
// Koneksi ke database
include 'db.php';

// Header untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Arsip Surat Masuk.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Ambil data inventaris
$query = "SELECT * FROM surat_masuk";
$result = $conn->query($query);

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
                <td>{$row['sifat']}</td>
                <td>{$row['dokumen']}</td>
            </tr>";
        $no++;
    }
}
echo "</table>";
?>
