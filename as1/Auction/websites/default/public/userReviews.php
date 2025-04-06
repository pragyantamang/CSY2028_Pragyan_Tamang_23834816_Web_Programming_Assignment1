<?php
session_start();
include('database.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user']['id'];
    $auctionId = $_POST['auctionId'];
    $reviewText = trim($_POST['reviewText']);

    if (!empty($reviewText)) {
        $stmt = $connection->prepare('
            INSERT INTO reviews (userId, auctionId, reviewText, createdate)
            VALUES (:userId, :auctionId, :reviewText, NOW())
        ');
        $stmt->execute([
            ':userId' => $userId,
            ':auctionId' => $auctionId,
            ':reviewText' => $reviewText
        ]);
    }
}

header("Location: dashboard.php");
exit();
?>
