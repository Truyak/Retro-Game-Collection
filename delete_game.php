<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: games.php");
    exit;
}

$db = new Database();
$conn = $db->connect();
$user_id = $_SESSION['user_id'];
$game_id = $_GET['id'];

$sql = "DELETE FROM games WHERE id = :id AND user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $game_id);
$stmt->bindParam(':user_id', $user_id);

try {
    $stmt->execute();
    header("Location: games.php?success=deleted");
    exit;
} catch(PDOException $e) {
    header("Location: games.php?error=delete_failed");
    exit;
}
?>