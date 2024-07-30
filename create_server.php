<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $user_id = $_SESSION['user_id'];

    createMinecraftServer($name, $user_id);

    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Server - Minecraft Server Hosting</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Create New Server</h1>
    </header>
    <main>
        <form method="POST">
            <label for="name">Server Name:</label>
            <input type="text" id="name" name="name" required>

            <button type="submit">Create Server</button>
        </form>
    </main>
</body>
</html>
