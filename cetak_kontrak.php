<?php

include 'db.php';

$uploadDirectory = 'uploads/';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id']; 

    $sql = "SELECT dokumen_kontrak FROM surat_kontrak WHERE id_kontrak = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filePath = $row['dokumen_kontrak'];
       
        if (empty($filePath)) {
            echo "<script>alert('File tidak ada.'); window.location.href='surat_kontrak.php';</script>";
            exit;
        }

        $fullFilePath = $uploadDirectory . $filePath;

        if (file_exists($fullFilePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($fullFilePath) . '"');
            header('Content-Length: ' . filesize($fullFilePath));
            header('Pragma: no-cache');
            header('Expires: 0');
            readfile($fullFilePath); 
            exit;
        } else {
            echo "<script>alert('File tidak ditemukan.'); window.location.href='surat_kontrak.php';</script>";
        }
    } else {
        echo "<script>alert('ID dokumen tidak valid.'); window.location.href='surat_kontrak.php';</script>";
    }
} else {
    echo "<script>alert('Tidak ada dokumen yang dipilih.'); window.location.href='surat_kontrak.php';</script>";
}
?>
