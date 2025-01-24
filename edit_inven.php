<?php
session_start();
include 'db.php'; // Pastikan file ini memiliki koneksi database yang benar

// Periksa apakah parameter ID ada di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data inventaris berdasarkan ID
    $query = "SELECT * FROM inventaris WHERE id_inventaris = $id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='data_inven.php';</script>";
        exit;
    }
}

// Proses update data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_inventaris = $_POST['id_inventaris'];
    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $waktu_pengadaan = mysqli_real_escape_string($conn, $_POST['waktu_pengadaan']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);
    $lokasi_barang = mysqli_real_escape_string($conn, $_POST['lokasi_barang']);
    $kondisi_barang = mysqli_real_escape_string($conn, $_POST['kondisi_barang']);

    // Proses upload gambar
    $foto_barang = $data['foto_barang']; // Default foto adalah yang sudah ada
    if (!empty($_FILES['foto_barang']['name'])) {
        $uploadDir = 'uploads/';
        $fileName = time() . '_' . basename($_FILES['foto_barang']['name']); // Tambahkan timestamp untuk nama unik
        $uploadFile = $uploadDir . $fileName;

        // Validasi tipe file
        $allowedTypes = ['image/jpeg', 'image/png'];
        $fileType = mime_content_type($_FILES['foto_barang']['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            echo "<script>alert('Format file tidak didukung! Hanya JPEG/PNG yang diperbolehkan.');</script>";
            exit;
        }

        // Pindahkan file baru dan hapus file lama (jika ada)
        if (move_uploaded_file($_FILES['foto_barang']['tmp_name'], $uploadFile)) {
            if (!empty($data['foto_barang']) && file_exists($uploadDir . $data['foto_barang'])) {
                unlink($uploadDir . $data['foto_barang']);
            }
            $foto_barang = $fileName;
        } else {
            echo "<script>alert('Gagal mengunggah foto!');</script>";
            exit;
        }
    }

    // Query update data
    $query = "UPDATE inventaris SET 
                kode_barang = '$kode_barang',
                waktu_pengadaan = '$waktu_pengadaan',
                nama_barang = '$nama_barang',
                stok = '$stok',
                lokasi_barang = '$lokasi_barang',
                kondisi_barang = '$kondisi_barang',
                foto_barang = '$foto_barang'
              WHERE id_inventaris = $id_inventaris";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='data_inven.php';</script>";
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
    <title>Edit Data Inventaris</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
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

    <div class="main-content">
        <div class="topbar">
            <h2>Inventaris</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['role']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>

        <div class="container">
            <h2>Edit Data Inventaris</h2>
            <form action="" method="post" enctype="multipart/form-data" class="form-container">
                <input type="hidden" name="id_inventaris" value="<?= htmlspecialchars($data['id_inventaris']); ?>">
                <div class="form-group">
                    <div class="form-group">
                        <label for="kode_barang">Kode Barang</label>
                        <input type="text" id="kode_barang" name="kode_barang" 
                               value="<?= htmlspecialchars($data['kode_barang']); ?>" 
                               placeholder="Input Kode Barang" required>
                    </div>
                    <div class="form-group">
                        <label for="waktu_pengadaan">Waktu Pengadaan</label>
                        <input type="date" id="waktu_pengadaan" name="waktu_pengadaan" 
                               value="<?= htmlspecialchars($data['waktu_pengadaan']); ?>" 
                               required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang</label>
                        <input type="text" id="nama_barang" name="nama_barang" 
                               value="<?= htmlspecialchars($data['nama_barang']); ?>" 
                               placeholder="Input Nama Barang" required>
                    </div>
                    <div class="form-group">
                        <label for="stok">Stok</label>
                        <input type="text" id="stok" name="stok" 
                               value="<?= htmlspecialchars($data['stok']); ?>" 
                               placeholder="Input Stok Barang" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="lokasi_barang">Lokasi Barang</label>
                        <select id="lokasi_barang" name="lokasi_barang" required>
                            <option value="">--Lokasi Barang--</option>
                            <option value="Bidang PPM" <?= $data['lokasi_barang'] == 'Bidang PPM' ? 'selected' : ''; ?>>Bidang PPM</option>
                            <option value="Bidang PEMDES" <?= $data['lokasi_barang'] == 'Bidang PEMDES' ? 'selected' : ''; ?>>Bidang PEMDES</option>
                            <option value="Bidang PKSB" <?= $data['lokasi_barang'] == 'Bidang PKSB' ? 'selected' : ''; ?>>Bidang PKSB</option>
                        </select>
                    </div>
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
                <div class="form-group">
                    <label for="foto_barang">Foto Barang</label>
                    <input type="file" id="foto_barang" name="foto_barang" accept="image/png, image/jpeg">
                    <?php if (!empty($data['foto_barang'])): ?>
                        <p>Foto saat ini: <img src="uploads/<?= htmlspecialchars($data['foto_barang']); ?>" alt="Foto Barang" width="100"></p>
                    <?php endif; ?>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-primary btn-equal">Simpan Perubahan</button>
                    <a href="data_inven.php" class="btn btn-secondary btn-equal">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>


<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Inventaris</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
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

    <div class="main-content">
        <div class="topbar">
            <h2>Inventaris</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['role']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>

        <div class="container">
            <h2>Edit Data Inventaris</h2>
            <form action="" method="post" enctype="multipart/form-data" class="form-container">
                <input type="hidden" name="id_inventaris" value="<?= htmlspecialchars($data['id_inventaris']); ?>">
                <div class="form-group">
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
                        <input type="file" id="foto_barang" name="foto_barang" accept="image/png, image/jpeg">
                        <?php if (!empty($data['foto_barang'])): ?>
                            <p>Foto saat ini: <img src="uploads/<?= htmlspecialchars($data['foto_barang']); ?>" alt="Foto Barang" width="100"></p>
                        <?php endif; ?>
                    </div>
                    <div class="form-row">
                        <button type="submit" class="btn btn-primary btn-equal">Simpan Perubahan</button>
                        <a href="data_inven.php" class="btn btn-secondary btn-equal">Batal</a>
                    </div>
            </form>
        </div>
    </div>

    <footer>
        <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html> -->
