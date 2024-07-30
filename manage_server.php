<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';

requireLogin();

$server_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'start':
            startServer($server_id);
            break;
        case 'stop':
            stopServer($server_id);
            break;
        case 'delete':
            deleteServer($server_id);
            header("Location: dashboard.php");
            exit();
    }
}

$stmt = $conn->prepare("SELECT * FROM servers WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $server_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$server = $result->fetch_assoc();
$stmt->close();

if (!$server) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Server - Minecraft Server Hosting</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Manage Server: <?php echo htmlspecialchars($server['name']); ?></h1>
    </header>
    <main>
        <p>Status: <?php echo $server['status']; ?></p>
        <form method="POST">
            <?php if ($server['status'] == 'Stopped'): ?>
                <button type="submit" name="action" value="start">Start Server</button>
            <?php else: ?>
                <button type="submit" name="action" value="stop">Stop Server</button>
            <?php endif; ?>
            <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this server?')">Delete Server</button>
        </form>
        <a href="view_logs.php?id=<?php echo $server_id; ?>">View Logs</a>
        <a href="dashboard.php">Back to Dashboard</a>
    </main>
</body>
</html>
