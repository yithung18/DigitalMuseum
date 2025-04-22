<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$database = new SQLite3('museum.db');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_id = $_POST['item_id'] ?? 0;
    $user_id = $_SESSION['user_id'] ?? 0;
    $comment = trim($_POST['comment'] ?? '');

    if ($user_id == 0) {
        echo "Error: You must be logged in to comment.";
        exit;
    }

    // Check if user is muted
    $muteCheck = $database->prepare("SELECT mute FROM users WHERE id = ?");
    $muteCheck->bindValue(1, $user_id, SQLITE3_INTEGER);
    $result = $muteCheck->execute()->fetchArray(SQLITE3_ASSOC);

    if ($result && $result['mute'] == 1) {
        echo "Error: You are muted and cannot comment.";
        exit;
    }

    if (!empty($comment)) {
        $stmt = $database->prepare("INSERT INTO comments (item_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bindValue(1, $item_id, SQLITE3_INTEGER);
        $stmt->bindValue(2, $user_id, SQLITE3_INTEGER);
        $stmt->bindValue(3, $comment, SQLITE3_TEXT);
        $stmt->execute();
    }
}

// Reload updated comments
include 'fetch_comments.php';
?>

