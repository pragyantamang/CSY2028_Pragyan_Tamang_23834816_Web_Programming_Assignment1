<?php
// Start the session
session_start();

// Include the database connection file
include('database.php');

// Check if the user is logged in and has provided an auction ID
if (!isset($_SESSION['user']) || !isset($_POST['id'])) {
    // Redirect to the dashboard if not authorized
    header("Location: dashboard.php");
    exit();
}

// Sanitize the auction ID to prevent SQL injection
$auctionId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

// Begin a transaction
$connection->beginTransaction();

try {
    // Delete bids associated with the auction
    $stmt = $connection->prepare('DELETE FROM bids WHERE auctionId = :id');
    $stmt->bindParam(':id', $auctionId, PDO::PARAM_INT);
    $stmt->execute();

    // Delete the auction
    $stmt = $connection->prepare('DELETE FROM auctions WHERE id = :id AND userId = :userId');
    $stmt->bindParam(':id', $auctionId, PDO::PARAM_INT);
    $stmt->bindParam(':userId', $_SESSION['user']['id'], PDO::PARAM_INT);
    $stmt->execute();

    // Commit the transaction
    $connection->commit();

    // Redirect to the dashboard with a success message
    $_SESSION['message'] = 'Auction and its bids deleted successfully.';
} catch (Exception $e) {
    // Rollback the transaction if an error occurs
    $connection->rollBack();

    // Log the error message
    error_log($e->getMessage());

    // Redirect to the dashboard with an error message
    $_SESSION['message'] = 'Failed to delete auction: ' . $e->getMessage();
}

header("Location: dashboard.php");
exit();
?>
