<?php

include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 

    // Ambil nama file dokumen berdasarkan ID dari database
    $query = "SELECT dokumen_surat FROM surat_keluar WHERE id_surat_keluar = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $dokumen = $row['dokumen_surat'];

        // Periksa apakah nama file sudah mencakup subfolder 'uploads/'
        if (strpos($dokumen, 'uploads/') === 0) {
            $file_path = __DIR__ . '/' . $dokumen; 
        } else {
            $file_path = __DIR__ . '/uploads/' . $dokumen; 
        }

        if (!empty($dokumen) && file_exists($file_path)) {
            // Header untuk mengunduh file
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

            $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
            $mime_type = $mime_types[$file_extension] ?? 'application/octet-stream';

            // Kirim header untuk unduhan
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        } else {
            // Gunakan alert untuk menampilkan pesan jika file tidak ditemukan
            echo "<script>alert('File surat tidak ditemukan.'); window.location.href = 'surat_keluar.php';</script>";
        }
    } else {
        // Gunakan alert untuk menampilkan pesan jika data dokumen tidak ditemukan
        echo "<script>alert('Data dokumen tidak ditemukan untuk ID: " . $id . "'); window.location.href = 'surat_keluar.php';</script>";
    }
} else {
    // Gunakan alert untuk menampilkan pesan jika ID tidak valid
    echo "<script>alert('ID tidak valid.'); window.location.href = 'surat_keluar.php';</script>";
}
?>
