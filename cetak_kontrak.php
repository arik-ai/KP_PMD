<?php
// Include database connection
include 'db.php';

// Define the directory where files are stored
$uploadDirectory = 'uploads/';

// Check if 'id' parameter is set
if (isset($_GET['id'])) {
    $id = (int) $_GET['id']; // Make sure the ID is an integer

    // Query to get the file path from the database
    $sql = "SELECT dokumen_kontrak FROM surat_kontrak WHERE id_kontrak = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if a file exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filePath = $row['dokumen_kontrak'];

        // Prepend the upload directory path
        $fullFilePath = $uploadDirectory . $filePath;

        // Check if file exists on the server
        if (file_exists($fullFilePath)) {
            // Set the appropriate headers to force download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($fullFilePath) . '"');
            header('Content-Length: ' . filesize($fullFilePath));
            header('Pragma: no-cache');
            header('Expires: 0');
            readfile($fullFilePath); // Read the file and send it to the user
            exit;
        } else {
            echo "File not found.";
        }
    } else {
        echo "Invalid document ID.";
    }
} else {
    echo "No document selected.";
}
?>
