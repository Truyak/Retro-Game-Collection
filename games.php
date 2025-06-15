<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM games WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_value = 0;
foreach ($games as $game) {
    if ($game['purchase_price']) {
        $total_value += $game['purchase_price'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Collection - Retro Game Collection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-5 display-4" style="font-family: 'Press Start 2P', cursive; font-size: 3.5rem; line-height: 1.2;">My Retro Game Collection</h2>
        <p class="text-center mb-4 h4">Total Collection Value: $<?php echo number_format($total_value, 2); ?></p>
        <div class="d-flex justify-content-center mb-4">
            <a href="add_game.php" class="btn btn-primary me-2">Add New Game</a>
            <a href="logout.php" class="btn btn-secondary">Log Out</a>
        </div>
        <?php if (empty($games)): ?>
            <p class="text-center">No games in your collection yet. Add one now!</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-dark custom-table w-100">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Platform</th>
                            <th>Region</th>
                            <th>Condition</th>
                            <th>Box/Manual</th>
                            <th>Purchase Date</th>
                            <th>Purchase Price</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($games as $game): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($game['title']); ?></td>
                                <td><?php echo htmlspecialchars($game['platform']); ?></td>
                                <td><?php echo htmlspecialchars($game['region'] ?: '-'); ?></td>
                                <td><?php echo htmlspecialchars($game['condition'] ?: '-'); ?></td>
                                <td><?php echo htmlspecialchars($game['box_manual']); ?></td>
                                <td><?php echo $game['purchase_date'] ? htmlspecialchars($game['purchase_date']) : '-'; ?></td>
                                <td><?php echo $game['purchase_price'] ? '$' . number_format($game['purchase_price'], 2) : '-'; ?></td>
                                <td><?php echo $game['notes'] ? htmlspecialchars($game['notes']) : '-'; ?></td>
                                <td>
                                    <a href="edit_game.php?id=<?php echo $game['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete_game.php?id=<?php echo $game['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this game?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>