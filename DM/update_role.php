<?php
$database = new SQLite3('museum.db');

$id = $_POST['id'] ?? 0;
$newRole = $_POST['role'] ?? '';

if ($id && in_array($newRole, ['admin', 'staff', 'user'])) {
    $stmt = $database->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bindValue(1, $newRole, SQLITE3_TEXT);
    $stmt->bindValue(2, $id, SQLITE3_INTEGER);
    $stmt->execute();
    echo "User role updated successfully.";
} else {
    echo "Invalid role selected.";
}
?>
