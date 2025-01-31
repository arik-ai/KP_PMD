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

    // Format nomor urut agar selalu 3 digit
    $noUrut = str_pad($nextNo, 3, "0", STR_PAD_LEFT);

    return "$kodeSurat/$noUrut/$kodeDinas/$tahunSurat";
}

// Proses penyimpanan data ke database jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_surat = mysqli_real_escape_string($conn, $_POST['kode_surat']);
    $perihal_surat = mysqli_real_escape_string($conn, $_POST['perihal_surat']);
    $tanggal_surat = mysqli_real_escape_string($conn, $_POST['tanggal_surat']);
    $agenda_keluar = !empty($_POST['agenda_keluar']) ? mysqli_real_escape_string($conn, $_POST['agenda_keluar']) : NULL;
    $penerima = mysqli_real_escape_string($conn, $_POST['penerima']);
    $id_sifat_surat = mysqli_real_escape_string($conn, $_POST['sifat_surat']);
    $user_input_keluar = $_SESSION['id']; // ID pengguna yang sedang login

    // Ambil nama sifat surat dari tabel sifat_surat berdasarkan ID yang dipilih
    $querySifat = "SELECT nama_sifat_surat FROM sifat_surat WHERE id_sifat = ?";
    $stmtSifat = $conn->prepare($querySifat);
    $stmtSifat->bind_param("i", $id_sifat_surat);
    $stmtSifat->execute();
    $resultSifat = $stmtSifat->get_result();
    $rowSifat = $resultSifat->fetch_assoc();
    $nama_sifat_surat = $rowSifat['nama_sifat_surat'];

    $no_surat = generateNoSurat($conn, $tanggal_surat, $kode_surat);

    // Simpan data ke tabel surat_keluar
    $query = "INSERT INTO surat_keluar (no_surat, tanggal_surat, agenda_keluar, perihal_surat, penerima, nama_sifat_surat, user_input_keluar) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssi", $no_surat, $tanggal_surat, $agenda_keluar, $perihal_surat, $penerima, $nama_sifat_surat, $user_input_keluar);

    if ($stmt->execute()) {
        // Jika berhasil, arahkan ke detail_surat_keluar.php dengan parameter id
        header("Location: detail_surat_keluar.php?id=" . urlencode($stmt->insert_id));
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Ambil data sifat surat dari tabel sifat_surat
$sifat_surat_query = "SELECT * FROM sifat_surat";
$sifat_surat_result = $conn->query($sifat_surat_query);

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
            <li><a href="surat_masuk.php"><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php" class="active"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="surat_perjanjian_kontrak.php"><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
            <li><a href="surat_keputusan.php"><span class="icon">📋</span> Surat Keputusan</a></li>
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
                        <label for="tanggal_surat">Agenda Surat</label>
                        <input type="date" id="agenda_keluar" name="agenda_keluar">
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
                            <?php while ($row = $sifat_surat_result->fetch_assoc()) : ?>
                                <option value="<?= htmlspecialchars($row['id_sifat']) ?>"><?= htmlspecialchars($row['nama_sifat_surat']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-primary">Tambah Surat</button>
                    <a href="surat_keluar.php" class="btn btn-secondary btn-equal">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>
