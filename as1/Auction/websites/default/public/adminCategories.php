<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page if not an admin
    header("Location: adminlogin.php");
    exit();
}

// Include the database connection
$connection = require 'database.php';  // This should return a PDO object

// Query to fetch all categories from the database
try {
    $sql = "SELECT * FROM category";
    $stmt = $connection->query($sql);  // Execute the query using the valid PDO object
    $categories = $stmt->fetchAll();  // Fetch all categories from the database
} catch (PDOException $e) {
    // If the query fails, catch the exception and display an error message
    echo "Error fetching categories: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbuy Auctions</title>
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
        <h2>Manage Categories</h2>

        <!-- Link to add a new category -->
        <p><a href="addCategory.php">Add New Category</a></p>

        <!-- Check if there are any categories -->
        <?php if (count($categories) > 0): ?>
            <table border="1">
                <tr>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td>
                            <!-- Link to edit category -->
                            <a href="editCategory.php?id=<?php echo $category['id']; ?>">Edit</a> | 
                            <!-- Link to delete category -->
                            <a href="deleteCategory.php?id=<?php echo $category['id']; ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No categories found.</p>
        <?php endif; ?>
    </main>

    <footer>
        &copy; Carbuy 2025
    </footer>
</body>
</html>
