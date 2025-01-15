<?php
session_start();
// Koneksi ke database
include 'db.php';

// Cek apakah parameter ID ada di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data surat berdasarkan ID
    $query = "SELECT * FROM inventaris WHERE id_inventaris = $id";
    $result = mysqli_query($conn, $query);

    // Jika data ditemukan
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='data_inven.php';</script>";
        exit;
    }
}

// Proses update data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_inventaris = $_POST['id_inventaris'];
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);
    $lokasi_barang = mysqli_real_escape_string($conn, $_POST['lokasi_barang']);
    $kondisi_barang = mysqli_real_escape_string($conn, $_POST['kondisi_barang']);

    // Query untuk update data
    $query = "UPDATE inventaris SET 
        nama_barang = '$nama_barang', 
        stok = '$stok', 
        lokasi_barang = '$lokasi_barang', 
        kondisi_barang = '$kondisi_barang'
    WHERE id_inventaris = $id_inventaris";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='data_inven.php';</script>";
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
    <title>Edit Data Inventaris</title>
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
            <li><a href="index.php" class="active"><span class="icon">📂</span> Data Inventaris</a></li>
            <li><a href="logout.php"><span class="icon">🔒</span> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <h2>Inventaris</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['role']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>

        <!-- Form Edit Surat Masuk -->
        <div class="container">
            <h2>Edit Data Inventaris</h2>
            <form action="" method="post" enctype="multipart/form-data" class="form-container">
                <input type="hidden" name="id_inventaris" value="<?= htmlspecialchars($data['id_inventaris']); ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang</label>
                        <input type="text" id="nama_barang" name="nama_barang" value="<?= htmlspecialchars($data['nama_barang']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="stok">Stok Barang</label>
                        <input type="integer" id="stok" name="stok" value="<?= htmlspecialchars($data['stok']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="lokasi_barang">Lokasi Barang</label>
                        <select id="lokasi_barang" name="lokasi_barang" required>
                            <option value="">--Lokasi Barang--</option>
                            <option value="Bidang PPM" <?= $data['lokasi_barang'] == 'Bidang PPM' ? 'selected' : ''; ?>>Bidang PPM</option>
                            <option value="Bidang Pemdes" <?= $data['lokasi_barang'] == 'Bidang Pemdes' ? 'selected' : ''; ?>>Bidang Pemdes</option>
                            <option value="Bidang PKSB" <?= $data['lokasi_barang'] == 'Bidang PKSB' ? 'selected' : ''; ?>>Bidang PKSB</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="kondisi_barang">Kondisi Barang</label>
                        <select id="kondisi_barang" name="kondisi_barang" required>
                            <option value="">--Kondisi Barang--</option>
                            <option value="Baik" <?= $data['kondisi_barang'] == 'Baik' ? 'selected' : ''; ?>>Baik</option>
                            <option value="Rusak" <?= $data['kondisi_barang'] == 'Rusak' ? 'selected' : ''; ?>>Rusak</option>
                            <option value="Hilang" <?= $data['kondisi_barang'] == 'Hilang' ? 'selected' : ''; ?>>Hilang</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                <div class="form-row">
                    <button type="submit" class="btn btn-primary btn-equal">Simpan Perubahan</button>
                    <a href="data_inven.php" class="btn btn-secondary btn-equal">Batal</a>
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
