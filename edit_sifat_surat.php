<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
include 'db.php';

// Periksa apakah ada parameter id_sifat yang dikirim melalui URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID Sifat Surat tidak ditemukan!";
    exit;
}

$id_sifat = intval($_GET['id']); // Pastikan input berupa angka

// Ambil data sifat surat berdasarkan ID
$query = "SELECT * FROM sifat_surat WHERE id_sifat = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_sifat);
$stmt->execute();
$result = $stmt->get_result();
$sifat_surat = $result->fetch_assoc();

if (!$sifat_surat) {
    echo "Data tidak ditemukan!";
    exit;
}

// Proses update data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_sifat_surat = mysqli_real_escape_string($conn, $_POST['nama_sifat_surat']);

    // Update data sifat surat
    $updateQuery = "UPDATE sifat_surat SET nama_sifat_surat = ? WHERE id_sifat = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $nama_sifat_surat, $id_sifat);

    if ($updateStmt->execute()) {
        // Redirect ke halaman data_master.php setelah update sukses
        header("Location: data_master.php?msg=update_success");
        exit;
    } else {
        echo "Error: " . $updateStmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sifat Surat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo" />
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="surat_masuk.php"><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="surat_perjanjian_kontrak.php"><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
            <li><a href="surat_keputusan.php"><span class="icon">📋</span> Surat Keputusan</a></li>
            <li><a href="surat_tugas.php"><span class="icon">📄</span> Surat Tugas</a></li>
            <li><a href="arsip.php"><span class="icon">📚</span> Arsip Surat</a></li>
            <li><a href="laporan.php"><span class="icon">📊</span> Laporan</a></li>
            <li><a href="data_master.php" class="active"><span class="icon">⚙️</span> Data Master</a></li>
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
            <h1>Edit Sifat Surat</h1>
            <form action="" method="post" class="form-container">
                <div class="form-row">
                    <label for="nama_sifat_surat">Nama Sifat Surat</label>
                    <input type="text" id="nama_sifat_surat" name="nama_sifat_surat" value="<?= htmlspecialchars($sifat_surat['nama_sifat_surat']) ?>" required>
                </div>

                <div class="form-row">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="data_master.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
