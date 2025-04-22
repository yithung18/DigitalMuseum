<?php
$database = new SQLite3('museum.db');

$id = $_POST['id'] ?? 0;

if ($id) {
    $stmt = $database->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bindValue(1, $id, SQLITE3_INTEGER);
    $stmt->execute();
    echo "User deleted successfully.";
} else {
    echo "Invalid user ID.";
}
?>
