<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit;
}

include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM surat_masuk WHERE id_surat = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: surat_masuk.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
