<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Tangkap ID surat kontrak
if (!isset($_GET['id'])) {
    header("Location: surat_perjanjian_kontrak.php?message=invalid_id");
    exit;
}

$idSuratKontrak = intval($_GET['id']); // Sanitasi input

// Cek apakah file sudah pernah diunggah
$queryCheck = "SELECT dokumen_kontrak FROM surat_kontrak WHERE id_kontrak = ?";
$stmtCheck = $conn->prepare($queryCheck);
$stmtCheck->bind_param("i", $idSuratKontrak);
$stmtCheck->execute();
$stmtCheck->bind_result($existingFile);
$stmtCheck->fetch();
$stmtCheck->close();

if ($existingFile) {
    $errorMsg = "File sudah diunggah sebelumnya. Tidak dapat mengunggah ulang.";
} else {
    // Proses unggah file
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['file_surat']['tmp_name'];
            $fileName = $_FILES['file_surat']['name'];
            $fileSize = $_FILES['file_surat']['size'];
            $fileType = $_FILES['file_surat']['type'];

            // Memeriksa ekstensi file
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $allowedExtensions = ['pdf'];

            // Memeriksa tipe MIME file
            $allowedMimeTypes = ['application/pdf'];

            if (!in_array(strtolower($fileExtension), $allowedExtensions) || !in_array($fileType, $allowedMimeTypes)) {
                $errorMsg = "Hanya file PDF yang diperbolehkan.";
            } else {
                // Tentukan folder tujuan penyimpanan
                $uploadFileDir = 'uploads/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }

                $destPath = $uploadFileDir . $fileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // Simpan nama file saja ke database
                    $query = "UPDATE surat_kontrak SET dokumen_kontrak = ? WHERE id_kontrak = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("si", $fileName, $idSuratKontrak);

                    if ($stmt->execute()) {
                        header("Location: surat_perjanjian_kontrak.php?message=upload_success");
                        exit;
                    } else {
                        $errorMsg = "Gagal menyimpan informasi file ke database.";
                    }
                } else {
                    $errorMsg = "Gagal memindahkan file ke direktori tujuan.";
                }
            }
        } else {
            $errorMsg = "Tidak ada file yang diunggah atau terjadi kesalahan dalam proses unggah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unggah File Surat Kontrak</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Menghilangkan border pada tombol */
        button {
            border: none;
            outline: none; /* Menghilangkan outline jika ada */
            background-color: #3498db; /* Ubah sesuai warna yang diinginkan */
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px; /* Agar tombol terlihat melengkung */
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2980b9; /* Warna tombol saat hover */
        }

    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo" />
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="surat_masuk.php" ><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="surat_perjanjian_kontrak.php" class="active"><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
            <li><a href="surat_keputusan.php"><span class="icon">📋</span> Surat Keputusan</a></li>
            <li><a href="surat_tugas.php"><span class="icon">📄</span> Surat Tugas</a></li>
            <li><a href="arsip.php"><span class="icon">📚</span> Arsip Surat</a></li>
            <li><a href="laporan.php"><span class="icon">📊</span> Laporan</a></li>
            <li><a href="data_master.php"><span class="icon">⚙️</span> Data Master</a></li>
            <li><a href="logout.php"><span class="icon">🔒</span> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <h2>Administrasi</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['role']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>
        <div class="container">
            <h2>Upload File Surat Kontrak (.PDF)</h2>
            <?php if (isset($errorMsg)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($errorMsg); ?>
                    <a href="surat_perjanjian_kontrak.php" class="btn btn-secondary">Kembali</a>
                </div>
            <?php else: ?>
                <form action="upload_kontrak.php?id=<?= htmlspecialchars($idSuratKontrak); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="file_surat">Pilih File:</label>
                        <input type="file" name="file_surat" id="file_surat" required accept=".PDF">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                    <a href="surat_perjanjian_kontrak.php" class="btn btn-secondary">Kembali</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
