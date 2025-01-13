<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Konfigurasi pagination
$perPage = 3; // Jumlah data per halaman
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

// Mendapatkan nilai pencarian dari URL jika ada
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Hitung total data
$totalQuery = "SELECT COUNT(*) AS total FROM surat_masuk WHERE nomor_surat LIKE ? OR perihal LIKE ? OR pengirim LIKE ? OR sifat LIKE ?";
$stmtTotal = $conn->prepare($totalQuery);
$searchWildcard = "%$searchQuery%";
$stmtTotal->bind_param("ssss", $searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalData = $resultTotal->fetch_assoc()['total'];
$totalPages = ceil($totalData / $perPage);

// Query data dengan limit dan offset
$sql = "SELECT * FROM surat_masuk WHERE nomor_surat LIKE ? OR perihal LIKE ? OR pengirim LIKE ? OR sifat LIKE ? LIMIT ? OFFSET ?";
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
    </style>

</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo" />
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="surat_masuk.php" class="active"><span class="icon">📂</span> Data Surat Masuk</a></li>
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
                        <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
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
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                        <a href="edit.php?id=<?= $row['id_surat']; ?>" class="btn btn-warning">Edit</a>
                                    <?php endif; ?>
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
        &copy; Sistem Informasi 2023
    </footer>
</body>
</html>
