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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the category name from the form submission
    $categoryName = trim($_POST['name']);

    // Validate the category name (ensure it's not empty)
    if (empty($categoryName)) {
        $errorMessage = "Category name is required.";
    } else {
        // Prepare the SQL query to insert the new category into the database
        try {
            $sql = "INSERT INTO category (name) VALUES (:name)";
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':name', $categoryName, PDO::PARAM_STR);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect to the admin categories page after successful insertion
                header("Location: adminCategories.php");
                exit();
            } else {
                $errorMessage = "Error adding category.";
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
    <title>Add Category - Admin</title>
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
            >
            <!-- Home link to adminpanel.php -->
            <li><a href="adminpanel.php">Home</a></li>
            <!-- Link to manage categories page -->
            <li><a href="adminCategories.php">Manage Categories</a></li>
            <!-- Link to manage addAdmin.php -->
            <li><a href="addAdmin.php">Add Admin</a></li>
            <!-- Link to manage admins page -->
            <li><a href="manageAdmins.php">Manage Admins</a></li>
        </ul>
    </nav>

    <img src="banners/1.jpg" alt="Banner" />

    <main>
        <h2>Add New Category</h2>

        <!-- Display error message if any -->
        <?php if (!empty($errorMessage)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <!-- Form to add a new category -->
        <form action="addCategory.php" method="POST">
            <label for="name">Category Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($categoryName); ?>" required>
            <br><br>
            <input type="submit" value="Add Category">
        </form>
    </main>

    <footer>
        &copy; Carbuy 2025
    </footer>
</body>
</html>
