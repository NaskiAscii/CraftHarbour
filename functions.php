<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function createMinecraftServer($name, $user_id) {
    global $conn;

    $server_dir = MINECRAFT_DIR . $name;
    if (!file_exists($server_dir)) {
        mkdir($server_dir, 0755, true);
    }

    // Download Minecraft server JAR (you'd need to update this URL periodically)
    $jar_url = "https://launcher.mojang.com/v1/objects/1b557e7b033b583cd9f66746b7a9ab1ec1673ced/server.jar";
    file_put_contents($server_dir . "/server.jar", file_get_contents($jar_url));

    // Create server.properties file
    $properties = "server-port=25565\nmotd=A Minecraft Server\n";
    file_put_contents($server_dir . "/server.properties", $properties);

    // Insert server info into database
    $stmt = $conn->prepare("INSERT INTO servers (name, user_id, status) VALUES (?, ?, 'Stopped')");
    $stmt->bind_param("si", $name, $user_id);
    $stmt->execute();
    $stmt->close();
}

function startServer($server_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT name FROM servers WHERE id = ?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $server = $result->fetch_assoc();
    $stmt->close();

    $server_dir = MINECRAFT_DIR . $server['name'];
    exec("screen -dmS mc_" . $server_id . " java -Xmx1024M -Xms1024M -jar " . $server_dir . "/server.jar nogui");

    $stmt = $conn->prepare("UPDATE servers SET status = 'Running' WHERE id = ?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $stmt->close();
}

function stopServer($server_id) {
    global $conn;

    exec("screen -S mc_" . $server_id . " -X stuff 'stop\n'");

    $stmt = $conn->prepare("UPDATE servers SET status = 'Stopped' WHERE id = ?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $stmt->close();
}

function deleteServer($server_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT name FROM servers WHERE id = ?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $server = $result->fetch_assoc();
    $stmt->close();

    $server_dir = MINECRAFT_DIR . $server['name'];
    exec("rm -rf " . $server_dir);

    $stmt = $conn->prepare("DELETE FROM servers WHERE id = ?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $stmt->close();
}

function getServerLogs($server_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT name FROM servers WHERE id = ?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $server = $result->fetch_assoc();
    $stmt->close();

    $log_file = MINECRAFT_DIR . $server['name'] . "/logs/latest.log";
    if (file_exists($log_file)) {
        return file_get_contents($log_file);
    } else {
        return "No logs available.";
    }
}
?>
