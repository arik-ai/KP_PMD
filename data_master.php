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
    $sql = "DELETE FROM sifat_surat WHERE id_sifat = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus!'); window.location.href='data_master.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Proses tambah data
if (isset($_POST['add_sifat_surat'])) {
    $nama_sifat_surat = $_POST['nama_sifat_surat'];

    // Query untuk menambahkan data
    $sql = "INSERT INTO sifat_surat (nama_sifat_surat) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nama_sifat_surat);

    if ($stmt->execute()) {
        echo "<script>alert('Sifat Surat berhasil ditambahkan!'); window.location.href='data_master.php';</script>";
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
$totalQuery = "SELECT COUNT(*) AS total FROM sifat_surat WHERE nama_sifat_surat LIKE ?";
$stmtTotal = $conn->prepare($totalQuery);
$searchWildcard = "%$searchQuery%";
$stmtTotal->bind_param("s", $searchWildcard);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalData = $resultTotal->fetch_assoc()['total'];
$totalPages = ceil($totalData / $perPage);

// Query data dengan limit dan offset
$sql = "SELECT * FROM sifat_surat WHERE nama_sifat_surat LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $searchWildcard, $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Master - Sifat Surat</title>
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

        /* Styling Form */
        .form-container {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container input[type="text"] {
            width: 40%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #0056b3;
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
            <li><a href="surat_masuk.php"><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="surat_perjanjian_kontrak.php"><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
            <li><a href="surat_keputusan.php"><span class="icon">📋</span> Surat Keputusan</a></li>
            <li><a href="surat_tugas.php"><span class="icon">📄</span> Surat Tugas</a></li>
            <li><a href="arsip.php"><span class="icon">📚</span> Arsip Surat</a></li>
            <li><a href="laporan.php"><span class="icon">📊</span> Laporan</a></li>
            <li><a href="data_master.php" class="active"><span class="icon">⚙️</span> Data Master</a></li>
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

        <!-- Add Sifat Surat Form -->
        <div class="container">
            <div class="form-container">
            <!-- Table Content -->
            <h2>Data Master - Sifat Surat</h2>
            <br>
            <h3>Tambah Sifat Surat</h3>
                <form action="data_master.php" method="POST">
                    <input type="text" name="nama_sifat_surat" placeholder="Masukkan Nama Sifat Surat" required>
                    <button type="submit" name="add_sifat_surat">Tambah</button>
                </form>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Sifat Surat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_sifat_surat']); ?></td>
                                <td>
                                    <a href="edit_sifat_surat.php?id=<?= $row['id_sifat']; ?>" class="btn btn-warning">Edit</a>
                                    <a href="?id=<?= $row['id_sifat']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="no-data">Tidak ada data.</td>
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
