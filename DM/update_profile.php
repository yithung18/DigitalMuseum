<?php
session_start();
$database = new SQLite3('museum.db');

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id == 0) {
    echo "Error: User not logged in.";
    exit;
}

$name = $_POST['name'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Hash password if changed
if (!empty($password)) {
    $password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $database->prepare("UPDATE users SET name = ?, username = ?, email = ?, password = ? WHERE id = ?");
    $stmt->bindValue(1, $name, SQLITE3_TEXT);
    $stmt->bindValue(2, $username, SQLITE3_TEXT);
    $stmt->bindValue(3, $email, SQLITE3_TEXT);
    $stmt->bindValue(4, $password, SQLITE3_TEXT);
    $stmt->bindValue(5, $user_id, SQLITE3_INTEGER);
} else {
    $stmt = $database->prepare("UPDATE users SET name = ?, username = ?, email = ? WHERE id = ?");
    $stmt->bindValue(1, $name, SQLITE3_TEXT);
    $stmt->bindValue(2, $username, SQLITE3_TEXT);
    $stmt->bindValue(3, $email, SQLITE3_TEXT);
    $stmt->bindValue(4, $user_id, SQLITE3_INTEGER);
}

if ($stmt->execute()) {
    echo "Profile updated successfully.";
} else {
    echo "Error updating profile.";
}
?>
