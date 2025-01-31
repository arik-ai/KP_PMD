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
    $no_tugas_input = mysqli_real_escape_string($conn, $_POST['no_tugas']);
    $perihal_tugas = mysqli_real_escape_string($conn, $_POST['perihal_tugas']);
    $tgl_tugas = mysqli_real_escape_string($conn, $_POST['tgl_tugas']);
    $agenda_tugas = mysqli_real_escape_string($conn, $_POST['agenda_tugas']);
    $tugas_input = $_SESSION['id']; // Dapatkan user ID dari session

    // Ambil tahun dari tanggal surat
    $tahun = date('Y', strtotime($tgl_tugas));
    
    // Tentukan kode dinas PMD secara otomatis
    $kode_dinas = '432.312'; 

    // Gabungkan nomor kontrak yang diinput dengan kode dinas dan tahun
    $no_tugas = $no_tugas_input . '/' . $kode_dinas . '/' . $tahun;

    // Query untuk menyimpan data ke tabel surat_tugas
    $query = "INSERT INTO surat_tugas (no_tugas, perihal_tugas, tgl_tugas, agenda_tugas, tugas_input) 
              VALUES ('$no_tugas', '$perihal_tugas', '$tgl_tugas', '$agenda_tugas', '$tugas_input')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='surat_tugas.php';</script>";
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
            var kodeSurat = document.getElementById("no_tugas").value; // Ambil kode surat
            var tanggal = document.getElementById("tgl_tugas").value; // Ambil tanggal surat
            if (tanggal) {
                var date = new Date(tanggal);
                var tahun = date.getFullYear(); // Ambil tahun dari tanggal
                var kodeDinas = '432.312'; // Kode Dinas PMD tetap

                // Gabungkan menjadi format lengkap
                var noKontrak = kodeSurat + '/' + kodeDinas + '/' + tahun;
                document.getElementById("no_tugas_full").value = noKontrak; // Tampilkan hasil di input tersembunyi
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
            <li><a href="surat_perjanjian_kontrak.php" ><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
            <li><a href="surat_keputusan.php"><span class="icon">📋</span> Surat Keputusan</a></li>
            <li><a href="surat_tugas.php"  class="active"><span class="icon">📄</span> Surat Tugas</a></li>
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
            <h2>Tambah Surat Tugas</h2>
            <form action="" method="post" class="form-container">
                <div class="form-row">
                    <div class="form-group">
                        <label for="no_tugas">No Surat (.../...)</label>
                        <input type="text" id="no_tugas" name="no_tugas" placeholder="Input no surat masuk" required oninput="generateNoKontrak()">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="perihal_tugas">Perihal</label>
                        <input type="text" id="perihal_tugas" name="perihal_tugas" placeholder="Input Perihal/keterangan surat" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tgl_tugas">Tanggal Surat</label>
                        <input type="date" id="tgl_tugas" name="tgl_tugas" required oninput="generateNoKontrak()">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="agenda_tugas">Tanggal Agenda (Tidak wajib)</label>
                        <input type="date" id="agenda_tugas" name="agenda_tugas">
                    </div>
                </div>

                <!-- Input tersembunyi untuk menyimpan nomor surat lengkap -->
                <input type="hidden" id="no_tugas_full" name="no_tugas_full">

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
