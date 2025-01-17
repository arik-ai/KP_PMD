<?php
// cetak_dokumen.php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil nama file dokumen berdasarkan ID
    $query = "SELECT dokumen FROM surat_masuk WHERE id_surat = $id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $dokumen = $row['dokumen'];

        // Path file dokumen
        $file_path = __DIR__ . '/uploads/' . $dokumen;

        // Debugging path dan dokumen
        echo "Nama dokumen: " . $dokumen . "<br>";
        echo "Path file: " . $file_path . "<br>";

        if (!empty($dokumen) && file_exists($file_path)) {
            // Header untuk mengunduh file
            $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
            $mime_types = [
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'txt' => 'text/plain',
            ];

            $mime_type = $mime_types[strtolower(pathinfo($file_path, PATHINFO_EXTENSION))] ?? 'application/octet-stream';

            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
        } else {
            echo "File tidak ditemukan di path: " . $file_path;
        }
    } else {
        echo "Data dokumen tidak ditemukan untuk ID: " . $id;
    }
} else {
    echo "ID tidak valid.";
}
?>
