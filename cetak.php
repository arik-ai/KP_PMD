<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit;
}

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
            // Gunakan alert untuk menampilkan pesan jika file tidak ditemukan
            echo "<script>alert('File surat tidak ditemukan.'); window.location.href = 'surat_masuk.php';</script>";
        }
    } else {
        // Gunakan alert untuk menampilkan pesan jika data dokumen tidak ditemukan
        echo "<script>alert('Data dokumen tidak ditemukan untuk ID: " . $id . "'); window.location.href = 'surat_masuk.php';</script>";
    }
} else {
    // Gunakan alert untuk menampilkan pesan jika ID tidak valid
    echo "<script>alert('ID tidak valid.'); window.location.href = 'surat_masuk.php';</script>";
}
?>
