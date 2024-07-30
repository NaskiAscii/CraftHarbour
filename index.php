<?php
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minecraft Server Hosting</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Minecraft Server Hosting</h1>
        <nav>
            <?php if (isLoggedIn()): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <h2>Welcome to Minecraft Server Hosting</h2>
        <p>Create and manage your own Minecraft servers with ease!</p>
    </main>
</body>
</html>
