<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $no_kontrak_input = mysqli_real_escape_string($conn, $_POST['no_kontrak']);
    $perihal_kontrak = mysqli_real_escape_string($conn, $_POST['perihal_kontrak']);
    $tgl_kontrak = mysqli_real_escape_string($conn, $_POST['tgl_kontrak']);
    $agenda_kontrak = mysqli_real_escape_string($conn, $_POST['agenda_kontrak']);
    $kontrak_input = $_SESSION['id']; // Dapatkan user ID dari session

    // Ambil tahun dari tanggal surat
    $tahun = date('Y', strtotime($tgl_kontrak));
    
    // Tentukan kode dinas PMD secara otomatis
    $kode_dinas = '432.312'; 

    // Gabungkan nomor kontrak yang diinput dengan kode dinas dan tahun
    $no_kontrak = $no_kontrak_input . '/' . $kode_dinas . '/' . $tahun;

    // Query untuk menyimpan data ke tabel surat_kontrak
    $query = "INSERT INTO surat_kontrak (no_kontrak, perihal_kontrak, tgl_kontrak, agenda_kontrak, kontrak_input) 
              VALUES ('$no_kontrak', '$perihal_kontrak', '$tgl_kontrak', '$agenda_kontrak', '$kontrak_input')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='surat_perjanjian_kontrak.php';</script>";
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
    <title>Tambah Surat Masuk</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function generateNoKontrak() {
            var kodeSurat = document.getElementById("no_kontrak").value; // Ambil kode surat
            var tanggal = document.getElementById("tgl_kontrak").value; // Ambil tanggal surat
            if (tanggal) {
                var date = new Date(tanggal);
                var tahun = date.getFullYear(); // Ambil tahun dari tanggal
                var kodeDinas = '432.312'; // Kode Dinas PMD tetap

                // Gabungkan menjadi format lengkap
                var noKontrak = kodeSurat + '/' + kodeDinas + '/' + tahun;
                document.getElementById("no_kontrak_full").value = noKontrak; // Tampilkan hasil di input tersembunyi
            }
        }
    </script>
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
            <li><a href="surat_perjanjian_kontrak.php" class="active"><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
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
            <h2>Tambah Surat Perjanjian Kontrak</h2>
            <form action="" method="post" class="form-container">
                <div class="form-row">
                    <div class="form-group">
                        <label for="no_kontrak">No Surat (.../...)</label>
                        <input type="text" id="no_kontrak" name="no_kontrak" placeholder="Input no surat masuk" required oninput="generateNoKontrak()">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="perihal_kontrak">Perihal</label>
                        <input type="text" id="perihal_kontrak" name="perihal_kontrak" placeholder="Input Perihal/keterangan surat" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tgl_kontrak">Tanggal Surat</label>
                        <input type="date" id="tgl_kontrak" name="tgl_kontrak" required oninput="generateNoKontrak()">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="agenda_kontrak">Tanggal Agenda (Tidak wajib)</label>
                        <input type="date" id="agenda_kontrak" name="agenda_kontrak">
                    </div>
                </div>

                <!-- Input tersembunyi untuk menyimpan nomor surat lengkap -->
                <input type="hidden" id="no_kontrak_full" name="no_kontrak_full">

                <div class="form-row">
                    <button type="submit" class="btn btn-primary">Tambah Surat</button>
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
