<?php
session_start();
include('database.php');

// Define admin credentials
$admin_email = "admin123@gmail.com";
$admin_password = "admin123";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate credentials
    if ($email == $admin_email && $password == $admin_password) {
        // Set session for admin
        $_SESSION['admin_logged_in'] = true;
        // Redirect to admin panel
        header("Location: adminpanel.php");
        exit();
    } else {
        // Invalid login
        $error_message = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
    <form method="POST" action="">
        <label for="email">Email:</label><br>
        <input type="email" name="email" required><br><br>
        
        <label for="password">Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <input type="submit" value="Login">
    </form>

    <?php
    // Display error message if login fails
    if (isset($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
    }
    ?>
</body>
</html>
