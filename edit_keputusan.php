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

// Ambil ID keputusan dari URL
if (isset($_GET['id'])) {
    $id_keputusan = $_GET['id'];

    // Query untuk mendapatkan data keputusan berdasarkan id
    $query = "SELECT * FROM surat_keputusan WHERE id_keputusan = '$id_keputusan'";
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
    $no_keputusan = mysqli_real_escape_string($conn, $_POST['no_keputusan']);
    $perihal_keputusan = mysqli_real_escape_string($conn, $_POST['perihal_keputusan']);
    $tgl_keputusan = mysqli_real_escape_string($conn, $_POST['tgl_keputusan']);
    $agenda_keputusan = mysqli_real_escape_string($conn, $_POST['agenda_keputusan']);
    
    // Menangani upload file jika ada
    $dokumen_keputusan = '';
    if (isset($_FILES['dokumen_keputusan']) && $_FILES['dokumen_keputusan']['error'] == 0) {
        $targetDir = "uploads/"; // Folder tempat menyimpan file
        $fileName = basename($_FILES['dokumen_keputusan']['name']);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Validasi file
        $allowedTypes = ['pdf', 'doc', 'docx']; // Format yang diperbolehkan
        if (in_array($fileType, $allowedTypes)) {
            // Pindahkan file ke folder tujuan
            if (move_uploaded_file($_FILES['dokumen_keputusan']['tmp_name'], $targetFile)) {
                $dokumen_keputusan = $fileName;
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
        } else {
            echo "Sorry, only PDF, DOC, and DOCX files are allowed.";
            exit;
        }
    }

    // Query untuk update data keputusan
    $updateQuery = "UPDATE surat_keputusan SET no_keputusan = '$no_keputusan', perihal_keputusan = '$perihal_keputusan', 
                    tgl_keputusan = '$tgl_keputusan', agenda_keputusan = '$agenda_keputusan'";

    // Jika ada file dokumen, tambahkan pada query update
    if ($dokumen_keputusan != '') {
        $updateQuery .= ", dokumen_keputusan = '$dokumen_keputusan'";
    }

    $updateQuery .= " WHERE id_keputusan = '$id_keputusan'";

    // Eksekusi query
    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Data berhasil diupdate!'); window.location.href='surat_keputusan.php';</script>";
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
    <title>Edit Surat Keputusan</title>
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
            <li><a href="surat_keputusan.php"  class="active"><span class="icon">📋</span> Surat Keputusan</a></li>
            <li><a href="surat_tugas.php"><span class="icon">📄</span> Surat Tugas</a></li>
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
            <h2>Edit Surat Keputusan</h2>
            <form action="" method="post" class="form-container" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="no_keputusan">No Surat Keputusan</label>
                        <input type="text" id="no_keputusan" name="no_keputusan" value="<?= htmlspecialchars($row['no_keputusan']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="perihal_keputusan">Perihal</label>
                        <input type="text" id="perihal_keputusan" name="perihal_keputusan" value="<?= htmlspecialchars($row['perihal_keputusan']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tgl_keputusan">Tanggal Surat</label>
                        <input type="date" id="tgl_keputusan" name="tgl_keputusan" value="<?= htmlspecialchars($row['tgl_keputusan']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="agenda_keputusan">Tanggal Agenda</label>
                        <input type="date" id="agenda_keputusan" name="agenda_keputusan" value="<?= htmlspecialchars($row['agenda_keputusan']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="dokumen_keputusan">Dokumen Keputusan</label>
                        <input type="file" id="dokumen_keputusan" name="dokumen_keputusan" accept=".pdf">
                        <?php if (!empty($row['dokumen_keputusan'])): ?>
                            <p>Dokument Sekarang: <a href="uploads/<?= htmlspecialchars($row['dokumen_keputusan']); ?>" target="_blank" class="document-link"><?= htmlspecialchars($row['dokumen_keputusan']); ?></a></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-primary">Update Surat</button>
                    <a href="surat_keputusan.php" class="btn btn-secondary btn-equal">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>
