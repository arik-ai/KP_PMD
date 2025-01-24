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

// Ambil ID kontrak dari URL
if (isset($_GET['id'])) {
    $id_kontrak = $_GET['id'];

    // Query untuk mendapatkan data kontrak berdasarkan id
    $query = "SELECT * FROM surat_kontrak WHERE id_kontrak = '$id_kontrak'";
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
    $no_kontrak = mysqli_real_escape_string($conn, $_POST['no_kontrak']);
    $perihal_kontrak = mysqli_real_escape_string($conn, $_POST['perihal_kontrak']);
    $tgl_kontrak = mysqli_real_escape_string($conn, $_POST['tgl_kontrak']);
    $agenda_kontrak = mysqli_real_escape_string($conn, $_POST['agenda_kontrak']);
    
    // Menangani upload file jika ada
    $dokumen_kontrak = '';
    if (isset($_FILES['dokumen_kontrak']) && $_FILES['dokumen_kontrak']['error'] == 0) {
        $targetDir = "uploads/"; // Folder tempat menyimpan file
        $fileName = basename($_FILES['dokumen_kontrak']['name']);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Validasi file
        $allowedTypes = ['pdf', 'doc', 'docx']; // Format yang diperbolehkan
        if (in_array($fileType, $allowedTypes)) {
            // Pindahkan file ke folder tujuan
            if (move_uploaded_file($_FILES['dokumen_kontrak']['tmp_name'], $targetFile)) {
                $dokumen_kontrak = $fileName;
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
        } else {
            echo "Sorry, only PDF, DOC, and DOCX files are allowed.";
            exit;
        }
    }

    // Query untuk update data kontrak
    $updateQuery = "UPDATE surat_kontrak SET no_kontrak = '$no_kontrak', perihal_kontrak = '$perihal_kontrak', 
                    tgl_kontrak = '$tgl_kontrak', agenda_kontrak = '$agenda_kontrak'";

    // Jika ada file dokumen, tambahkan pada query update
    if ($dokumen_kontrak != '') {
        $updateQuery .= ", dokumen_kontrak = '$dokumen_kontrak'";
    }

    $updateQuery .= " WHERE id_kontrak = '$id_kontrak'";

    // Eksekusi query
    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Data berhasil diupdate!'); window.location.href='surat_perjanjian_kontrak.php';</script>";
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
    <title>Edit Surat Kontrak</title>
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
            <li><a href="surat_kontrak.php" class="active"><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="logout.php"><span class="icon">🔒</span> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h2>Administrasi</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['username']); ?> (<?= htmlspecialchars($_SESSION['role']); ?>)</span>
                <div class="profile-icon">👤</div>
            </div>
        </div>

        <div class="container">
            <h2>Edit Surat Kontrak</h2>
            <form action="" method="post" class="form-container" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="no_kontrak">No Surat Kontrak</label>
                        <input type="text" id="no_kontrak" name="no_kontrak" value="<?= htmlspecialchars($row['no_kontrak']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="perihal_kontrak">Perihal</label>
                        <input type="text" id="perihal_kontrak" name="perihal_kontrak" value="<?= htmlspecialchars($row['perihal_kontrak']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tgl_kontrak">Tanggal Surat</label>
                        <input type="date" id="tgl_kontrak" name="tgl_kontrak" value="<?= htmlspecialchars($row['tgl_kontrak']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="agenda_kontrak">Tanggal Agenda</label>
                        <input type="date" id="agenda_kontrak" name="agenda_kontrak" value="<?= htmlspecialchars($row['agenda_kontrak']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="dokumen_kontrak">Dokumen Kontrak</label>
                        <input type="file" id="dokumen_kontrak" name="dokumen_kontrak" accept=".pdf">
                        <?php if (!empty($row['dokumen_kontrak'])): ?>
                            <p>Dokument Sekarang: <a href="uploads/<?= htmlspecialchars($row['dokumen_kontrak']); ?>" target="_blank" class="document-link"><?= htmlspecialchars($row['dokumen_kontrak']); ?></a></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-primary">Update Surat</button>
                    <a href="surat_perjanjian_kontrak.php" class="btn btn-secondary btn-equal">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>
