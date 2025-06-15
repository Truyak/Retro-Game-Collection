<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retro Game Collection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container text-center mt-5">
        <h1 class="mb-4" style="font-family: 'Press Start 2P', cursive;">Retro Game Collection</h1>
        <p class="lead mb-4">Organize and track your retro game collection with ease! From NES to PlayStation, keep all your games in one place.</p>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <h3>Welcome back, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Friend'); ?>!</h3>
            <a href="games.php" class="btn btn-primary btn-lg mt-3">View Your Collection</a>
            <br>
            <a href="logout.php" class="btn btn-secondary mt-2">Log Out</a>
        <?php else: ?>
            <h3>Join the Retro Community!</h3>
            <a href="register.php" class="btn btn-primary btn-lg mt-3">Sign Up</a>
            <a href="login.php" class="btn btn-success btn-lg mt-3 ms-2">Log In</a>
        <?php endif; ?>
    </div>
    <footer class="text-center mt-5 py-3">
        <p>&copy; <?php echo date('Y'); ?> Retro Game Collection. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>