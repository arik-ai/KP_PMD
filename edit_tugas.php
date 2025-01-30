<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username']) || !isset($_SESSION['id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit;
}

include 'db.php'; // Koneksi ke database

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil ID tugas dari URL
if (isset($_GET['id'])) {
    $id_tugas = $_GET['id'];

    // Query untuk mendapatkan data tugas berdasarkan id
    $query = "SELECT * FROM surat_tugas WHERE id_tugas = '$id_tugas'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "Data tidak ditemukan.";
        exit;
    }
}

// Proses update data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $no_tugas = mysqli_real_escape_string($conn, $_POST['no_tugas']);
    $perihal_tugas = mysqli_real_escape_string($conn, $_POST['perihal_tugas']);
    $tgl_tugas = mysqli_real_escape_string($conn, $_POST['tgl_tugas']);
    $agenda_tugas = mysqli_real_escape_string($conn, $_POST['agenda_tugas']);
    
    // Menangani upload file jika ada
    $dokumen_tugas = '';
    if (isset($_FILES['dokumen_tugas']) && $_FILES['dokumen_tugas']['error'] == 0) {
        $targetDir = "uploads/"; // Folder tempat menyimpan file
        $fileName = basename($_FILES['dokumen_tugas']['name']);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Validasi file
        $allowedTypes = ['pdf', 'doc', 'docx']; // Format yang diperbolehkan
        if (in_array($fileType, $allowedTypes)) {
            // Pindahkan file ke folder tujuan
            if (move_uploaded_file($_FILES['dokumen_tugas']['tmp_name'], $targetFile)) {
                $dokumen_tugas = $fileName;
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
        } else {
            echo "Sorry, only PDF, DOC, and DOCX files are allowed.";
            exit;
        }
    }

    // Query untuk update data tugas
    $updateQuery = "UPDATE surat_tugas SET no_tugas = '$no_tugas', perihal_tugas = '$perihal_tugas', 
                    tgl_tugas = '$tgl_tugas', agenda_tugas = '$agenda_tugas'";

    // Jika ada file dokumen, tambahkan pada query update
    if ($dokumen_tugas != '') {
        $updateQuery .= ", dokumen_tugas = '$dokumen_tugas'";
    }

    $updateQuery .= " WHERE id_tugas = '$id_tugas'";

    // Eksekusi query
    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Data berhasil diupdate!'); window.location.href='surat_tugas.php';</script>";
    } else {
        echo "Error: " . $updateQuery . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Surat tugas</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Remove the underline from the document link */
        .document-link {
            text-decoration: none;
            color: #000; /* Optional: You can adjust the color to match your design */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="surat_masuk.php" ><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="surat_perjanjian_kontrak.php"><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
            <li><a href="surat_tugas.php" ><span class="icon">📋</span> Surat Keputusan</a></li>
            <li><a href="surat_tugas.php"  class="active"><span class="icon">📄</span> Surat Tugas</a></li>
            <li><a href="arsip.php"><span class="icon">📚</span> Arsip Surat</a></li>
            <li><a href="laporan.php"><span class="icon">📊</span> Laporan</a></li>
            <li><a href="data_master.php"><span class="icon">⚙️</span> Data Master</a></li>
            <li><a href="logout.php"><span class="icon">🔒</span> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h2>Administrasi</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['role']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>

        <div class="container">
            <h2>Edit Surat tugas</h2>
            <form action="" method="post" class="form-container" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="no_tugas">No Surat tugas</label>
                        <input type="text" id="no_tugas" name="no_tugas" value="<?= htmlspecialchars($row['no_tugas']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="perihal_tugas">Perihal</label>
                        <input type="text" id="perihal_tugas" name="perihal_tugas" value="<?= htmlspecialchars($row['perihal_tugas']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tgl_tugas">Tanggal Surat</label>
                        <input type="date" id="tgl_tugas" name="tgl_tugas" value="<?= htmlspecialchars($row['tgl_tugas']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="agenda_tugas">Tanggal Agenda</label>
                        <input type="date" id="agenda_tugas" name="agenda_tugas" value="<?= htmlspecialchars($row['agenda_tugas']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="dokumen_tugas">Dokumen tugas</label>
                        <input type="file" id="dokumen_tugas" name="dokumen_tugas" accept=".pdf">
                        <?php if (!empty($row['dokumen_tugas'])): ?>
                            <p>Dokument Sekarang: <a href="uploads/<?= htmlspecialchars($row['dokumen_tugas']); ?>" target="_blank" class="document-link"><?= htmlspecialchars($row['dokumen_tugas']); ?></a></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-primary">Update Surat</button>
                    <a href="surat_tugas.php" class="btn btn-secondary btn-equal">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>
