<?php
session_start();
$database = new SQLite3('museum.db');

$user_id = $_SESSION['user_id'] ?? 0;

if ($user_id == 0) {
    echo json_encode(["error" => "User not logged in."]);
    exit;
}

$stmt = $database->prepare("SELECT name, username, email, profile_pic FROM users WHERE id = ?");
$stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
$result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if ($result) {
    echo json_encode($result);
} else {
    echo json_encode(["error" => "User not found."]);
}
?>
