<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // Include the database connection

// Proses hapus data jika ada parameter `id`
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Query hapus data
    $sql = "DELETE FROM surat_kontrak WHERE id_kontrak = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus!'); window.location.href='surat_perjanjian_kontrak.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Konfigurasi pagination
$perPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

// Mendapatkan nilai pencarian dari URL jika ada
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Hitung total data
$totalQuery = "SELECT COUNT(*) AS total FROM surat_kontrak WHERE no_kontrak LIKE ? OR perihal_kontrak LIKE ?";
$stmtTotal = $conn->prepare($totalQuery);
$searchWildcard = "%$searchQuery%";
$stmtTotal->bind_param("ss", $searchWildcard, $searchWildcard);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalData = $resultTotal->fetch_assoc()['total'];
$totalPages = ceil($totalData / $perPage);

// Query data dengan limit dan offset
$sql = "SELECT * FROM surat_kontrak WHERE no_kontrak LIKE ? OR perihal_kontrak LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $searchWildcard, $searchWildcard, $perPage, $offset);
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
        /* Styling untuk form pencarian */
        .search-container {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-container input[type="text"] {
            padding: 8px 15px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 250px;
            transition: border-color 0.3s;
        }

        .search-container input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
        }

        .search-container button {
            padding: 8px 16px;
            margin-left: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .search-container button:hover {
            background-color: #0056b3;
        }

        .search-container button:focus {
            outline: none;
        }

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
        .table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .table th, .table td {
            padding: 4px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .table th {
            background-color: #e6f7ff;
            font-weight: bold;
        }

        .table th, .table td {
            vertical-align: middle;
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
            width: 50%;
        }

        /* Tombol */
        .btn {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 4px;
        }

        .btn-info:hover {
            background-color: #218838;
        }

        .btn-warning {
            background-color: #ffc107;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
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
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-success {
            background-color: rgb(89, 83, 84);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

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
</head><body>
    <!-- Sidebar -->
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

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h2>Administrasi</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['role']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>

        <div class="container">
            <h2>Daftar Surat Kontrak</h2>

            <!-- Form Pencarian -->
            <form action="surat_perjanjian_kontrak.php" method="GET" class="search-container">
                <input type="text" name="search" placeholder="Cari No Surat atau Perihal..." value="<?= htmlspecialchars($searchQuery); ?>" />
                <button type="submit">Search</button>
            </form>

            <?php if ($_SESSION['role'] !== 'pimpinan') : ?>
            <a href="tambah_kontrak.php" class="btn btn-primary">Tambah Surat +</a>
            <?php endif; ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Surat</th>
                        <th>Perihal</th>
                        <th>Tanggal</th>
                        <th>Pihak 1</th>
                        <th>Pihak 2</th>
                        <?php if ($_SESSION['role'] !== 'pimpinan') : ?>
                        <th colspan="5">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['no_kontrak']); ?></td>
                                <td><?= htmlspecialchars($row['perihal_kontrak']); ?></td>
                                <td><?= htmlspecialchars($row['tgl_kontrak']); ?></td>
                                <td><?= htmlspecialchars($row['pihak1']); ?></td>
                                <td><?= htmlspecialchars($row['pihak2']); ?></td>
                                <?php if ($_SESSION['role'] !== 'pimpinan') : ?>
                                <td>
                                    <?php if (empty($row['dokumen_kontrak'])): ?>
                                    <a href="upload_kontrak.php?id=<?= $row['id_kontrak']; ?>" class="btn btn-primary">Upload</a>
                                    <?php endif; ?>
                                </td>
                                <td><a href="cetak_kontrak.php?id=<?= $row['id_kontrak']; ?>" class="btn btn-success" target="_blank">Cetak</a></td>
                                <td><a href="edit_kontrak.php?id=<?= $row['id_kontrak']; ?>" class="btn btn-warning">Edit</a></td>
                                <td><a href="?id=<?= $row['id_kontrak']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')">Hapus</a></td>
                                <td><a href="detail_kontrak.php?id=<?= $row['id_kontrak']; ?>" class="btn btn-info">Detail</a></td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11">Tidak ada data.</td>
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

    <footer>
        <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>