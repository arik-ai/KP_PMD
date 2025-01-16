<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
include 'db.php';

// Fungsi untuk menghasilkan nomor surat otomatis
function generateNoSurat($conn, $tanggal_surat, $kodeSurat) {
    $kodeDinas = "432.312";
    $tahunSurat = date("Y", strtotime($tanggal_surat));

    // Ambil nomor urut terakhir dari database berdasarkan tahun surat
    $query = "SELECT MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(no_surat, '/', 2), '/', -1) AS UNSIGNED)) AS max_no 
              FROM surat_keluar WHERE YEAR(tanggal_surat) = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tahunSurat);
    $stmt->execute();
    $result = $stmt->get_result();
    $maxNo = $result->fetch_assoc()['max_no'];

    // Jika tidak ada data sebelumnya, mulai dari 1
    $nextNo = $maxNo ? $maxNo + 1 : 1;

    // Format nomor urut agar selalu 2 digit
    $noUrut = str_pad($nextNo, 2, "0", STR_PAD_LEFT);

    return "$kodeSurat/$noUrut/$kodeDinas/$tahunSurat";
}


// Proses penyimpanan data ke database jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_surat = mysqli_real_escape_string($conn, $_POST['kode_surat']);
    $perihal_surat = mysqli_real_escape_string($conn, $_POST['perihal_surat']);
    $tanggal_surat = mysqli_real_escape_string($conn, $_POST['tanggal_surat']);
    $penerima = mysqli_real_escape_string($conn, $_POST['penerima']);
    $sifat_surat = mysqli_real_escape_string($conn, $_POST['sifat_surat']);

    $no_surat = generateNoSurat($conn, $tanggal_surat, $kode_surat);

    $query = "INSERT INTO surat_keluar (no_surat, tanggal_surat, perihal_surat, penerima, sifat_surat) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $no_surat, $tanggal_surat, $perihal_surat, $penerima, $sifat_surat);

    if ($stmt->execute()) {
        // Jika berhasil, arahkan ke detail_surat.php dengan parameter no_surat
        header("Location: detail_surat_keluar.php?no_surat=" . urlencode($no_surat));
        exit;
    }else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Surat Keluar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="surat_masuk.php" ><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php" class="active"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="arsip.php"><span class="icon">📚</span> Arsip Surat</a></li>
            <li><a href="laporan.php"><span class="icon">📊</span> Laporan</a></li>
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
            <h2>Tambah Surat Keluar</h2>
            <form action="" method="post" class="form-container">
                <div class="form-row">
                    <div class="form-group">
                        <label for="kode_surat">Kode Surat</label>
                        <input type="text" id="kode_surat" name="kode_surat" placeholder="Input kode surat" required>
                    </div>
                    <div class="form-group">
                        <label for="perihal_surat">Perihal Surat</label>
                        <input type="text" id="perihal_surat" name="perihal_surat" placeholder="Input perihal surat" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tanggal_surat">Tanggal Surat</label>
                        <input type="date" id="tanggal_surat" name="tanggal_surat" required>
                    </div>
                    <div class="form-group">
                        <label for="penerima">Penerima</label>
                        <input type="text" id="penerima" name="penerima" placeholder="Input penerima" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="sifat_surat">Sifat Surat</label>
                        <select id="sifat_surat" name="sifat_surat" required>
                            <option value="">--Pilih Sifat--</option>
                            <option value="Penting">Penting</option>
                            <option value="Rahasia">Rahasia</option>
                            <option value="Biasa">Biasa</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-primary">Tambah Surat</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
    <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>
