<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Konfigurasi pagination
$perPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

// Pencarian dan filter
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$filterYear = isset($_GET['year']) ? (int)$_GET['year'] : '';
$filterMonth = isset($_GET['month']) ? (int)$_GET['month'] : '';

// Menyiapkan kondisi tambahan untuk filter tahun dan bulan
$conditions = "1"; // Default kondisi (menampilkan semua data)
$params = [];
$paramTypes = "";

if ($searchQuery !== '') {
    $conditions .= " AND (no_surat LIKE ? OR perihal_surat LIKE ? OR penerima LIKE ? OR nama_sifat_surat LIKE ?)";
    $searchWildcard = "%$searchQuery%";
    array_push($params, $searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard);
    $paramTypes .= "ssss";
}

if ($filterYear) {
    $conditions .= " AND YEAR(tanggal_surat) = ?";
    array_push($params, $filterYear);
    $paramTypes .= "i";
}

if ($filterMonth) {
    $conditions .= " AND MONTH(tanggal_surat) = ?";
    array_push($params, $filterMonth);
    $paramTypes .= "i";
}

// Hitung total data
$totalQuery = "SELECT COUNT(*) AS total FROM surat_keluar WHERE $conditions";
$stmtTotal = $conn->prepare($totalQuery);
if (!empty($params)) {
    $stmtTotal->bind_param($paramTypes, ...$params);
}
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalData = $resultTotal->fetch_assoc()['total'];
$totalPages = ceil($totalData / $perPage);

// Query data dengan limit dan offset
$sql = "SELECT * FROM surat_keluar WHERE $conditions LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

// Gabungkan parameter tambahan untuk LIMIT dan OFFSET
array_push($params, $perPage, $offset);
$paramTypes .= "ii";

$stmt->bind_param($paramTypes, ...$params);
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
        /* Gaya untuk filter (input, select, dan button) */
        .search-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-bar input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            width: 250px;
        }

        .search-bar select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            width: 150px;
        }

        .search-bar button {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-bar button:hover {
            background-color: #0056b3;
        }

        /* Gaya untuk tombol Export */
        .export-buttons {
            margin-top: 20px;
        }

        .export-buttons a {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .export-buttons a:hover {
            background-color: #218838;
        }

        .notification {
            margin: 20px 0;
            padding: 15px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .notification button:hover {
            background-color: #218838;
        }

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
                    width: 30%;
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
            <li><a href="surat_keluar.php" ><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="surat_perjanjian_kontrak.php"><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
            <li><a href="surat_keputusan.php"><span class="icon">📋</span> Surat Keputusan</a></li>
            <li><a href="surat_tugas.php"><span class="icon">📄</span> Surat Tugas</a></li>
            <li><a href="arsip.php" class="active"><span class="icon">📚</span> Arsip Surat</a></li>
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
            <!-- Form pencarian -->
            <div class="search-bar">
                <form action="arsip_keluar.php" method="GET">
                    <input type="text" name="search" placeholder="Pencarian" value="<?= htmlspecialchars($searchQuery); ?>" />
                    <button type="submit">Search</button>
                    <select name="year">
                        <option value="">Pilih Tahun</option>
                        <?php for ($year = date('Y'); $year >= 2000; $year--): ?>
                            <option value="<?= $year; ?>" <?= $filterYear == $year ? 'selected' : ''; ?>><?= $year; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="month">
                        <option value="">Pilih Bulan</option>
                        <?php
                        $months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                        foreach ($months as $key => $month): ?>
                            <option value="<?= $key + 1; ?>" <?= $filterMonth == $key + 1 ? 'selected' : ''; ?>><?= $month; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Filter</button>
                </form>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Surat</th>
                        <th>Perihal</th>
                        <th>Tanggal Surat</th>
                        <th>Penerima</th>
                        <th>Sifat</th>
                        <th>Dokumen</th>
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
                                <td><?= htmlspecialchars($row['dokumen_surat']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="export-buttons">
                <a href="export_arsip_keluar.php?search=<?= urlencode($searchQuery); ?>&year=<?= $filterYear; ?>&month=<?= $filterMonth; ?>" class="btn btn-success">Export ke Excel</a>
            </div>

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