<?php
session_start();
$database = new SQLite3('museum.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'delete' && isset($_POST['comment_id'])) {
        $comment_id = $_POST['comment_id'];
        $stmt = $database->prepare("DELETE FROM comments WHERE id = :comment_id");
        $stmt->bindValue(':comment_id', $comment_id, SQLITE3_INTEGER);
        $stmt->execute();
        echo "Comment deleted.";
    }

    if ($action === 'mute' && isset($_POST['user_id']) && isset($_POST['mute'])) {
        $user_id = $_POST['user_id'];
        $mute_status = $_POST['mute'];
        $stmt = $database->prepare("UPDATE users SET mute = :mute WHERE id = :user_id");
        $stmt->bindValue(':mute', $mute_status, SQLITE3_INTEGER);
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmt->execute();
        echo ($mute_status == 1) ? "User muted." : "User unmuted.";
    }
}
?>
