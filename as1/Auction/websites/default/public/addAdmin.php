<?php
// Start the session
session_start();

// Include the database connection
include('database.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // If not, redirect to login page
    header("Location: adminlogin.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the user form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $name = $_POST['name'];

    // Check if the email already exists in the users table
    $stmt = $connection->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        // If the email already exists, show an error message
        echo "A user with this email already exists.";
    } else {
        // Hash the password before storing
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert the new user into the users table
        $stmt = $connection->prepare('INSERT INTO users (email, password, name) VALUES (:email, :password, :name)');
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            echo "User account created successfully!";
        } else {
            echo "Error: Unable to create user account.";
        }
    }
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
        <h1>Add New User</h1>

        <!-- User Registration Form -->
        <form action="addAdmin.php" method="POST">
            <label for="name">User Name:</label>
            <input type="text" id="name" name="name" required />
            <br />
            
            <label for="email">User Email:</label>
            <input type="email" id="email" name="email" required />
            <br />
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required />
            <br />
            
            <input type="submit" value="Add User" />
        </form>

        <br />
        
    </main>

    <footer>
        &copy; Carbuy 2025
    </footer>
</body>
</html>
