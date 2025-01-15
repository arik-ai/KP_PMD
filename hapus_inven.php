<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM inventaris WHERE id_inventaris = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: data_inven.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
