<?php
// Start the session
session_start();

// Include the database connection file
include('database.php');

// Prepare the SQL statement to fetch all auctions
$stmt = $connection->prepare('
    SELECT auctions.*, categories.name AS category_name, MAX(bids.bid_amount) AS highest_bid
    FROM auctions
    LEFT JOIN categories ON auctions.categoryId = categories.id
    LEFT JOIN bids ON auctions.id = bids.auctionId
    GROUP BY auctions.id
    ORDER BY auctions.endDate ASC
    LIMIT 10
');

// Execute the statement
$stmt->execute();

// Fetch the results
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css">

 
</head>

<body>
    <header>
        <h1><span class="C">C</span><span class="a">a</span><span class="r">r</span><span class="b">b</span><span class="u">u</span><span class="y">y</span></h1>

        <form action="#">
            <input type="text" name="search" placeholder="Search for a car" />
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
            <li><a class="categoryLink" href="#">Estate</a></li>
            <li><a class="categoryLink" href="#">Electric</a></li>
            <li><a class="categoryLink" href="#">Coupe</a></li>
            <li><a class="categoryLink" href="#">Saloon</a></li>
            <li><a class="categoryLink" href="#">4x4</a></li>
            <li><a class="categoryLink" href="#">Sports</a></li>
            <li><a class="categoryLink" href="#">Hybrid</a></li>
            <li><a class="categoryLink" href="#">More</a></li>

            <!-- Add Auction Link (only visible to logged-in users) -->
            <?php if (isset($_SESSION['user'])): ?>
                <li><a class="categoryLink" href="addAuction.php">Add Auction</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <img src="banners/1.jpg" alt="Banner" />

    <main>
        <h1>Latest Car Listings</h1>
        <ul class="carList">
            <?php foreach ($auctions as $auction): ?>
                <li>
                    <img src="images/<?php echo htmlspecialchars($auction['imagePath'] ?? 'default.jpg'); ?>" alt="Car Image" width="100" />
                    <article>
                        <h2><?php echo htmlspecialchars($auction['title']); ?></h2>
                        <p><?php echo htmlspecialchars($auction['description']); ?></p>
                        <h3>Category: <?php echo htmlspecialchars($auction['category_name']); ?></h3>
                        <p><strong>End Date:</strong> <?php echo isset($auction['endDate']) ? htmlspecialchars($auction['endDate']) : "Not available"; ?></p>
                        <p class="price">Current bid: £<?php echo isset($auction['highest_bid']) ? htmlspecialchars($auction['highest_bid']) : "No bids yet"; ?></p>
                        <a href="make_bid.php?id=<?php echo $auction['id']; ?>" class="more auctionLink">Bid &gt;&gt;</a>

                        <!-- Edit and Delete Buttons (next to each other) -->
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] === $auction['userId']): ?>
                            <div class="auction-action-buttons" style="display: inline-block; margin-top: 10px;">
                                <!-- Edit Button -->
                                <a href="editAuction.php?id=<?php echo $auction['id']; ?>"><button>Edit</button></a>

                                <!-- Delete Button -->
                                <form action="deleteAuction.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this auction?');">
                                    <input type="hidden" name="id" value="<?php echo $auction['id']; ?>" />
                                    <input type="submit" value="Delete" />
                                </form>
                            </div>
                        <?php endif; ?>
                    </article>
                   
                </li>
            <?php endforeach; ?>
			
        </ul>
    </main>
      
    <footer>
        &copy; Carbuy 2025
    </footer>
</body>
</html>
