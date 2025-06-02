<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['username']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Koneksi database
include 'db.php';

$kode_surat = '188';
$kode_dinas = '432.312';
$tahun_ini  = date('Y');         
$sql_last = "
    SELECT no_keputusan
    FROM   surat_keputusan
    WHERE  no_keputusan LIKE '$kode_surat/%/$kode_dinas/$tahun_ini'
    ORDER  BY CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(no_keputusan,'/',-3),'/',1) AS UNSIGNED) DESC
    LIMIT  1";

$hasil_last = mysqli_query($conn, $sql_last);
if ($hasil_last && mysqli_num_rows($hasil_last) > 0) {
    $row         = mysqli_fetch_assoc($hasil_last);
    $parts       = explode('/', $row['no_keputusan']);
    $last_number = isset($parts[1]) ? (int)$parts[1] : 0;
    $next_number = $last_number + 1;
} else {
    $next_number = 1;
}
$no_urut_pad   = str_pad($next_number, 3, '0', STR_PAD_LEFT);      // 3 digit
$preview_no    = "$kode_surat/$no_urut_pad/$kode_dinas/$tahun_ini";

/* ───────────────────────────────────────────────
   Proses simpan saat form disubmit
   ─────────────────────────────────────────────── */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $perihal_keputusan = mysqli_real_escape_string($conn, $_POST['perihal_keputusan']);
    $tgl_keputusan     = mysqli_real_escape_string($conn, $_POST['tgl_keputusan']);
    $agenda_keputusan  = mysqli_real_escape_string($conn, $_POST['agenda_keputusan']);
    $keputusan_input   = $_SESSION['id'];

    $tahun           = date('Y', strtotime($tgl_keputusan));               // tahun sesuai tanggal surat
    $no_keputusan      = "$kode_surat/$no_urut_pad/$kode_dinas/$tahun";

    $sql = "INSERT INTO surat_keputusan
              (no_keputusan, perihal_keputusan, tgl_keputusan, agenda_keputusan,
               keputusan_input)
            VALUES
              ('$no_keputusan', '$perihal_keputusan', '$tgl_keputusan', '$agenda_keputusan',
               '$keputusan_input')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data berhasil ditambahkan!'); 
              window.location.href='surat_keputusan.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Surat Keputusan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo" />
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php">🏠 Dashboard</a></li>
            <li><a href="surat_masuk.php">📂 Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php">📤 Data Surat Keluar</a></li>
            <li><a href="surat_perjanjian_kontrak.php" class="active">📜 Surat Perjanjian Kontrak</a></li>
            <li><a href="surat_keputusan.php">📋 Surat Keputusan</a></li>
            <li><a href="surat_tugas.php">📄 Surat Tugas</a></li>
            <li><a href="arsip.php">📚 Arsip Surat</a></li>
            <li><a href="laporan.php">📊 Laporan</a></li>
            <li><a href="data_master.php">⚙️ Data Master</a></li>
            <li><a href="logout.php">🔒 Logout</a></li>
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
        <h2>Tambah Surat Keputusan</h2>
        <form method="post" class="form-container">
            <!-- Nomor kontrak otomatis (readonly) -->
            <div class="form-row">
                <div class="form-group">
                    <label>Nomor Surat</label>
                    <input type="text" value="<?= $preview_no ?>" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="perihal_keputusan">Perihal</label>
                    <input type="text" id="perihal_keputusan" name="perihal_keputusan" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="tgl_keputusan">Tanggal Surat</label>
                    <input type="date" id="tgl_keputusan" name="tgl_keputusan" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="agenda_keputusan">Tanggal Agenda (Opsional)</label>
                    <input type="date" id="agenda_keputusan" name="agenda_keputusan">
                </div>
            </div>

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
