<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Tangkap notifikasi nomor surat baru
$newSuratNo = isset($_GET['new_surat_no']) ? $_GET['new_surat_no'] : '';

// Konfigurasi pagination
$perPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

// Pencarian
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Proses penghapusan surat
if (isset($_GET['hapus_id'])) {
    $hapusId = intval($_GET['hapus_id']); // Sanitasi input
    $deleteQuery = "DELETE FROM surat_keluar WHERE id_surat_keluar = ?";
    $stmtDelete = $conn->prepare($deleteQuery);
    $stmtDelete->bind_param("i", $hapusId);

    if ($stmtDelete->execute()) {
        header("Location: surat_keluar.php?message=deleted");
        exit;
    } else {
        echo "Gagal menghapus data.";
    }
}

// Hitung total data
$totalQuery = "SELECT COUNT(*) AS total FROM surat_keluar WHERE no_surat LIKE ? OR perihal_surat LIKE ? OR penerima LIKE ? OR nama_sifat_surat LIKE ?";
$stmtTotal = $conn->prepare($totalQuery);
$searchWildcard = "%$searchQuery%";
$stmtTotal->bind_param("ssss", $searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalData = $resultTotal->fetch_assoc()['total'];
$totalPages = ceil($totalData / $perPage);

// Query data dengan limit dan offset
$sql = "SELECT *, dokumen_surat FROM surat_keluar WHERE no_surat LIKE ? OR perihal_surat LIKE ? OR penerima LIKE ? OR nama_sifat_surat LIKE ? LIMIT ? OFFSET ?";
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
    <title>Daftar Surat Keluar</title>
    <link rel="stylesheet" href="style.css">
    <style>
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
        
        .notification {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            position: relative;
        }

        .notification button {
            background-color: transparent;
            color: white;
            border: none;
            font-size: 16px;
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo" />
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="surat_masuk.php" ><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php" class="active"><span class="icon">📤</span> Data Surat Keluar</a></li>
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
        <div class="topbar">
            <h2>Administrasi</h2>
            <div class="profile">
                <span><?= htmlspecialchars($_SESSION['role']); ?></span>
                <div class="profile-icon">👤</div>
            </div>
        </div>
        <div class="container">
            <h2>Daftar Surat Keluar</h2>

            <!-- Tampilkan notifikasi jika ada -->
            <?php if (!empty($newSuratNo)): ?>
                <div class="notification" id="notification">
                    Surat berhasil ditambah dengan No. Surat: <?= htmlspecialchars($newSuratNo); ?>
                    <button id="close-notification">Oke</button>
                </div>
            <?php endif; ?>

            <!-- Form pencarian -->
            <div class="search-bar">
                <form action="surat_keluar.php" method="GET">
                    <input type="text" name="search" placeholder="Pencarian" value="<?= htmlspecialchars($searchQuery); ?>" />
                    <button class="btn btn-primary" type="submit">Search</button>
                </form>
            </div>
            <a href="tambah_surat_keluar.php" class="btn btn-primary">Tambah Surat +</a>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Surat</th>
                        <th>Perihal</th>
                        <th>Tanggal Surat</th>
                        <th>Penerima</th>
                        <th>Sifat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['no_surat']); ?></td>
                                <td><?= htmlspecialchars($row['perihal_surat']); ?></td>
                                <td><?= htmlspecialchars($row['tanggal_surat']); ?></td>
                                <td><?= htmlspecialchars($row['penerima']); ?></td>
                                <td><?= htmlspecialchars($row['nama_sifat_surat']); ?></td>
                                <td style="text-align: right;">
                                    <!-- Jika kolom 'file_upload' kosong, tampilkan tombol Upload -->
                                    <?php if (empty($row['dokumen_surat'])): ?>
                                        <a href="upload.php?id=<?= $row['id_surat_keluar']; ?>" class="btn btn-primary">Upload</a>
                                    <?php endif; ?>
                                    <a href="cetak_surat_keluar.php?id=<?= $row['id_surat_keluar']; ?>" class="btn btn-secondary">Cetak</a>
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                        <a href="edit_surat_keluar.php?id=<?= $row['id_surat_keluar']; ?>" class="btn btn-warning">Edit</a>
                                    <?php endif; ?>
                                    <a href="surat_keluar.php?hapus_id=<?= $row['id_surat_keluar']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="no-data">Tidak ada data.</td>
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
