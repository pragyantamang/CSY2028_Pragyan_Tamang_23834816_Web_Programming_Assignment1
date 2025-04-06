<?php
$servername = 'mysql';
$username = 'student';
$password = 'student';
$databasename = 'ijdb';

try {
    // Create a new PDO instance for the database connection
    $connection = new PDO('mysql:dbname=' . $databasename . ';host=' . $servername, $username, $password);
    // Set the PDO error mode to exception to handle errors
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If the connection fails, display the error message and terminate
    echo "Connection failed: " . $e->getMessage();
    exit;  // Stop further execution if the connection fails
}

return $connection;  // Return the PDO connection object
?>
