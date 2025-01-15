<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Tangkap ID surat keluar
if (!isset($_GET['id'])) {
    header("Location: surat_keluar.php?message=invalid_id");
    exit;
}

$idSuratKeluar = intval($_GET['id']); // Sanitasi input

// Proses unggah file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file_surat']['tmp_name'];
        $fileName = $_FILES['file_surat']['name'];
        $fileSize = $_FILES['file_surat']['size'];
        $fileType = $_FILES['file_surat']['type'];

        // Hapus validasi ekstensi file, atau Anda bisa menambahkan beberapa ekstensi yang ingin dibatasi
        // $allowedExtensions = ['pdf', 'doc', 'docx'];
        // $fileNameCmps = explode(".", $fileName);
        // $fileExtension = strtolower(end($fileNameCmps));
        // if (!in_array($fileExtension, $allowedExtensions)) {
        //     $errorMsg = "Ekstensi file tidak diizinkan. Hanya file PDF, DOC, atau DOCX yang diperbolehkan.";
        //     exit;
        // } else {
            // Gunakan nama asli file yang diunggah
            $newFileName = $fileName;

            // Tentukan folder tujuan penyimpanan
            $uploadFileDir = 'uploads/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            $destPath = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Simpan informasi file ke database
                $query = "UPDATE surat_keluar SET dokumen_surat = ? WHERE id_surat_keluar = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("si", $destPath, $idSuratKeluar);

                if ($stmt->execute()) {
                    header("Location: surat_keluar.php?message=upload_success");
                    exit;
                } else {
                    $errorMsg = "Gagal menyimpan informasi file ke database.";
                }
            } else {
                $errorMsg = "Gagal memindahkan file ke direktori tujuan.";
            }
        // }
    } else {
        $errorMsg = "Tidak ada file yang diunggah atau terjadi kesalahan dalam proses unggah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unggah File Surat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Unggah File Surat</h2>
        <?php if (isset($errorMsg)): ?>
            <div class="error-message"><?= htmlspecialchars($errorMsg); ?></div>
        <?php endif; ?>
        <form action="upload.php?id=<?= htmlspecialchars($idSuratKeluar); ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file_surat">Pilih File:</label>
                <input type="file" name="file_surat" id="file_surat" required>
            </div>
            <button type="submit" class="btn btn-primary">Unggah</button>
            <a href="surat_keluar.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html> 
