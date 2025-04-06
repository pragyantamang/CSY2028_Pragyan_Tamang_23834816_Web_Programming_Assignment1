<?php
// Start the session
session_start();
include('database.php');

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get the search query from the URL or form submission
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the SQL query to search the auctions by title or description
$stmt = $connection->prepare('
    SELECT auctions.*, categories.name AS category_name, MAX(bids.bid_amount) AS highest_bid
    FROM auctions
    LEFT JOIN categories ON auctions.categoryId = categories.id
    LEFT JOIN bids ON auctions.id = bids.auctionId
    WHERE auctions.title LIKE :searchQuery OR auctions.description LIKE :searchQuery
    GROUP BY auctions.id
');
$stmt->execute([
    ':searchQuery' => '%' . $searchQuery . '%',
]);

$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>

<body>
    <header>
        <h1>Carbuy Auctions</h1>
        <form action="search.php" method="GET">
            <input type="text" name="search" placeholder="Search for a car" value="<?php echo htmlspecialchars($searchQuery); ?>" />
            <input type="submit" name="submit" value="Search" />
        </form>

        <!-- Login/Logout Button -->
        <?php if (isset($_SESSION['user'])): ?>
            <a href="logout.php"><button>Logout</button></a>
        <?php else: ?>
            <a href="login.php"><button>Login</button></a>
        <?php endif; ?>
    </header>
    <nav>
        <ul>
            <li><a class="categoryLink" href="Estate.php">Estate</a></li>
            <li><a class="categoryLink" href="Electric.php">Electric</a></li>
            <li><a class="categoryLink" href="Coupe.php">Coupe</a></li>
            <li><a class="categoryLink" href="Saloon.php">Saloon</a></li>
            <li><a class="categoryLink" href="4x4.php">4x4</a></li>
            <li><a class="categoryLink" href="Sports.php">Sports</a></li>
            <li><a class="categoryLink" href="Hybrid.php">Hybrid</a></li>
            <li><a class="categoryLink" href="dashboard.php">More</a></li>

            <!-- Add Auction Link (only visible to logged-in users) -->
            <?php if (isset($_SESSION['user'])): ?>
                <li><a class="categoryLink" href="addAuction.php">Add Auction</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <img src="banners/1.jpg" alt="Banner" />

    <main>
        <h1>Search Results</h1>
        <?php if (empty($auctions)): ?>
            <p>No results found for "<?php echo htmlspecialchars($searchQuery); ?>".</p>
        <?php else: ?>
            <ul class="carList">
                <?php foreach ($auctions as $auction): ?>
                    <li>
                        <img src="images/<?php echo htmlspecialchars($auction['imagePath'] ?? 'default.jpg'); ?>" alt="Car Image" width="100" />
                        <article>
                            <h2><?php echo htmlspecialchars($auction['title']); ?></h2>
                            <p><?php echo htmlspecialchars($auction['description']); ?></p>
                            <h3>Category: <?php echo htmlspecialchars($auction['category_name'] ?? 'No Category'); ?></h3>
                            <p><strong>End Date:</strong> <?php echo htmlspecialchars($auction['endDate']); ?></p>
                            <p class="price">Current bid: £<?php echo htmlspecialchars($auction['highest_bid'] ?? 'No bids yet'); ?></p>
                            <a href="make_bid.php?id=<?php echo $auction['id']; ?>">Bid &gt;&gt;</a>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </main>

    <footer>
        &copy; Carbuy 2025
    </footer>
</body>
</html>
