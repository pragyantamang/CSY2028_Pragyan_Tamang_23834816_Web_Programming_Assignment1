<?php
// Start the session
session_start();

// Include database connection
include('database.php');

// Check if a category ID is passed via the URL
if (isset($_GET['categoryId'])) {
    $categoryId = $_GET['categoryId'];

    // Prepare and execute query to get auctions for the specific category
    $stmt = $connection->prepare('
        SELECT a.*, c.name AS category_name
        FROM auction a
        JOIN categories c ON a.categoryId = c.id
        WHERE a.categoryId = :categoryId
        ORDER BY a.endDate DESC
    ');
    $stmt->bindParam(':categoryId', $categoryId);
    $stmt->execute();
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get category name
    $stmtCategory = $connection->prepare('SELECT name FROM categories WHERE id = :categoryId');
    $stmtCategory->bindParam(':categoryId', $categoryId);
    $stmtCategory->execute();
    $category = $stmtCategory->fetch(PDO::FETCH_ASSOC);
} else {
    // Redirect to homepage if no category is passed
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auctions in <?php echo htmlspecialchars($category['name']); ?></title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>
    <header>
        <h1><span class="C">C</span><span class="a">a</span><span class="r">r</span><span class="b">b</span><span class="u">u</span><span class="y">y</span></h1>
        <nav>
            <ul>
                <li><a class="categoryLink" href="category.php?categoryId=1">Estate</a></li>
                <li><a class="categoryLink" href="category.php?categoryId=2">Electric</a></li>
                <li><a class="categoryLink" href="category.php?categoryId=3">Coupe</a></li>
                <li><a class="categoryLink" href="category.php?categoryId=4">Saloon</a></li>
                <li><a class="categoryLink" href="category.php?categoryId=5">4x4</a></li>
                <li><a class="categoryLink" href="category.php?categoryId=6">Sports</a></li>
                <li><a class="categoryLink" href="category.php?categoryId=7">Hybrid</a></li>
                <li><a class="categoryLink" href="category.php?categoryId=8">More</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Auctions in <?php echo htmlspecialchars($category['name']); ?> Category</h1>

        <?php if ($auctions): ?>
            <ul class="carList">
                <?php foreach ($auctions as $auction): ?>
                    <li>
                        <img src="car.png" alt="Car image">
                        <article>
                            <h2><?php echo htmlspecialchars($auction['title']); ?></h2>
                            <h3><?php echo htmlspecialchars($auction['category_name']); ?></h3>
                            <p><?php echo htmlspecialchars($auction['description']); ?></p>
                            <p class="price">Current bid: £<?php echo number_format($auction['currentBid'], 2); ?></p>
                            <a class="more auctionLink" href="auction.php?id=<?php echo $auction['id']; ?>">More &gt;&gt;</a>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No auctions available in this category at the moment.</p>
        <?php endif; ?>
    </main>

    <footer>
        &copy; Carbuy 2025
    </footer>
</body>
</html>
