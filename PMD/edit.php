<?php
// Koneksi ke database
include 'db.php';

// Cek apakah parameter ID ada di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data surat berdasarkan ID
    $query = "SELECT * FROM surat_masuk WHERE id_surat = $id";
    $result = mysqli_query($conn, $query);

    // Jika data ditemukan
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='surat_masuk.php';</script>";
        exit;
    }
}

// Proses update data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_surat = $_POST['id_surat'];
    $nomor_surat = mysqli_real_escape_string($conn, $_POST['nomor_surat']);
    $tgl_surat = mysqli_real_escape_string($conn, $_POST['tgl_surat']);
    $perihal = mysqli_real_escape_string($conn, $_POST['perihal']);
    $pengirim = mysqli_real_escape_string($conn, $_POST['pengirim']);
    $terima_tanggal = mysqli_real_escape_string($conn, $_POST['terima_tanggal']);
    $sifat = mysqli_real_escape_string($conn, $_POST['sifat']);

    // Proses upload file jika ada file yang diunggah
    $dokumen = $data['dokumen']; // Default dokumen adalah dokumen lama
    if (!empty($_FILES['file_surat']['name'])) {
        $file_surat = $_FILES['file_surat']['name'];
        $tmp_name = $_FILES['file_surat']['tmp_name'];
        $upload_dir = 'uploads/';
        $file_path = $upload_dir . basename($file_surat);

        // Pindahkan file yang diupload ke folder tujuan
        if (move_uploaded_file($tmp_name, $file_path)) {
            $dokumen = basename($file_surat); // Update nama file jika berhasil upload
        } else {
            echo "<script>alert('Gagal mengupload file!'); window.history.back();</script>";
            exit;
        }
    }

    // Query untuk update data
    $query = "UPDATE surat_masuk SET 
        nomor_surat = '$nomor_surat', 
        tgl_surat = '$tgl_surat', 
        perihal = '$perihal', 
        pengirim = '$pengirim', 
        terima_tanggal = '$terima_tanggal', 
        sifat = '$sifat', 
        dokumen = '$dokumen' 
        WHERE id_surat = $id_surat";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='surat_masuk.php';</script>";
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
    <title>Edit Surat Masuk</title>
    <link rel="stylesheet" href="style.css"> <!-- Menggunakan CSS yang sudah dibuat -->
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="index.php" class="active"><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php"><span class="icon">📤</span> Data Surat Keluar</a></li>
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
                <div class="profile-icon">&#128100;</div>
                <span>Admin/Operator</span>
            </div>
        </div>

        <!-- Form Edit Surat Masuk -->
        <div class="container">
            <h2>Edit Surat Masuk</h2>
            <form action="" method="post" enctype="multipart/form-data" class="form-container">
                <input type="hidden" name="id_surat" value="<?= htmlspecialchars($data['id_surat']); ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nomor_surat">No Surat Masuk</label>
                        <input type="text" id="nomor_surat" name="nomor_surat" value="<?= htmlspecialchars($data['nomor_surat']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tgl_surat">Tanggal Surat</label>
                        <input type="date" id="tgl_surat" name="tgl_surat" value="<?= htmlspecialchars($data['tgl_surat']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="perihal">Perihal</label>
                        <input type="text" id="perihal" name="perihal" value="<?= htmlspecialchars($data['perihal']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pengirim">Instansi Pengirim</label>
                        <input type="text" id="pengirim" name="pengirim" value="<?= htmlspecialchars($data['pengirim']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="terima_tanggal">Diterima Tanggal</label>
                        <input type="date" id="terima_tanggal" name="terima_tanggal" value="<?= htmlspecialchars($data['terima_tanggal']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="sifat">Sifat</label>
                        <select id="sifat" name="sifat" required>
                            <option value="">--Sifat--</option>
                            <option value="Penting" <?= $data['sifat'] == 'Penting' ? 'selected' : ''; ?>>Penting</option>
                            <option value="Rahasia" <?= $data['sifat'] == 'Rahasia' ? 'selected' : ''; ?>>Rahasia</option>
                            <option value="Biasa" <?= $data['sifat'] == 'Biasa' ? 'selected' : ''; ?>>Biasa</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="file_surat">Ubah File Surat (Opsional)</label>
                        <input type="file" id="file_surat" name="file_surat">
                        <p>File saat ini: <strong><?= htmlspecialchars($data['dokumen']); ?></strong></p>
                    </div>
                </div>
                <div class="form-row">
                <div class="form-row">
                    <button type="submit" class="btn btn-primary btn-equal">Simpan Perubahan</button>
                    <a href="surat_masuk.php" class="btn btn-secondary btn-equal">Batal</a>
                </div>


            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy;Sistem Informasi 2023
    </footer>
</body>
</html>
