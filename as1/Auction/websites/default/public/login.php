<?php
// Start the session
session_start();

// Include the database connection
include('database.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the email and password from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if the email exists in the database
    $stmt = $connection->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If user exists and password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Store user details in the session
        $_SESSION['user'] = $user;
        // Redirect to the dashboard or home page
        header('Location: dashboard.php');
        exit();
    } else {
        // If credentials are incorrect, show an error message
        echo "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        
        <form action="login.php" method="POST">
            <label>Email:</label>
            <input type="email" name="email" required />
            <br />
            <label>Password:</label>
            <input type="password" name="password" required />
            <br />
            <input type="submit" value="Login" />
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
        <p>Admin?<a href="adminlogin.php">Admin Dashboard</a></p>
    </div>
</body>
</html>
