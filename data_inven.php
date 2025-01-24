<?php
session_start();
include 'db.php';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$perPage = 10; // Jumlah data per halaman
$currentPage = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($currentPage - 1) * $perPage;

// Mendapatkan nilai pencarian dan filter
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$lokasiBarang = isset($_GET['lokasi_barang']) ? trim($_GET['lokasi_barang']) : '';

// Query untuk menghitung total data
$whereClauses = [];
$params = [];
$paramTypes = '';

if (!empty($searchQuery)) {
    $whereClauses[] = "(nama_barang LIKE ? OR stok LIKE ? OR lokasi_barang LIKE ? OR kondisi_barang LIKE ?)";
    $searchWildcard = "%$searchQuery%";
    $params = array_merge($params, [$searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard]);
    $paramTypes .= 'ssss';
}

if (!empty($lokasiBarang)) {
    $whereClauses[] = "lokasi_barang = ?";
    $params[] = $lokasiBarang;
    $paramTypes .= 's';
}

$whereSql = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
$totalSql = "SELECT COUNT(*) as total FROM inventaris $whereSql";
$totalStmt = $conn->prepare($totalSql);

if ($params) {
    $totalStmt->bind_param($paramTypes, ...$params);
}
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $perPage);

// Query untuk mendapatkan data inventaris
$dataSql = "SELECT * FROM inventaris $whereSql LIMIT ?, ?";
$params[] = $offset;
$params[] = $perPage;
$paramTypes .= 'ii';

$dataStmt = $conn->prepare($dataSql);
$dataStmt->bind_param($paramTypes, ...$params);
$dataStmt->execute();
$result = $dataStmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Inventaris</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 10px 0;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination a {
            text-decoration: none;
            color: #007bff;
            padding: 8px 12px;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }

        .pagination a:hover:not(.active) {
            background-color: #0056b3;
            color: white;
        }

        .pagination .disabled {
            color: #ccc;
            pointer-events: none;
                }
        /* Filter Lokasi dan Pencarian */
            .search-bar{
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 10px;
                padding: 5px 10px;
            }
            .filter-lokasi {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 10px;
                padding: 5px 5px;
                margin-right: 1000px;
                margin-left: 5px;
            }

            .search-bar input,
            .filter-lokasi select {
                flex: 1;
                padding: 10px;
                font-size: 14px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }

            .search-bar button{
                padding: 10px 18px;
                font-size: 14px;
                color: white;
                background-color: #0078d4;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease;
                margin-top: 0px;
            }

            .filter-lokasi button {
                padding: 10px 15px;
                font-size: 14px;
                color: white;
                background-color: #0078d4;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease;
                margin-top: 0px;
                margin-left: 0px;
            }

            .search-bar button:hover,
            .filter-lokasi button:hover {
                background-color: #005bb5;
                transform: scale(1.05);
            }

            .filter-lokasi select {
                width: 250px; /* Ukuran dropdown */
                max-width: 100%;
            }

            .search-bar input:focus,
            .filter-lokasi select:focus {
                outline: none;
                border-color: #0078d4;
                box-shadow: 0 0 5px rgba(0, 120, 212, 0.5);
            }
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 10px 0;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination a {
            text-decoration: none;
            color: #007bff;
            padding: 8px 12px;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }

        .pagination a:hover:not(.active) {
            background-color: #0056b3;
            color: white;
        }

        .pagination .disabled {
            color: #ccc;
            pointer-events: none;
                }
        /* Filter Lokasi dan Pencarian */
            .search-bar,
            .filter-lokasi {
                align-items: center;
                gap: 10px;
                margin-bottom: 20px;
                padding: 5px 15px;
            }

            .search-bar input,
            .filter-lokasi select {
                padding: 10px;
                font-size: 14px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }

            .search-bar button,
            .filter-lokasi button {
                padding: 10px 15px;
                font-size: 14px;
                color: white;
                background-color: #0078d4;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease;
            }

            .search-bar button:hover,
            .filter-lokasi button:hover {
                background-color: #005bb5;
                transform: scale(1.05);
            }

            .filter-lokasi select {
                width: 250px; /* Ukuran dropdown */
                max-width: 100%;
            }

            .search-bar input:focus,
            .filter-lokasi select:focus {
                outline: none;
                border-color: #0078d4;
                box-shadow: 0 0 5px rgba(0, 120, 212, 0.5);
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
            <h2>Daftar Inventaris</h2>
            <form action="data_inven.php" method="GET">
                <div class="search-bar">
                    <input type="text" name="search" placeholder="Pencarian" value="<?= htmlspecialchars($searchQuery); ?>" />
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
                <div class="filter-lokasi">
                    <select name="lokasi_barang">
                        <option value="">Pilih Lokasi Barang</option>
                        <?php
                        $lokasiQuery = "SELECT DISTINCT lokasi_barang FROM inventaris ORDER BY lokasi_barang ASC";
                        $lokasiResult = $conn->query($lokasiQuery);

                        if ($lokasiResult && $lokasiResult->num_rows > 0) {
                            while ($lokasi = $lokasiResult->fetch_assoc()) {
                                $selected = ($lokasiBarang == $lokasi['lokasi_barang']) ? 'selected' : '';
                                echo "<option value=\"" . htmlspecialchars($lokasi['lokasi_barang']) . "\" $selected>" . htmlspecialchars($lokasi['lokasi_barang']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                    <button class="btn btn-primary" type="submit">Filter</button>
                </div>
            </form>


            <a href="tambah_inven.php" class="btn btn-primary">Tambah Inventaris +</a>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Waktu Pengadaan</th>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Lokasi Barang</th>
                        <th>Kondisi Barang</th>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <th>Aksi</th>
                        <!-- <th>Dokumentasi</th> -->
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                                <td><?= htmlspecialchars($row['waktu_pengadaan']); ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                                <td><?= htmlspecialchars($row['stok']); ?></td>
                                <td><?= htmlspecialchars($row['lokasi_barang']); ?></td>
                                <td><?= htmlspecialchars($row['kondisi_barang']); ?></td>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <td>
                                    <a href="detail_inven.php?id=<?= $row['id_inventaris']; ?>" class="btn btn-secondary">Detail</a>
                                    <a href="edit_inven.php?id=<?= $row['id_inventaris']; ?>" class="btn btn-warning">Edit</a>
                                    <a href="hapus_inven.php?id=<?= $row['id_inventaris']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')">Hapus</a>
                                </td>
                                <!-- <td>
                                    <?php if (!empty($row['foto_barang'])): ?>
                                        <img src="<?= htmlspecialchars($row['foto_barang']); ?>" alt="Foto Barang" width="50" height="50">
                                    <?php else: ?>
                                        Tidak ada foto
                                    <?php endif; ?>
                                </td> -->

                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Tidak ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <form action="data_inven.php" method="POST">
            <ul class="pagination">
            <?php if ($currentPage > 1): ?>
                <li><a href="?page=<?= $currentPage - 1; ?>&search=<?= htmlspecialchars($searchQuery); ?>&lokasi_barang=<?= htmlspecialchars($lokasiBarang); ?>">&laquo; Prev</a></li>
            <?php else: ?>
                <li class="disabled"><span>&laquo; Prev</span></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li>
                    <a href="?page=<?= $i; ?>&search=<?= htmlspecialchars($searchQuery); ?>&lokasi_barang=<?= htmlspecialchars($lokasiBarang); ?>" class="<?= $i === $currentPage ? 'active' : ''; ?>">
                        <?= $i; ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <li><a href="?page=<?= $currentPage + 1; ?>&search=<?= htmlspecialchars($searchQuery); ?>&lokasi_barang=<?= htmlspecialchars($lokasiBarang); ?>">Next &raquo;</a></li>
            <?php else: ?>
                <li class="disabled"><span>Next &raquo;</span></li>
            <?php endif; ?>
        </ul>
            </form>

            <div class="export-buttons">
                <a href="export_excel.php?search=<?= urlencode($searchQuery); ?>&lokasi_barang=<?= urlencode($lokasiBarang); ?>" class="btn btn-success">Export ke Excel</a>
            </div>
        </div>
    </div>
    <footer>
        <p>https://dpmd.pamekasankab.go.id/</p>
    </footer>
</body>
</html>
