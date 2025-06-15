<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new Database();
    $conn = $db->connect();

    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $platform = trim($_POST['platform'] === 'Other' ? $_POST['platform_other'] : $_POST['platform']);
    $region = trim($_POST['region'] === 'Other' ? $_POST['region_other'] : $_POST['region']);
    $condition = trim($_POST['condition'] === 'Other' ? $_POST['condition_other'] : $_POST['condition']);
    $box_manual = trim($_POST['box_manual']);
    $purchase_date = !empty($_POST['purchase_date']) ? $_POST['purchase_date'] : null;
    $purchase_price = !empty($_POST['purchase_price']) ? floatval($_POST['purchase_price']) : null;
    $notes = trim($_POST['notes']);

    // Check Lengths
    $errors = [];
    if (strlen($title) > 100) {
        $errors[] = "Title must be 100 characters or less.";
    }
    if (strlen($platform) > 50) {
        $errors[] = "Platform must be 50 characters or less.";
    }
    if (strlen($region) > 50) {
        $errors[] = "Region must be 50 characters or less.";
    }
    if (strlen($condition) > 50) {
        $errors[] = "Condition must be 50 characters or less.";
    }
    if (strlen($notes) > 1000) {
        $errors[] = "Notes must be 1000 characters or less.";
    }
    if ($purchase_price !== null && $purchase_price < 0) {
        $errors[] = "Purchase price cannot be negative.";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO games (user_id, title, platform, region, `condition`, box_manual, purchase_date, purchase_price, notes) 
                VALUES (:user_id, :title, :platform, :region, :condition, :box_manual, :purchase_date, :purchase_price, :notes)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':platform', $platform);
        $stmt->bindParam(':region', $region);
        $stmt->bindParam(':condition', $condition);
        $stmt->bindParam(':box_manual', $box_manual);
        $stmt->bindParam(':purchase_date', $purchase_date);
        $stmt->bindParam(':purchase_price', $purchase_price);
        $stmt->bindParam(':notes', $notes);

        try {
            $stmt->execute();
            header("Location: games.php?success=added");
            exit;
        } catch(PDOException $e) {
            $errors[] = "Failed to add game: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Game - Retro Game Collection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Add New Game</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="" id="add-game-form">
            <div class="mb-3">
                <label for="title" class="form-label">Game Title</label>
                <input type="text" class="form-control" id="title" name="title" maxlength="100" required title="The name of the game (e.g., Super Mario Bros)">
            </div>
            <div class="mb-3">
                <label for="platform" class="form-label">Platform</label>
                <select class="form-control" id="platform" name="platform" required title="The gaming console (e.g., NES, SNES)">
                    <option value="">Select Platform</option>
                    <option value="NES">NES</option>
                    <option value="SNES">SNES</option>
                    <option value="Sega Genesis">Sega Genesis</option>
                    <option value="PlayStation 1">PlayStation 1</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" class="form-control mt-2" id="platform_other" name="platform_other" style="display: none;" maxlength="50" placeholder="Enter custom platform" title="Custom platform name (e.g., Game Boy)">
            </div>
            <div class="mb-3">
                <label for="region" class="form-label">Region</label>
                <select class="form-control" id="region" name="region" title="The region format of the game (e.g., NTSC-U for North America)">
                    <option value="">Select Region</option>
                    <option value="NTSC-U">NTSC-U (North America)</option>
                    <option value="PAL">PAL (Europe)</option>
                    <option value="NTSC-J">NTSC-J (Japan)</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" class="form-control mt-2" id="region_other" name="region_other" style="display: none;" maxlength="50" placeholder="Enter custom region" title="Custom region (e.g., NTSC-C for China)">
            </div>
            <div class="mb-3">
                <label for="condition" class="form-label">Condition</label>
                <select class="form-control" id="condition" name="condition" title="Physical condition of the game (e.g., New, Used)">
                    <option value="">Select Condition</option>
                    <option value="New">New</option>
                    <option value="Used">Used</option>
                    <option value="Damaged">Damaged</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" class="form-control mt-2" id="condition_other" name="condition_other" style="display: none;" maxlength="50" placeholder="Enter custom condition" title="Custom condition (e.g., Refurbished)">
            </div>
            <div class="mb-3">
                <label for="box_manual" class="form-label">Box/Manual</label>
                <select class="form-control" id="box_manual" name="box_manual" required title="Does the game include its original box and manual?">
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                    <option value="Partial">Partial</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="purchase_date" class="form-label">Purchase Date</label>
                <input type="date" class="form-control" id="purchase_date" name="purchase_date" title="The date you purchased the game">
            </div>
            <div class="mb-3">
                <label for="purchase_price" class="form-label">Purchase Price ($)</label>
                <input type="number" class="form-control" id="purchase_price" name="purchase_price" step="0.01" title="The price you paid for the game (in USD)">
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="4" maxlength="1000" title="Additional notes about the game (e.g., special edition)"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Add Game</button>
            <a href="games.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>