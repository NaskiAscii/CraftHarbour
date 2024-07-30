<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';

requireLogin();

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM servers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$servers = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Minecraft Server Hosting</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Dashboard</h1>
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <h2>My Servers</h2>
        <?php if (empty($servers)): ?>
            <p>You don't have any servers yet.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($servers as $server): ?>
                    <li>
                        <?php echo htmlspecialchars($server['name']); ?> -
                        Status: <?php echo $server['status']; ?>
                        <a href="manage_server.php?id=<?php echo $server['id']; ?>">Manage</a>
                        <a href="view_logs.php?id=<?php echo $server['id']; ?>">View Logs</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a href="create_server.php" class="button">Create New Server</a>
    </main>
</body>
</html>
