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

// Fetch only estate car auctions along with their categories and bid amounts
$estateCategoryId = 1; // Replace with the actual categoryId for estate cars
$stmt = $connection->prepare('
    SELECT auctions.*, categories.name AS category_name, MAX(bids.bid_amount) AS highest_bid
    FROM auctions
    LEFT JOIN categories ON auctions.categoryId = categories.id
    LEFT JOIN bids ON auctions.id = bids.auctionId
    WHERE auctions.categoryId = :categoryId
    GROUP BY auctions.id
');
$stmt->bindParam(':categoryId', $estateCategoryId, PDO::PARAM_INT);
$stmt->execute();
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
    <style>
        /* Style for both Edit and Delete buttons to have the same size */
        .auction-action-buttons button {
            width: 100px; /* Set a fixed width */
            padding: 10px; /* Set padding for consistent size */
            margin-right: 10px; /* Add space between the buttons */
            cursor: pointer; /* Change the cursor on hover */
            font-size: 14px; /* Set a font size */
        }

        /* Style for Delete button */
        .auction-action-buttons input[type="submit"] {
            width: 100px; /* Set the same width as the Edit button */
            padding: 10px; /* Set padding for consistent size */
            font-size: 14px; /* Match the font size */
            cursor: pointer; /* Change the cursor on hover */
            background-color: white; /* Set background color to white */
            color: black; /* Set text color to black */
            border: 1px solid #ccc; /* Add a border for visibility */
            border-radius: 5px; /* Optional: make the edges rounded */
        }

        /* Optional: Add hover effect to Delete button */
        .auction-action-buttons input[type="submit"]:hover {
            background-color: #f8f8f8; /* Slight gray on hover */
            border-color: #888; /* Darker border on hover */
        }
    </style>
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
        <h1>Estate Car Listings</h1>
        <ul class="carList">
            <?php foreach ($auctions as $auction): ?>
                <li>
                    <img src="images/<?php echo htmlspecialchars($auction['imagePath'] ?? 'default.jpg'); ?>" alt="Car Image" width="100" />
                    <article>
                        <h2><?php echo htmlspecialchars($auction['title']); ?></h2>
                        <p><?php echo htmlspecialchars($auction['description']); ?></p>
                        <h3>Category: 
                            <?php 
                            echo isset($auction['category_name']) && !empty($auction['category_name']) ? htmlspecialchars($auction['category_name']) : "No Category";
                            ?>
                        </h3>
                        <p><strong>End Date:</strong> <?php 
                            echo isset($auction['endDate']) && !empty($auction['endDate']) ? htmlspecialchars($auction['endDate']) : "Not available";
                        ?></p>
                        <p class="price">Current bid: £
                            <?php 
                            echo isset($auction['highest_bid']) && !empty($auction['highest_bid']) ? htmlspecialchars($auction['highest_bid']) : "No bids yet";
                            ?>
                        </p>
                        <a href="make_bid.php?id=<?php echo $auction['id']; ?>" class="more auctionLink">Bid &gt;&gt;</a>
                        <?php if ($_SESSION['user']['id'] === $auction['userId']): ?>
                            <div class="auction-action-buttons" style="display: inline-block; margin-top: 10px;">
                                <a href="editAuction.php?id=<?php echo $auction['id']; ?>"><button>Edit</button></a>
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
