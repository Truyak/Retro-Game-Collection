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

if (!isset($_GET['id'])) {
    header("Location: games.php");
    exit;
}

$game_id = $_GET['id'];
$sql = "SELECT * FROM games WHERE id = :id AND user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $game_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    header("Location: games.php");
    exit;
}

$platform_options = ['NES', 'SNES', 'Sega Genesis', 'PlayStation 1'];
$is_other_platform = !in_array($game['platform'], $platform_options);
$selected_platform = $is_other_platform ? 'Other' : $game['platform'];

$region_options = ['NTSC-U', 'PAL', 'NTSC-J'];
$is_other_region = !in_array($game['region'], $region_options);
$selected_region = $is_other_region ? 'Other' : $game['region'];

$condition_options = ['New', 'Used', 'Damaged'];
$is_other_condition = !in_array($game['condition'], $condition_options);
$selected_condition = $is_other_condition ? 'Other' : $game['condition'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        $errors[] = "Invalid purchase price.";
    }

    if (empty($errors)) {
        $sql = "UPDATE games SET title = :title, platform = :platform, region = :region, `condition` = :condition, 
                box_manual = :box_manual, purchase_date = :purchase_date, purchase_price = :purchase_price, notes = :notes 
                WHERE id = :id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':platform', $platform);
        $stmt->bindParam(':region', $region);
        $stmt->bindParam(':condition', $condition);
        $stmt->bindParam(':box_manual', $box_manual);
        $stmt->bindParam(':purchase_date', $purchase_date);
        $stmt->bindParam(':purchase_price', $purchase_price);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':id', $game_id);
        $stmt->bindParam(':user_id', $user_id);

        try {
            $stmt->execute();
            header("Location: games.php?success=updated");
            exit;
        } catch(PDOException $e) {
            $errors[] = "Failed to update game: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Game - Retro Game Collection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Edit Game</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="" id="edit-game-form">
            <div class="mb-3">
                <label for="title" class="form-label">Game Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($game['title']); ?>" maxlength="100" required title="The name of the game (e.g., Super Mario Bros)">
            </div>
            <div class="mb-3">
                <label for="platform" class="form-label">Platform</label>
                <select class="form-control" id="platform" name="platform" required title="The gaming console (e.g., NES, SNES)">
                    <option value="NES" <?php if ($selected_platform == 'NES') echo 'selected'; ?>>NES</option>
                    <option value="SNES" <?php if ($selected_platform == 'SNES') echo 'selected'; ?>>SNES</option>
                    <option value="Sega Genesis" <?php if ($selected_platform == 'Sega Genesis') echo 'selected'; ?>>Sega Genesis</option>
                    <option value="PlayStation 1" <?php if ($selected_platform == 'PlayStation 1') echo 'selected'; ?>>PlayStation 1</option>
                    <option value="Other" <?php if ($selected_platform == 'Other') echo 'selected'; ?>>Other</option>
                </select>
                <input type="text" class="form-control mt-2" id="platform_other" name="platform_other" style="<?php echo $is_other_platform ? '' : 'display: none;'; ?>" maxlength="50" value="<?php echo $is_other_platform ? htmlspecialchars($game['platform']) : ''; ?>" placeholder="Enter custom platform" title="Custom platform name (e.g., Game Boy)">
            </div>
            <div class="mb-3">
                <label for="region" class="form-label">Region</label>
                <select class="form-control" id="region" name="region" required title="The region format of the game (e.g., NTSC-U for North America)">
                    <option value="NTSC-U" <?php if ($selected_region == 'NTSC-U') echo 'selected'; ?>>NTSC-U (North America)</option>
                    <option value="PAL" <?php if ($selected_region == 'PAL') echo 'selected'; ?>>PAL (Europe)</option>
                    <option value="NTSC-J" <?php if ($selected_region == 'NTSC-J') echo 'selected'; ?>>NTSC-J (Japan)</option>
                    <option value="Other" <?php if ($selected_region == 'Other') echo 'selected'; ?>>Other</option>
                </select>
                <input type="text" class="form-control mt-2" id="region_other" name="region_other" style="<?php echo $is_other_region ? '' : 'display: none;'; ?>" maxlength="50" value="<?php echo $is_other_region ? htmlspecialchars($game['region']) : ''; ?>" placeholder="Enter custom region" title="Custom region (e.g., NTSC-C for China)">
            </div>
            <div class="mb-3">
                <label for="condition" class="form-label">Condition</label>
                <select class="form-control" id="condition" name="condition" required title="Physical condition of the game (e.g., New, Used)">
                    <option value="New" <?php if ($selected_condition == 'New') echo 'selected'; ?>>New</option>
                    <option value="Used" <?php if ($selected_condition == 'Used') echo 'selected'; ?>>Used</option>
                    <option value="Damaged" <?php if ($selected_condition == 'Damaged') echo 'selected'; ?>>Damaged</option>
                    <option value="Other" <?php if ($selected_condition == 'Other') echo 'selected'; ?>>Other</option>
                </select>
                <input type="text" class="form-control mt-2" id="condition_other" name="condition_other" style="<?php echo $is_other_condition ? '' : 'display: none;'; ?>" maxlength="50" value="<?php echo $is_other_condition ? htmlspecialchars($game['condition']) : ''; ?>" placeholder="Enter custom condition" title="Custom condition (e.g., Refurbished)">
            </div>
            <div class="mb-3">
                <label for="box_manual" class="form-label">Box/Manual</label>
                <select class="form-control" id="box_manual" name="box_manual" required title="Does the game include its original box and manual?">
                    <option value="No" <?php if ($game['box_manual'] == 'No') echo 'selected'; ?>>No</option>
                    <option value="Yes" <?php if ($game['box_manual'] == 'Yes') echo 'selected'; ?>>Yes</option>
                    <option value="Partial" <?php if ($game['box_manual'] == 'Partial') echo 'selected'; ?>>Partial</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="purchase_date" class="form-label">Purchase Date</label>
                <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?php echo $game['purchase_date']; ?>" title="The date you purchased the game">
            </div>
            <div class="mb-3">
                <label for="purchase_price" class="form-label">Purchase Price ($)</label>
                <input type="number" class="form-control" id="purchase_price" name="purchase_price" step="0.01" value="<?php echo $game['purchase_price']; ?>" title="The price you paid for the game (in USD)">
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="4" maxlength="1000" title="Additional notes about the game (e.g., special edition)"><?php echo htmlspecialchars($game['notes']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Game</button>
            <a href="games.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>