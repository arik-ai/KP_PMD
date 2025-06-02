<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Proses hapus data jika ada parameter `id`
if (isset($_GET['id'])) {
    $id = (int) $_GET['id']; // Pastikan ID adalah angka untuk keamanan

    // Query hapus data
    $sql = "DELETE FROM surat_masuk WHERE id_surat = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus!'); window.location.href='surat_masuk.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Konfigurasi pagination
$perPage = 10; // Jumlah data per halaman
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

// Mendapatkan nilai pencarian dari URL jika ada
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Hitung total data
$totalQuery = "SELECT COUNT(*) AS total FROM surat_masuk WHERE nomor_surat LIKE ? OR perihal LIKE ? OR pengirim LIKE ? OR nama_sifat_surat LIKE ?";
$stmtTotal = $conn->prepare($totalQuery);
$searchWildcard = "%$searchQuery%";
$stmtTotal->bind_param("ssss", $searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalData = $resultTotal->fetch_assoc()['total'];
$totalPages = ceil($totalData / $perPage);

// Query data dengan limit dan offset
$sql = "SELECT * FROM surat_masuk WHERE nomor_surat LIKE ? OR perihal LIKE ? OR pengirim LIKE ? OR nama_sifat_surat LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssii", $searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard, $perPage, $offset);
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
    <style>
        /* Gaya Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            margin: 20px 0;
            padding: 0;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination a {
            display: inline-block;
            padding: 10px 15px;
            color: white;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .pagination a:hover {
            background-color: #0056b3;
        }

        .pagination .active {
            background-color: #0056b3;
            font-weight: bold;
            pointer-events: none;
        }

        .pagination .disabled span {
            display: inline-block;
            padding: 10px 15px;
            color: #ffffff;
            background-color: #cccccc;
            text-decoration: none;
            border-radius: 5px;
            cursor: default;
        }

        .pagination .disabled {
            pointer-events: none;
        }
        /* Tabel */
        /* Tabel */
        .table {
            width: 100%;
            border-collapse: collapse;
            text-align: center; /* Center align content in the table */
        }

        .table th, .table td {
            padding: 4px; /* Increased padding for better spacing */
            text-align: center;
            border: 1px solid #ddd;
        }

        .table th {
            background-color: #e6f7ff;
            font-weight: bold;
        }

        .table th, .table td {
            vertical-align: middle; /* Vertically center content */
        }

        /* Atur lebar kolom sesuai dengan kontennya */
        .table th:nth-child(1), .table td:nth-child(1) {
            width: 5%;
        }

        .table th:nth-child(2), .table td:nth-child(2) {
            width: 20%;
        }

        .table th:nth-child(3), .table td:nth-child(3) {
            width: 20%;
        }

        .table th:nth-child(4), .table td:nth-child(4) {
            width: 15%;
        }

        .table th:nth-child(5), .table td:nth-child(5) {
            width: 15%;
        }

        .table th:nth-child(6), .table td:nth-child(6) {
            width: 15%;
        }

        .table th:nth-child(7), .table td:nth-child(7) {
            width: 10%;
        }

        .table th:nth-child(8), .table td:nth-child(8) {
            width: 40%;
        }
        /* Tombol */
        .btn {
            padding: 6px 12px; /* Smaller padding */
            font-size: 14px; /* Smaller font size */
            border-radius: 4px; /* Slightly smaller border-radius */
        }


        .btn-info:hover {
            background-color: #218838;
        }

        .btn-warning {
            background-color: #ffc107;
            color: white;
            border: none;
            padding: 6px 12px; /* Smaller padding */
            border-radius: 4px; /* Slightly smaller border-radius */
            font-size: 14px; /* Smaller font size */
            text-decoration: none;
            display: inline-block;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px; /* Smaller padding */
            border-radius: 4px; /* Slightly smaller border-radius */
            font-size: 14px; /* Smaller font size */
            text-decoration: none;
            display: inline-block;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Warna tombol Detail */
        .btn-info {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-info:hover {
            background-color: #218838;
        }

            
    </style>

</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo" />
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

        <!-- Table Content -->
        <div class="container">
            <h2>Daftar Surat Masuk</h2>
            <div class="search-bar">
                <form action="surat_masuk.php" method="GET">
                    <input type="text" name="search" placeholder="Pencarian" value="<?= htmlspecialchars($searchQuery); ?>" />
                    <button class="btn btn-primary" type="submit">Search</button>
                </form>
            </div>
            <?php if ($_SESSION['role'] !== 'pimpinan') : ?>
            <a href="tambah_surat_masuk.php" class="btn btn-primary">Tambah Surat +</a>
             <?php endif; ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Surat</th>
                        <th>Perihal</th>
                        <th>Tanggal Surat</th>
                        <th>Diterima Tanggal</th>
                        <th>Instansi Pengirim</th>
                        <th>sifat</th>
                    <?php if ($_SESSION['role'] !== 'pimpinan') : ?>
                        <th colspan="4">Aksi</th>
                    <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nomor_surat']); ?></td>
                                <td><?= htmlspecialchars($row['perihal']); ?></td>
                                <td><?= htmlspecialchars($row['tgl_surat']); ?></td>
                                <td><?= htmlspecialchars($row['terima_tanggal']); ?></td>
                                <td><?= htmlspecialchars($row['pengirim']); ?></td>
                                <td><?= htmlspecialchars($row['nama_sifat_surat']); ?></td>
                                <td>
                                    <a href="cetak.php?id=<?= $row['id_surat']; ?>" class="btn btn-secondary">Cetak</a></td>
                                    <td> <a href="edit.php?id=<?= $row['id_surat']; ?>" class="btn btn-warning">Edit</a></td>
                                    <td><a href="?id=<?= $row['id_surat']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')">Hapus</a></td>
                                    <td><a href="detail_surat.php?id=<?= $row['id_surat']; ?>" class="btn btn-info">Detail</a> <!-- Tombol Detail -->
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="no-data">Tidak ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <ul class="pagination">
                <?php if ($currentPage > 1): ?>
                    <li><a href="?page=<?= $currentPage - 1; ?>&search=<?= htmlspecialchars($searchQuery); ?>">&laquo; Prev</a></li>
                <?php else: ?>
                    <li class="disabled"><span>&laquo; Prev</span></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li>
                        <a href="?page=<?= $i; ?>&search=<?= htmlspecialchars($searchQuery); ?>" class="<?= $i === $currentPage ? 'active' : ''; ?>">
                            <?= $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <li><a href="?page=<?= $currentPage + 1; ?>&search=<?= htmlspecialchars($searchQuery); ?>">Next &raquo;</a></li>
                <?php else: ?>
                    <li class="disabled"><span>Next &raquo;</span></li>
                <?php endif; ?>
            </ul>

        </div>
    </div>

    <!-- Footer -->
    <footer>
    <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>
