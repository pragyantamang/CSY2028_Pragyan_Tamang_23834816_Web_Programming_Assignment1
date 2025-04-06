<?php
// Start the session
session_start();
include('database.php');

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Fetch the auction ID from URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php"); // Redirect to dashboard if no auction ID is passed
    exit();
}
$auctionId = $_GET['id'];

// Fetch the auction data
$stmt = $connection->prepare('
    SELECT auctions.*, users.id AS user_id
    FROM auctions
    LEFT JOIN users ON auctions.userId = users.id
    WHERE auctions.id = :id
');
$stmt->bindParam(':id', $auctionId);
$stmt->execute();
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if auction exists and belongs to the logged-in user
if (!$auction || $auction['user_id'] != $_SESSION['user']['id']) {
    // Redirect if the auction doesn't exist or if the user isn't the owner
    header("Location: dashboard.php"); // Redirect to dashboard
    exit();
}

// Handle form submission to update auction
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update the auction data without price and imagePath
    $title = $_POST['title'];
    $description = $_POST['description'];
    $categoryId = $_POST['categoryId'];
    $endDate = $_POST['endDate'];

    $updateStmt = $connection->prepare('
        UPDATE auctions SET
            title = :title,
            description = :description,
            categoryId = :categoryId,
            endDate = :endDate
        WHERE id = :id AND userId = :userId
    ');

    $updateStmt->bindParam(':title', $title);
    $updateStmt->bindParam(':description', $description);
    $updateStmt->bindParam(':categoryId', $categoryId);
    $updateStmt->bindParam(':endDate', $endDate);
    $updateStmt->bindParam(':id', $auctionId);
    $updateStmt->bindParam(':userId', $_SESSION['user']['id']);
    $updateStmt->execute();

    // Show success message and redirect to the dashboard
    $_SESSION['success'] = "Auction updated successfully!";
    header("Location: dashboard.php"); // Redirect to the dashboard
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Auction</title>
    <link rel="stylesheet" href="editAuction.css" />
   
</head>
<body>
    <header>
        <h1>Edit Auction</h1>
    </header>

    <main>
        <!-- Display success message if available -->
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success-message"><?php echo $_SESSION['success']; ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form action="editAuction.php?id=<?php echo $auctionId; ?>" method="POST" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($auction['title']); ?>" required />

            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?php echo htmlspecialchars($auction['description']); ?></textarea>

            <label for="categoryId">Category (optional):</label>
            <select name="categoryId" id="categoryId">
                <option value="1" <?php echo ($auction['categoryId'] == 1) ? 'selected' : ''; ?>>Estate</option>
                <option value="2" <?php echo ($auction['categoryId'] == 2) ? 'selected' : ''; ?>>Electric</option>
                <option value="3" <?php echo ($auction['categoryId'] == 3) ? 'selected' : ''; ?>>Coupe</option>
                <option value="4" <?php echo ($auction['categoryId'] == 4) ? 'selected' : ''; ?>>Saloon</option>
                <option value="5" <?php echo ($auction['categoryId'] == 5) ? 'selected' : ''; ?>>4x4</option>
                <option value="6" <?php echo ($auction['categoryId'] == 6) ? 'selected' : ''; ?>>Sports</option>
                <option value="7" <?php echo ($auction['categoryId'] == 7) ? 'selected' : ''; ?>>Hybrid</option>
                <option value="8" <?php echo ($auction['categoryId'] == 8) ? 'selected' : ''; ?>>More</option>
            </select>

            <label for="endDate">End Date:</label>
            <input type="datetime-local" name="endDate" id="endDate" value="<?php echo htmlspecialchars($auction['endDate']); ?>" required />

            <input type="submit" value="Update Auction" />
        </form>
    </main>

    <footer>
        &copy; Carbuy 2025
    </footer>
</body>
</html>
