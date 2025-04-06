<?php
// Start session to check if the user is logged in
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page if not an admin
    header("Location: adminlogin.php");
    exit();
}

// Include the database connection
$connection = require 'database.php';  // This should return a PDO object

// Check if category ID is provided in the URL
if (isset($_GET['id'])) {
    $categoryId = $_GET['id'];

    // Try to delete the category from the database
    try {
        // Prepare the SQL query to delete the category
        $sql = "DELETE FROM category WHERE id = :id";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to the admin categories page after successful deletion
            header("Location: adminCategories.php");
            exit();
        } else {
            // Error if deletion fails
            echo "Error deleting category.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Category ID is required.";
}
?>
