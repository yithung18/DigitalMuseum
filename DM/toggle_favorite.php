<?php
session_start();
$database = new SQLite3('museum.db');

$user_id = $_SESSION['user_id'] ?? 0;
$item_id = $_POST['item_id'] ?? 0;

if ($user_id == 0 || $item_id == 0) {
    echo json_encode(["error" => "Invalid request."]);
    exit;
}

// Check if item is already a favorite
$check = $database->prepare("SELECT COUNT(*) as count FROM favorites WHERE user_id = ? AND item_id = ?");
$check->bindValue(1, $user_id, SQLITE3_INTEGER);
$check->bindValue(2, $item_id, SQLITE3_INTEGER);
$isFavorite = $check->execute()->fetchArray(SQLITE3_ASSOC)['count'] > 0;

if ($isFavorite) {
    // Remove from favorites
    $stmt = $database->prepare("DELETE FROM favorites WHERE user_id = ? AND item_id = ?");
    $stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(2, $item_id, SQLITE3_INTEGER);
    $stmt->execute();
    echo json_encode(["status" => "removed"]);
} else {
    // Add to favorites
    $stmt = $database->prepare("INSERT INTO favorites (user_id, item_id) VALUES (?, ?)");
    $stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(2, $item_id, SQLITE3_INTEGER);
    $stmt->execute();
    echo json_encode(["status" => "added"]);
}
?>
