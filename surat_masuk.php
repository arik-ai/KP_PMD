<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit;
}

include 'db.php';

// Mendapatkan nilai pencarian dari URL jika ada
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Menyusun SQL query dengan kondisi pencarian
$sql = "SELECT * FROM surat_masuk WHERE nomor_surat LIKE ? OR perihal LIKE ? OR pengirim LIKE ?";
$stmt = $conn->prepare($sql);
$searchWildcard = "%$searchQuery%";
$stmt->bind_param("sss", $searchWildcard, $searchWildcard, $searchWildcard);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Surat Masuk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo" />
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="index.php" class="active"><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="arsip.php"><span class="icon">📚</span> Arsip Surat</a></li>
            <li><a href="laporan.php"><span class="icon">📊</span> Laporan</a></li>
            <li><a href="logout.php"><span class="icon">🔒</span> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <h2>Administrasi</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['username']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>

        <!-- Table Content -->
        <div class="container">
            <h2>Daftar Surat Masuk</h2>
            <div class="search-bar">
            <form action="surat_masuk.php" method="GET">
                <input type="text" name="search" placeholder="Pencarian" value="<?= htmlspecialchars($searchQuery); ?>" />
                <button class="btn btn-primary" type="submit">Search</button>
            </form>

            </div>
            <a href="tambah_surat_masuk.php" class="btn btn-primary">Tambah Surat +</a>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Surat</th>
                        <th>Perihal</th>
                        <th>Tanggal Surat</th>
                        <th>Diterima Tanggal</th>
                        <th>Instansi Pengirim</th>
                        <th>Sifat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nomor_surat']); ?></td>
                                <td><?= htmlspecialchars($row['perihal']); ?></td>
                                <td><?= htmlspecialchars($row['tgl_surat']); ?></td>
                                <td><?= htmlspecialchars($row['terima_tanggal']); ?></td>
                                <td><?= htmlspecialchars($row['pengirim']); ?></td>
                                <td><?= htmlspecialchars($row['sifat']); ?></td>
                                <td>
                                    <a href="cetak.php?id=<?= $row['id_surat']; ?>" class="btn btn-secondary">Cetak</a>
                                    <?php
                                    // Pastikan pengguna sudah login dan memiliki role "admin"
                                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                                        // Jika role adalah admin, tampilkan tombol Edit
                                        echo '<a href="edit.php?id=' . $row['id_surat'] . '" class="btn btn-warning">Edit</a>';
                                    }
                                    ?>
                                    <a href="hapus_surat_masuk.php?id=<?= $row['id_surat']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="no-data">Tidak ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; Sistem Informasi 2023
    </footer>
</body>
</html>
