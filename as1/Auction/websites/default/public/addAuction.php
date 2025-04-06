<?php
session_start();

// Include database connection
include('database.php');

// Check if the user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $categoryId = $_POST['category'];
    $endDate = $_POST['endDate'];
    $userId = $_SESSION['user']['id'];
    $initialPrice = 0.00; // Initial price for the auction

    // Handle image upload (optional)
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageName = basename($_FILES['image']['name']);
        $imagePath = 'images/auctions/' . $imageName;
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/' . $imagePath;

        if (!is_dir(dirname($uploadDir))) {
            mkdir(dirname($uploadDir), 0777, true);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir)) {
            // Successfully uploaded the image
        } else {
            $imagePath = null;
        }
    }

    // Insert the auction into the database
    $stmt = $connection->prepare('
        INSERT INTO auctions (title, description, categoryId, endDate, userId, imagePath, price)
        VALUES (:title, :description, :categoryId, :endDate, :userId, :imagePath, :price)
    ');

    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':categoryId', $categoryId);
    $stmt->bindParam(':endDate', $endDate);
    $stmt->bindParam(':userId', $userId);
    $stmt->bindParam(':imagePath', $imagePath);
    $stmt->bindParam(':price', $initialPrice);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Auction successfully added!";
        header('Location: dashboard.php');
        exit();
    } else {
        echo "There was an error adding the auction.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Auction</title>
    <link rel="stylesheet" href="addAuction.css"> <!-- Link to external CSS file -->
</head>
<body>
    <main>
        <h1>Add New Auction</h1>

        <form action="addAuction.php" method="POST" enctype="multipart/form-data">
            <label for="title">Auction Title:</label>
            <input type="text" name="title" id="title" required /><br>

            <label for="description">Description:</label>
            <textarea name="description" id="description" required></textarea><br>

            <label for="category">Category:</label>
            <select name="category" id="category" required>
                <?php
                // Fetch all categories from the database
                $stmt = $connection->prepare('SELECT * FROM categories');
                $stmt->execute();
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($categories as $category) {
                    echo "<option value='{$category['id']}'>{$category['name']}</option>";
                }
                ?>
            </select><br>

            <label for="endDate">Auction End Date:</label>
            <input type="date" name="endDate" id="endDate" required /><br>

            <label for="image">Upload Image (optional):</label>
            <input type="file" name="image" id="image" accept="image/*" /><br>

            <input type="submit" value="Add Auction" />
        </form>
    </main>

    <footer>
        &copy; Carbuy 2025
    </footer>
</body>
</html>
