<?php
session_start();
include 'db.php';

// Proses tambah data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $waktu_pengadaan = mysqli_real_escape_string($conn, $_POST['waktu_pengadaan']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);
    $lokasi_barang = mysqli_real_escape_string($conn, $_POST['lokasi_barang']);
    $kondisi_barang = mysqli_real_escape_string($conn, $_POST['kondisi_barang']);

    // Proses upload gambar
    $foto_barang = '';
    if (!empty($_FILES['foto_barang']['name'])) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['foto_barang']['name']); // Menggunakan nama asli file
        $uploadFile = $uploadDir . $fileName;

        // Validasi tipe file
        $allowedTypes = ['image/jpeg', 'image/png'];
        $fileType = mime_content_type($_FILES['foto_barang']['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            echo "<script>alert('Format file tidak didukung!'); window.location.href='data_inven.php';</script>";
            exit;
        }

        // Cek jika file dengan nama yang sama sudah ada
        if (file_exists($uploadFile)) {
            echo "<script>alert('File dengan nama yang sama sudah ada. Harap ganti nama file sebelum mengunggah.'); window.location.href='data_inven.php';</script>";
            exit;
        }

        if (move_uploaded_file($_FILES['foto_barang']['tmp_name'], $uploadFile)) {
            $foto_barang = $fileName;
        } else {
            echo "<script>alert('Gagal mengunggah foto!'); window.location.href='data_inven.php';</script>";
            exit;
        }
    }

    // Insert data into the database
    $query = "INSERT INTO inventaris (kode_barang, waktu_pengadaan, nama_barang, stok, lokasi_barang, kondisi_barang, foto_barang) 
              VALUES ('$kode_barang', '$waktu_pengadaan', '$nama_barang', '$stok', '$lokasi_barang', '$kondisi_barang', '$foto_barang')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='data_inven.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="data_inven.php"><span class="icon">🛒</span> Data Inventaris</a></li>
            <li><a href="logout.php"><span class="icon">🔒</span> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h2>Inventaris</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['role']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>

        <div class="container">
            <h2>Tambah Data Barang</h2>
            <form action="" method="post" enctype="multipart/form-data" class="form-container">
            <div class="form-row">
                    <div class="form-group">
                        <label for="kode_barang">Kode Barang</label>
                        <input type="text" id="kode_barang" name="kode_barang" placeholder="Input Kode Barang" required>
                    </div>
                    <div class="form-group">
                        <label for="waktu_pengadaan">Waktu Pengadaan</label>
                        <input type="date" id="waktu_pengadaan" name="waktu_pengadaan" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang</label>
                        <input type="text" id="nama_barang" name="nama_barang" placeholder="Input Nama Barang" required>
                    </div>
                    <div class="form-group">
                        <label for="stok">Stok</label>
                        <input type="text" id="stok" name="stok" placeholder="Input Stok Barang" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="lokasi_barang">Lokasi Barang</label>
                        <select id="lokasi_barang" name="lokasi_barang" required>
                            <option value="">--Lokasi Barang--</option>
                            <option value="Bidang PPM">Bidang PPM</option>
                            <option value="Bidang PEMDES">Bidang PEMDES</option>
                            <option value="Bidang PKSB">Bidang PKSB</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kondisi_barang">Kondisi Barang</label>
                        <select id="kondisi_barang" name="kondisi_barang" required>
                            <option value="">--Kondisi Barang--</option>
                            <option value="Baik">Baik</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Hilang">Hilang</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="foto_barang">Foto Barang</label>
                    <input type="file" id="foto_barang" name="foto_barang" accept="image/png, image/jpeg" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Tambah Barang</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>
