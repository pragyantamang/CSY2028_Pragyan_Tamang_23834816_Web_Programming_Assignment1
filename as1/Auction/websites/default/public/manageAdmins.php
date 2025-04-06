<?php
// Start the session
session_start();

// Include the database connection
include('database.php');



// Fetch all users/admins
$stmt = $connection->prepare('SELECT * FROM users');
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle user/admin editing
if (isset($_GET['edit_id'])) {
    $userId = $_GET['edit_id'];
    $stmt = $connection->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $userToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle form submission for editing user/admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $editId = $_POST['edit_id'];
    $newName = $_POST['name'];
    $newEmail = $_POST['email'];

    // Update the user/admin details
    $updateStmt = $connection->prepare('UPDATE users SET name = :name, email = :email WHERE id = :id');
    $updateStmt->bindParam(':name', $newName);
    $updateStmt->bindParam(':email', $newEmail);
    $updateStmt->bindParam(':id', $editId);

    if ($updateStmt->execute()) {
        $_SESSION['success'] = "User/admin updated successfully.";
        header("Location: manageAdmins.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: Unable to update user/admin details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>
    <header>
        <h1>Carbuy Auctions</h1>
    </header>

    <nav>
        <ul>
            <li><a href="adminpanel.php">Home</a></li>
            <li><a href="adminCategories.php">Manage Categories</a></li>
            <li><a href="addAdmin.php">Add Admin</a></li>
            <li><a href="manageAdmins.php">Manage Admins</a></li>
        </ul>
    </nav>

    <h1>Manage Admins</h1>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <p class="success-message"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <p class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <!-- Display the list of users/admins -->
    <?php if ($users): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <a href="manageAdmins.php?edit_id=<?php echo $user['id']; ?>">Edit</a>
                        |
                        <a href="deleteAdmin.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user/admin?')">Delete</a>

                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>

    <br />

    <!-- Edit User/Admin Form -->
    <h2>Edit User/Admin</h2>
    <?php if (isset($userToEdit)): ?>
        <form action="manageAdmins.php" method="POST">
            <input type="hidden" name="edit_id" value="<?php echo $userToEdit['id']; ?>" />
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($userToEdit['name']); ?>" required />
            <br />
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($userToEdit['email']); ?>" required />
            <br />
            <input type="submit" value="Update User/Admin" />
        </form>
    <?php endif; ?>

    <footer>
        &copy; Carbuy 2025
    </footer>
</body>
</html>
