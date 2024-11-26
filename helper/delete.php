<?php
include 'db.php';

if (isset($_GET['id']) && isset($_GET['table'])) {
    $id = $_GET['id'];
    $table = $_GET['table'];

    // Get the file path from the database
    $query = $conn->prepare("SELECT file_path FROM $table WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $file = $result->fetch_assoc();

    if ($file) {
        $filePath = $file['file_path'];

        // Delete the file from the server
        if (unlink($filePath)) {
            // Delete the record from the database
            $deleteQuery = $conn->prepare("DELETE FROM $table WHERE id = ?");
            $deleteQuery->bind_param("i", $id);
            $deleteQuery->execute();

            header("Location: index.php?deletesuccess");
        } else {
            echo "Error deleting file from server.";
        }
    } else {
        echo "File not found.";
    }
}
?>
