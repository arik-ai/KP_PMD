<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id']; 

    // untuk hapus data
    $sql = "DELETE FROM surat_keputusan WHERE id_keputusan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus!'); window.location.href='surat_perjanjian_keputusan.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// halaman
$perPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$filterYear = isset($_GET['year']) ? (int)$_GET['year'] : '';
$filterMonth = isset($_GET['month']) ? (int)$_GET['month'] : '';

$conditions = "";
$params = [];
$paramTypes = "";

$searchWildcard = "%$searchQuery%";

if ($searchQuery !== '') {
    $conditions .= "(no_keputusan LIKE ? OR perihal_keputusan LIKE ?)";
    array_push($params, $searchWildcard, $searchWildcard);
    $paramTypes .= "ss";
}

if ($filterYear) {
    if ($conditions !== "") $conditions .= " AND ";
    $conditions .= "YEAR(tgl_keputusan) = ?";
    array_push($params, $filterYear);
    $paramTypes .= "i";
}

if ($filterMonth) {
    if ($conditions !== "") $conditions .= " AND ";
    $conditions .= "MONTH(tgl_keputusan) = ?";
    array_push($params, $filterMonth);
    $paramTypes .= "i";
}

if ($conditions === "") {
    $conditions = "1";
}

// Hitung total data
$totalQuery = "SELECT COUNT(*) AS total FROM surat_keputusan WHERE (no_keputusan LIKE ? OR perihal_keputusan LIKE ?) AND $conditions";
$stmtTotal = $conn->prepare($totalQuery);

$paramsTotal = array_merge([$searchWildcard, $searchWildcard], $params);

// Menentukan tipe parameter untuk total data
$totalParamTypes = "ss" . $paramTypes;

// Bind parameter untuk total data
$stmtTotal->bind_param($totalParamTypes, ...$paramsTotal);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalData = $resultTotal->fetch_assoc()['total'];
$totalPages = ceil($totalData / $perPage);

// Query data dengan limit dan offset
$sql = "SELECT * FROM surat_keputusan WHERE (no_keputusan LIKE ? OR perihal_keputusan LIKE ?) AND $conditions LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

// Gabungkan parameter pencarian dengan parameter lainnya
$paramsData = array_merge([$searchWildcard, $searchWildcard], $params);
$paramsData[] = $perPage; 
$paramsData[] = $offset; 

// Menentukan tipe parameter untuk data
$dataParamTypes = "ss" . $paramTypes . "ii"; 

// Bind parameter untuk data
$stmt->bind_param($dataParamTypes, ...$paramsData);
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
            width: 40%;
        }

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
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo" />
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="surat_masuk.php" ><span class="icon">📂</span> Data Surat Masuk</a></li>
            <li><a href="surat_keluar.php"><span class="icon">📤</span> Data Surat Keluar</a></li>
            <li><a href="surat_perjanjian_keputusan.php" ><span class="icon">📜</span> Surat Perjanjian keputusan</a></li>
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
            <h2>Daftar Surat keputusan</h2>

            <div class="search-bar">
                <form action="arsip_keputusan.php" method="GET">
                    <input type="text" name="search" placeholder="Pencarian" value="<?= htmlspecialchars($searchQuery); ?>" />
                    <button class="search-button" type="submit">Search</button> <!-- Tombol Search -->
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
                    <button class="filter-button" type="submit">Filter</button> <!-- Tombol Filter -->
                </form>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Surat</th>
                        <th>Perihal</th>
                        <th>Tanggal</th>
                        <th>Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['no_keputusan']); ?></td>
                                <td><?= htmlspecialchars($row['perihal_keputusan']); ?></td>
                                <td><?= htmlspecialchars($row['tgl_keputusan']); ?></td>
                                <td><?= htmlspecialchars($row['dokumen_keputusan']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Tidak ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Halaman -->
            <ul class="pagination">
                <?php if ($currentPage > 1): ?>
                    <li><a href="?page=<?= $currentPage - 1; ?>&search=<?= htmlspecialchars($searchQuery); ?>">« Prev</a></li>
                <?php else: ?>
                    <li class="disabled"><span>« Prev</span></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li>
                        <a href="?page=<?= $i; ?>&search=<?= htmlspecialchars($searchQuery); ?>" class="<?= $i === $currentPage ? 'active' : ''; ?>">
                            <?= $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <li><a href="?page=<?= $currentPage + 1; ?>&search=<?= htmlspecialchars($searchQuery); ?>">Next »</a></li>
                <?php else: ?>
                    <li class="disabled"><span>Next »</span></li>
                <?php endif; ?>
            </ul>
            <div class="export-buttons">
                <a href="export_arsip_keputusan.php?search=<?= urlencode($searchQuery); ?>&year=<?= $filterYear; ?>&month=<?= $filterMonth; ?>" class="btn btn-success">Export ke Excel</a>
            </div>
        </div>
    </div>
</body>
</html>
