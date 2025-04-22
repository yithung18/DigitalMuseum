<?php
session_start();
$database = new SQLite3('museum.db');

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    die("Unauthorized.");
}


$stmt = $database->prepare("DELETE FROM comments WHERE id = ?");
$stmt->bindValue(1, $_POST['comment_id'], SQLITE3_INTEGER);
$stmt->execute();

if ($_SESSION['role'] === 'admin') {
    header("Location: MainMenu_Admin.php");
} else {
    header("Location: MainMenu_Staff.php");
}
exit();
?>
