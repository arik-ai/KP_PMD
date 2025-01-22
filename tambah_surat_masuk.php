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

// Query untuk mengambil data sifat_surat
$sifat_surat_query = "SELECT id_sifat, nama_sifat_surat FROM sifat_surat";
$sifat_surat_result = mysqli_query($conn, $sifat_surat_query);

// Periksa jika query gagal
if (!$sifat_surat_result) {
    die("Query gagal: " . mysqli_error($conn));
}

// Proses penyimpanan data ke database jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nomor_surat = mysqli_real_escape_string($conn, $_POST['nomor_surat']);
    $tgl_surat = mysqli_real_escape_string($conn, $_POST['tgl_surat']);
    $perihal = mysqli_real_escape_string($conn, $_POST['perihal']);
    $pengirim = mysqli_real_escape_string($conn, $_POST['pengirim']);
    $terima_tanggal = mysqli_real_escape_string($conn, $_POST['terima_tanggal']);
    $nama_sifat_surat = mysqli_real_escape_string($conn, $_POST['nama_sifat_surat']);

    // Proses upload file
    $file_surat = $_FILES['file_surat']['name'];
    $tmp_name = $_FILES['file_surat']['tmp_name'];
    $upload_dir = 'uploads/'; // Folder untuk menyimpan file
    $file_path = $upload_dir . basename($file_surat);

    // Cek apakah folder upload ada, jika tidak, buat folder
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Pindahkan file yang diupload ke folder tujuan
    if (move_uploaded_file($tmp_name, $file_path)) {
        // Simpan nama file ke kolom 'dokumen'
        $dokumen = basename($file_surat);

        // Query untuk menyimpan data ke tabel surat_masuk
        $query = "INSERT INTO surat_masuk (nomor_surat, tgl_surat, perihal, pengirim, terima_tanggal, nama_sifat_surat, dokumen) 
                  VALUES ('$nomor_surat', '$tgl_surat', '$perihal', '$pengirim', '$terima_tanggal', '$nama_sifat_surat', '$dokumen')";

        // Eksekusi query
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='surat_masuk.php';</script>";
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Gagal mengupload file!'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Surat Masuk</title>
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

        <!-- Form Tambah Surat Masuk -->
        <div class="container">
            <h2>Tambah Surat Masuk</h2>
            <form action="" method="post" enctype="multipart/form-data" class="form-container">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nomor_surat">No Surat Masuk</label>
                        <input type="text" id="nomor_surat" name="nomor_surat" placeholder="Input no surat masuk" required>
                    </div>
                    <div class="form-group">
                        <label for="tgl_surat">Tanggal Surat</label>
                        <input type="date" id="tgl_surat" name="tgl_surat" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="perihal">Perihal</label>
                        <input type="text" id="perihal" name="perihal" placeholder="Input Perihal/keterangan surat" required>
                    </div>
                    <div class="form-group">
                        <label for="pengirim">Instansi Pengirim</label>
                        <input type="text" id="pengirim" name="pengirim" placeholder="Input instansi pengirim" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="terima_tanggal">Diterima Tanggal</label>
                        <input type="date" id="terima_tanggal" name="terima_tanggal" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_sifat_surat">Sifat</label>
                        <select id="nama_sifat_surat" name="nama_sifat_surat" required>
                            <option value="">--Pilih Sifat Surat--</option>
                            <?php
                            while ($row = mysqli_fetch_assoc($sifat_surat_result)) {
                                echo '<option value="' . htmlspecialchars($row['nama_sifat_surat']) . '">' . htmlspecialchars($row['nama_sifat_surat']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="file_surat">Pilih File Surat</label>
                        <input type="file" id="file_surat" name="file_surat" required>
                    </div>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-primary">Tambah Surat</button>
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
