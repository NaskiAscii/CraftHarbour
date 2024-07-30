<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';

requireLogin();

$server_id = $_GET['id'];

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

$logs = getServerLogs($server_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Logs - Minecraft Server Hosting</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Server Logs: <?php echo htmlspecialchars($server['name']); ?></h1>
    </header>
    <main>
        <pre><?php echo htmlspecialchars($logs); ?></pre>
        <a href="manage_server.php?id=<?php echo $server_id; ?>">Back to Server Management</a>
    </main>
</body>
</html>
