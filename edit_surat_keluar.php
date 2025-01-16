<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
include 'db.php';

// Cek apakah parameter ID ada di URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Pastikan ID aman
    $query = "SELECT * FROM surat_keluar WHERE id_surat_keluar = $id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='surat_keluar.php';</script>";
        exit;
    }
}

// Proses update data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_surat_keluar = intval($_POST['id_surat_keluar']);
    $no_surat = mysqli_real_escape_string($conn, $_POST['no_surat']);
    $perihal_surat = mysqli_real_escape_string($conn, $_POST['perihal_surat']);
    $tanggal_surat = mysqli_real_escape_string($conn, $_POST['tanggal_surat']);
    $penerima = mysqli_real_escape_string($conn, $_POST['penerima']);
    $sifat_surat = mysqli_real_escape_string($conn, $_POST['sifat_surat']);
    $dokumen = $data['dokumen_surat']; // Default dokumen lama

    // Proses upload file jika ada file baru
    if (!empty($_FILES['dokumen_surat']['name'])) {
        $file_surat = $_FILES['dokumen_surat']['name'];
        $tmp_name = $_FILES['dokumen_surat']['tmp_name'];
        $upload_dir = 'uploads/';
        $file_path = $upload_dir . basename($file_surat);

        if (move_uploaded_file($tmp_name, $file_path)) {
            // Hapus file lama jika ada
            if (!empty($dokumen) && file_exists($upload_dir . $dokumen)) {
                unlink($upload_dir . $dokumen);
            }
            $dokumen = basename($file_surat); // Tetap gunakan nama asli file
        } else {
            echo "<script>alert('Gagal mengupload file!'); window.history.back();</script>";
            exit;
        }
    }

    // Query untuk update data
    $query = "UPDATE surat_keluar SET 
        no_surat = '$no_surat', 
        perihal_surat = '$perihal_surat', 
        tanggal_surat = '$tanggal_surat', 
        penerima = '$penerima', 
        sifat_surat = '$sifat_surat', 
        dokumen_surat = '$dokumen' 
        WHERE id_surat_keluar = $id_surat_keluar";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='surat_keluar.php';</script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Surat Keluar</title>
    <link rel="stylesheet" href="style.css"> <!-- Menggunakan CSS yang sudah dibuat -->
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="surat_masuk.php"><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php" class="active"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="arsip.php"><span class="icon">📚</span> Arsip Surat</a></li>
            <li><a href="laporan.php"><span class="icon">📊</span> Laporan</a></li>
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
        <!-- Form Edit Surat Keluar -->
        <div class="container">
            <h2>Edit Surat Keluar</h2>
            <form action="" method="post" class="form-container" enctype="multipart/form-data">
                <input type="hidden" name="id_surat_keluar" value="<?= htmlspecialchars($data['id_surat_keluar']); ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="no_surat">No Surat</label>
                        <input type="text" id="no_surat" name="no_surat" value="<?= htmlspecialchars($data['no_surat']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_surat">Tanggal Surat</label>
                        <input type="date" id="tanggal_surat" name="tanggal_surat" value="<?= htmlspecialchars($data['tanggal_surat']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="perihal_surat">Perihal</label>
                        <input type="text" id="perihal_surat" name="perihal_surat" value="<?= htmlspecialchars($data['perihal_surat']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="penerima">Penerima</label>
                        <input type="text" id="penerima" name="penerima" value="<?= htmlspecialchars($data['penerima']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="sifat_surat">Sifat</label>
                        <select id="sifat_surat" name="sifat_surat" required>
                            <option value="">--Sifat--</option>
                            <option value="Penting" <?= $data['sifat_surat'] == 'Penting' ? 'selected' : ''; ?>>Penting</option>
                            <option value="Rahasia" <?= $data['sifat_surat'] == 'Rahasia' ? 'selected' : ''; ?>>Rahasia</option>
                            <option value="Biasa" <?= $data['sifat_surat'] == 'Biasa' ? 'selected' : ''; ?>>Biasa</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="dokumen_surat">Ubah File Surat (Opsional)</label>
                        <input type="file" id="dokumen_surat" name="dokumen_surat">
                        <p>File saat ini: <strong><?= htmlspecialchars($data['dokumen_surat']); ?></strong></p>
                    </div>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-primary btn-equal">Simpan Perubahan</button>
                    <a href="surat_keluar.php" class="btn btn-secondary btn-equal">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
    <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>
