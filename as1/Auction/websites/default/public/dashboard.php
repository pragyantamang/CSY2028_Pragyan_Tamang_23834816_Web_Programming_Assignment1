<?php
// Start the session
session_start();
include('database.php');

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Fetch all auctions with category names and highest bids
$stmt = $connection->prepare('
    SELECT auctions.*, auctions.userId, categories.name AS category_name, MAX(bids.bid_amount) AS highest_bid
    FROM auctions
    LEFT JOIN categories ON auctions.categoryId = categories.id
    LEFT JOIN bids ON auctions.id = bids.auctionId
    GROUP BY auctions.id, auctions.userId
');
$stmt->execute();
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all reviews and group them by auctionId
$reviewStmt = $connection->prepare('SELECT * FROM reviews');
$reviewStmt->execute();
$allReviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

// Group reviews by auctionId
$reviewsByAuction = [];
foreach ($allReviews as $review) {
    $reviewsByAuction[$review['auctionId']][] = $review;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
    <style>
        .auction-action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .auction-action-buttons button, 
        .auction-action-buttons input[type="submit"] {
            width: 100px;
            padding: 10px;
            font-size: 14px;
            cursor: pointer;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: white;
            color: black;
        }
        .auction-action-buttons input[type="submit"]:hover {
            background-color: #f8f8f8;
            border-color: #888;
        }
        .review-form {
            margin-top: 15px;
        }
        .review-form textarea {
            width: 100%;
            height: 80px;
            padding: 10px;
            resize: vertical;
        }
        .review-form input[type="submit"] {
            margin-top: 5px;
            padding: 8px 12px;
        }
        .reviews {
            margin-top: 10px;
            padding: 10px;
            background-color: #f4f4f4;
            border-radius: 5px;
        }
        .review {
            margin-bottom: 8px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
    </style>
</head>

<body>
    <header>
        <h1><span class="C">C</span><span class="a">a</span><span class="r">r</span><span class="b">b</span><span class="u">u</span><span class="y">y</span></h1>

        <form action="search.php" method="GET">
            <input type="text" name="search" placeholder="Search for a car" />
            <input type="submit" name="submit" value="Search" />
        </form>

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
            <li><a class="categoryLink" href="">More</a></li>

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
                        <h3>Category: <?php echo !empty($auction['category_name']) ? htmlspecialchars($auction['category_name']) : "No Category"; ?></h3>
                        <p><strong>End Date:</strong> <?php echo !empty($auction['endDate']) ? htmlspecialchars($auction['endDate']) : "Not available"; ?></p>
                        <p class="price">Current bid: £<?php echo !empty($auction['highest_bid']) ? htmlspecialchars($auction['highest_bid']) : "No bids yet"; ?></p>

                        <a href="make_bid.php?id=<?php echo $auction['id']; ?>" class="more auctionLink">Bid &gt;&gt;</a>

                        <?php if ((int)$_SESSION['user']['id'] === (int)$auction['userId']): ?>
                            <div class="auction-action-buttons">
                                <a href="editAuction.php?id=<?php echo $auction['id']; ?>"><button>Edit</button></a>
                                <form action="deleteAuction.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this auction?');">
                                    <input type="hidden" name="id" value="<?php echo $auction['id']; ?>" />
                                    <input type="submit" value="Delete" />
                                </form>
                            </div>
                        <?php else: ?>
                            <!-- Show Add Review form if user is not the owner -->
                            <div class="review-form">
                                <form method="POST" action="userReviews.php">
                                    <input type="hidden" name="auctionId" value="<?php echo $auction['id']; ?>" />
                                    <label for="reviewText">Write a Review:</label><br>
                                    <textarea name="reviewText" required></textarea><br>
                                    <input type="submit" value="Submit Review" />
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- Display Reviews -->
                        <div class="reviews">
                            <h4>Reviews:</h4>
                            <?php if (!empty($reviewsByAuction[$auction['id']])): ?>
                                <?php foreach ($reviewsByAuction[$auction['id']] as $review): ?>
                                    <div class="review">
                                        <p><?php echo htmlspecialchars($review['reviewText']); ?></p>
                                        <small>By User ID: <?php echo htmlspecialchars($review['userId']); ?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No reviews yet.</p>
                            <?php endif; ?>
                        </div>
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
