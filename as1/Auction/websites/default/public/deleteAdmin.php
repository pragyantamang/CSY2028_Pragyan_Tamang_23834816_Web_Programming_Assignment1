<?php
// Start the session
session_start();

// Include the database connection
include('database.php');

// Check if the delete_id is provided
if (!isset($_GET['delete_id'])) {
    $_SESSION['error'] = "No user ID provided for deletion.";
    header("Location: manageAdmins.php");
    exit();
}

$deleteId = $_GET['delete_id'];

// Check if the user/admin exists
$stmt = $connection->prepare('SELECT * FROM users WHERE id = :id');
$stmt->bindParam(':id', $deleteId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = "User/Admin not found.";
    header("Location: manageAdmins.php");
    exit();
}

try {
    // Begin a transaction
    $connection->beginTransaction();

    // Step 1: Get all auction IDs created by the user
    $auctionIdsStmt = $connection->prepare('SELECT id FROM auctions WHERE userId = :userId');
    $auctionIdsStmt->bindParam(':userId', $deleteId);
    $auctionIdsStmt->execute();
    $auctionIds = $auctionIdsStmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($auctionIds)) {
        // Step 2: Delete dependent records in the bids table
        $deleteBidsStmt = $connection->prepare('DELETE FROM bids WHERE auctionId IN (' . implode(',', array_fill(0, count($auctionIds), '?')) . ')');
        $deleteBidsStmt->execute($auctionIds);

        // Step 3: Delete auctions created by the user
        $deleteAuctionsStmt = $connection->prepare('DELETE FROM auctions WHERE userId = :userId');
        $deleteAuctionsStmt->bindParam(':userId', $deleteId);
        $deleteAuctionsStmt->execute();
    }

    // Step 4: Delete the user/admin
    $deleteStmt = $connection->prepare('DELETE FROM users WHERE id = :id');
    $deleteStmt->bindParam(':id', $deleteId);
    $deleteStmt->execute();

    // Commit the transaction
    $connection->commit();

    $_SESSION['success'] = "User/Admin and related records deleted successfully!";
} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $connection->rollBack();
    $_SESSION['error'] = "Error: Unable to delete the user/admin. " . $e->getMessage();
}

// Redirect to the manage admins page
header("Location: manageAdmins.php");
exit();
?>
