<?php
session_start();
$database = new SQLite3('museum.db');

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    die("Unauthorized.");
}


$stmt = $database->prepare("UPDATE users SET mute = '1' WHERE username = ?");
$stmt->bindValue(1, $_POST['username'], SQLITE3_TEXT);
$stmt->execute();

if ($_SESSION['role'] === 'admin') {
    header("Location: MainMenu_Admin.php");
} else {
    header("Location: MainMenu_Staff.php");
}
exit();
?>
