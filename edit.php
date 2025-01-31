<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit;
}

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

// Ambil data sifat surat dari tabel sifat_surat
$query_sifat = "SELECT * FROM sifat_surat";
$result_sifat = mysqli_query($conn, $query_sifat);

// Proses update data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_surat = $_POST['id_surat'];
    $nomor_surat = mysqli_real_escape_string($conn, $_POST['nomor_surat']);
    $tgl_surat = mysqli_real_escape_string($conn, $_POST['tgl_surat']);
    $agenda = mysqli_real_escape_string($conn, $_POST['agenda']);
    $perihal = mysqli_real_escape_string($conn, $_POST['perihal']);
    $pengirim = mysqli_real_escape_string($conn, $_POST['pengirim']);
    $terima_tanggal = mysqli_real_escape_string($conn, $_POST['terima_tanggal']);
    $nama_sifat_surat = mysqli_real_escape_string($conn, $_POST['sifat']); // Sesuaikan dengan nama field

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
        agenda = '$agenda',
        perihal = '$perihal', 
        pengirim = '$pengirim', 
        terima_tanggal = '$terima_tanggal', 
        nama_sifat_surat = '$nama_sifat_surat', 
        dokumen = '$dokumen' 
        WHERE id_surat = $id_surat"; // Pastikan nama kolom benar di database


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
            <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="surat_masuk.php" class="active"><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="surat_perjanjian_kontrak.php"><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
            <li><a href="surat_keputusan.php"><span class="icon">📋</span> Surat Keputusan</a></li>
            <li><a href="surat_tugas.php"><span class="icon">📄</span> Surat Tugas</a></li>
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
                    <div class="form-group">
                        <label for="tgl_surat">Agenda Surat</label>
                        <input type="date" id="agenda" name="agenda" value="<?= htmlspecialchars($data['agenda']); ?>" >
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
                            <option value="">--Pilih Sifat Surat--</option>
                            <?php while ($row = mysqli_fetch_assoc($result_sifat)): ?>
                                <option value="<?= $row['nama_sifat_surat']; ?>" <?= $data['nama_sifat_surat'] == $row['nama_sifat_surat'] ? 'selected' : ''; ?>>
                                    <?= $row['nama_sifat_surat']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="file_surat">Ubah File Surat (Opsional)</label>
                        <input type="file" id="file_surat" name="file_surat" accept=".pdf">
                        <p>File saat ini: <strong><?= htmlspecialchars($data['dokumen']); ?></strong></p>
                    </div>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-primary btn-equal">Simpan Perubahan</button>
                    <a href="surat_masuk.php" class="btn btn-secondary btn-equal">Batal</a>
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
