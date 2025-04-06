<?php
// Start the session
session_start();

// Include the database connection
include('database.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $name = $_POST['name'];

    // Hash the password before storing
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert the user into the database
    $stmt = $connection->prepare('INSERT INTO users (email, password, name) VALUES (:email, :password, :name)');
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':name', $name);

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: Unable to register user.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form action="register.php" method="POST">
            <label>Email:</label>
            <input type="email" name="email" required />
            <br />
            <label>Password:</label>
            <input type="password" name="password" required />
            <br />
            <label>Name:</label>
            <input type="text" name="name" required />
            <br />
            <input type="submit" value="Register" />
        </form>
        
        <p>
            Already have an account? <a href="login.php">Login here</a>
        </p>
        <p>
            Admin? <a href="admin.php">Go to Admin Panel</a>
        </p>
    </div>
</body>
</html>
