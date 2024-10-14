<?php
session_start();
include 'config.php';

// Initialize variables
$success = false;
$error = "";

// Check if the photo ID is set in the URL
if (isset($_GET['id_foto'])) {
    $id_foto = $conn->real_escape_string($_GET['id_foto']); // Sanitize input to prevent SQL injection

    // Query to fetch the photo details before deletion
    $sql = "SELECT LokasiFile FROM foto WHERE FotoID = '$id_foto'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $foto = $result->fetch_assoc();
        $fotoFile = $foto['LokasiFile'];

        // Attempt to delete the photo record from the database
        $deleteSql = "DELETE FROM foto WHERE FotoID = '$id_foto'";
        if ($conn->query($deleteSql) === TRUE) {
            // Delete the file from the server
            $filePath = 'uploads/' . basename($fotoFile); // Ensure this path is correct
            if (file_exists($filePath)) {
                unlink($filePath); // Remove the file
            }
            $success = true; // Mark as successful
        } else {
            $error = "Error deleting record: " . $conn->error; // Capture any error
        }
    } else {
        $error = "Photo not found."; // Photo does not exist
    }
} else {
    $error = "Invalid request."; // No ID provided
}

$conn->close();

// Redirect to dashboard with a message
if ($success) {
    header("Location: admin_dashboard.php?message=Photo deleted successfully.");
    exit();
} else {
    header("Location: admin_dashboard.php?error=" . urlencode($error));
    exit();
}
?>
