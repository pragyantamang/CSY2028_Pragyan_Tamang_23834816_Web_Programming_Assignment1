<?php
// Start the session
session_start();
include('database.php'); // Ensure this file establishes a PDO connection as $connection

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Bid placing logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bid_amount'], $_POST['auctionId'])) {
    $bid_amount = $_POST['bid_amount'];
    $auctionId = $_POST['auctionId'];
    $userId = $_SESSION['user']['id'];

    if (!is_numeric($bid_amount) || $bid_amount <= 0) {
        echo "<p>Invalid bid amount. Please enter a positive number.</p>";
    } else {
        try {
            $stmt = $connection->prepare('SELECT * FROM auctions WHERE id = :auctionId');
            $stmt->bindParam(':auctionId', $auctionId, PDO::PARAM_INT);
            $stmt->execute();
            $auction = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($auction) {
                $stmt = $connection->prepare('SELECT MAX(bid_amount) AS highest_bid FROM bids WHERE auctionId = :auctionId');
                $stmt->bindParam(':auctionId', $auctionId, PDO::PARAM_INT);
                $stmt->execute();
                $highest_bid = $stmt->fetch(PDO::FETCH_ASSOC)['highest_bid'] ?? 0;

                if ($bid_amount > $highest_bid) {
                    $connection->beginTransaction();

                    $stmt = $connection->prepare('
                        INSERT INTO bids (auctionId, userId, bid_amount, bid_time) 
                        VALUES (:auctionId, :userId, :bid_amount, NOW())
                    ');
                    $stmt->bindParam(':auctionId', $auctionId, PDO::PARAM_INT);
                    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                    $stmt->bindParam(':bid_amount', $bid_amount);
                    $stmt->execute();

                    $stmt = $connection->prepare('
                        UPDATE auctions 
                        SET price = :price 
                        WHERE id = :auctionId
                    ');
                    $stmt->bindParam(':price', $bid_amount);
                    $stmt->bindParam(':auctionId', $auctionId, PDO::PARAM_INT);
                    $stmt->execute();

                    $connection->commit();

                    echo "<p>Your bid of £" . htmlspecialchars($bid_amount) . " has been placed successfully!</p>";
                } else {
                    echo "<p>Your bid must be higher than the current highest bid of £" . htmlspecialchars($highest_bid) . ".</p>";
                }
            }
        } catch (Exception $e) {
            $connection->rollBack();
            echo "<p>Error: Unable to place your bid. " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Place Bid</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="submit"] {
            padding: 8px;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        a {
            display: inline-block;
            margin-right: 10px;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1>Place Your Bid</h1>

<form method="POST" action="make_bid.php?id=<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>">
    <input type="hidden" name="auctionId" value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>" />
    
    <label for="bid_amount">Bid Amount (£):</label><br>
    <input type="text" id="bid_amount" name="bid_amount" required />
    <input type="submit" value="Place Bid" />
</form>

<!-- Display Bid History -->
<?php
if (isset($_GET['id'])) {
    $auctionId = $_GET['id'];

    try {
        $stmt = $connection->prepare("
            SELECT b.bid_amount, b.bid_time, u.name AS bidder_name
            FROM bids b
            JOIN users u ON b.userId = u.id
            WHERE b.auctionId = :auctionId
            ORDER BY b.bid_time DESC
        ");
        $stmt->bindParam(':auctionId', $auctionId, PDO::PARAM_INT);
        $stmt->execute();
        $bidHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($bidHistory) {
            echo "<h2>Bid History</h2>";
            echo "<table>";
            echo "<tr><th>Bidder</th><th>Amount (£)</th><th>Time</th></tr>";
            foreach ($bidHistory as $bid) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($bid['bidder_name']) . "</td>";
                echo "<td>£" . htmlspecialchars($bid['bid_amount']) . "</td>";
                echo "<td>" . htmlspecialchars($bid['bid_time']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No bids yet for this auction.</p>";
        }
    } catch (Exception $e) {
        echo "<p>Error retrieving bid history: " . $e->getMessage() . "</p>";
    }
}
?>

<!-- Navigation Links -->
<a href="javascript:history.back()">Go Back</a>
<a href="auctionList.php">Back to Auction List</a>

</body>
</html>
