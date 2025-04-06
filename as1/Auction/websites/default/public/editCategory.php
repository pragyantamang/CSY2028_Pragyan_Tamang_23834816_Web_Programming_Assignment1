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

// Initialize variables for form data and error messages
$categoryName = '';
$errorMessage = '';

// Check if a category ID is provided in the URL
if (isset($_GET['id'])) {
    $categoryId = $_GET['id'];

    // Fetch the category from the database
    try {
        $sql = "SELECT * FROM category WHERE id = :id";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Check if the category was found
        $category = $stmt->fetch();
        if ($category) {
            $categoryName = $category['name'];  // Get the category name
        } else {
            $errorMessage = "Category not found.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
} else {
    $errorMessage = "Category ID is required.";
}

// Handle form submission for updating category
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the updated category name from the form submission
    $categoryName = trim($_POST['name']);

    // Validate the category name (ensure it's not empty)
    if (empty($categoryName)) {
        $errorMessage = "Category name is required.";
    } else {
        // Prepare the SQL query to update the category
        try {
            $sql = "UPDATE category SET name = :name WHERE id = :id";
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':name', $categoryName, PDO::PARAM_STR);
            $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);

            // Execute the query to update the category
            if ($stmt->execute()) {
                // Redirect to the admin categories page after successful update
                header("Location: adminCategories.php");
                exit();
            } else {
                $errorMessage = "Error updating category.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category - Admin</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>

<body>
    <header>
        <h1><span class="C">C</span><span class="a">a</span><span class="r">r</span><span class="b">b</span><span class="u">u</span><span class="y">y</span></h1>

        <form action="#">
            <input type="text" name="search" placeholder="Search for a car" />
            <input type="submit" name="submit" value="Search" />
        </form>
    </header>

    <nav>
        <ul>
            <!-- Add link to admin categories page -->
            <li><a href="adminCategories.php">Manage Categories</a></li>
        </ul>
    </nav>

    <img src="banners/1.jpg" alt="Banner" />

    <main>
        <h2>Edit Category</h2>

        <!-- Display error message if any -->
        <?php if (!empty($errorMessage)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <!-- Form to edit the category -->
        <form action="editCategory.php?id=<?php echo $categoryId; ?>" method="POST">
            <label for="name">Category Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($categoryName); ?>" required>
            <br><br>
            <input type="submit" value="Update Category">
        </form>
    </main>

    <footer>
        &copy; Carbuy 2025
    </footer>
</body>
</html>
